<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IntroController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PostCategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\SlugController;
use UniSharp\LaravelFilemanager\Lfm;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomerWelcomeEmail;

/*Route::get('/send-welcome-email', function () {
    $testRecipient = 'quanganhbn168@gmail.com';
    $testCustomerName = 'Khách Hàng VIP';

    try {
        Mail::to($testRecipient)->send(new CustomerWelcomeEmail($testCustomerName));
        return "Email chào mừng đã được gửi thành công tới: " . $testRecipient;
    } catch (\Exception $e) {
        // Hiển thị lỗi nếu có vấn đề
        return "Gửi mail thất bại. Lỗi: " . $e->getMessage();
    }
});*/

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['auth:admin']], function () {
    Lfm::routes();
});
Route::get("/", [HomeController::class,"index"])->name("home");
Route::middleware('throttle:30,1')->group(function () {
    Route::get('/o/{code}', [TrackingController::class, 'showByCode'])->name('warranty.code');
    Route::get('/qr/o/{code}.png', [TrackingController::class, 'qrByCode'])->name('warranty.code.qr');
});

Route::get('/tra-cuu-bao-hanh', [TrackingController::class, 'index'])->name('order.tracking');
Route::group(['prefix'=>'san-pham'], function(){
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/category/{category:slug}', [ProductController::class, 'byCategory'])->name('products.by_category');
    Route::get('/{product:slug}', [ProductController::class, 'show'])->name('frontend.product.show');
});
Route::group(["prefix"=>"danh-muc"], function(){
    Route::get("/{postCategory:slug}",[PostController::class,"postByCate"])->name("frontend.post.postByCate");
    Route::get("chi-tiet/{post:slug}",[PostController::class,"detail"])->name("frontend.post.detail");
});
Route::group(["prefix"=>"dich-vu"], function(){
    Route::get("/danh-muc/{slug}", [ServiceController::class,"serviceByCate"])->name("services.serviceByCate");
    Route::get("/{services:slug}", [ServiceController::class,"detail"])->name("services.show");
});
Route::get('/tim-kiem', [HomeController::class, 'search'])->name('frontend.search');

Route::get('gioi-thieu', [IntroController::class,'show'])->name('intro.show');
Route::get('lien-he',[ContactController::class,'show'])->name('contact.show');
Route::post('lien-he',[ContactController::class,'store'])->name('contact.store');
Route::middleware('auth')->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::put('/update/{cartItemId}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{cartItemId}', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
});

Route::get('/cart', [CartController::class, 'showCartPage'])->name('cart.page');
Route::post('/cart/buy-now', [CartController::class, 'buyNow'])->name('cart.buy_now');
Route::post('/cart/merge', [CartController::class, 'merge']);
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
Route::get('/order-success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::get('thank-you',function(){
    return view('page/thank-you');
})->name('thank-you');
Route::middleware(['auth:web'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    Route::get('/orders', [UserController::class, 'orderHistory'])->name('orders');
    Route::get('/orders/{orderId}', [UserController::class, 'orderDetail'])->name('order.detail');

    Route::get('/wishlist', [UserController::class, 'wishlist'])->name('wishlist');
});

// Route cho hành động thêm/xóa wishlist (có thể đặt ngoài group trên)
Route::post('/wishlist/add/{product}', [WishlistController::class, 'add'])->name('wishlist.add');
Route::post('/wishlist/remove/{product}', [WishlistController::class, 'remove'])->name('wishlist.remove');

require __DIR__.'/admin.php';   
require __DIR__.'/auth.php';   
Route::get('/{slug}', [SlugController::class, 'resolve'])->name('slug.resolve');