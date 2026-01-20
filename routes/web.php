<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Frontend\IntroController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\ProjectController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Frontend\ServiceController;
use App\Http\Controllers\Frontend\FieldController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\Frontend\SlugController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\PageContentController;
use App\Http\Controllers\Frontend\CareerController;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomerWelcomeEmail;
use App\Http\Controllers\MediaLibraryController;

Route::middleware(['web'])->group(function () {
    Route::get('/media-lib', [MediaLibraryController::class, 'index'])->name('media.lib.index');
    Route::post('/media-lib/upload', [MediaLibraryController::class, 'upload'])->name('media.lib.upload');
    Route::delete('/media-lib/delete', [MediaLibraryController::class, 'destroy'])->name('media.lib.delete');
    Route::post('/media-lib/sync', [MediaLibraryController::class, 'sync'])->name('media.lib.sync');
});

Route::get("/", [HomeController::class,"index"])->name("home");
Route::middleware('throttle:30,1')->group(function () {
    Route::get('/o/{code}', [TrackingController::class, 'showByCode'])->name('warranty.code');
    Route::get('/qr/o/{code}.png', [TrackingController::class, 'qrByCode'])->name('warranty.code.qr');
});

Route::get('/tra-cuu-bao-hanh', \App\Livewire\Public\WarrantySearch::class)->name('warranty.search');

Route::group(['prefix'=>'san-pham'], function(){
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/category/{category:slug}', [ProductController::class, 'byCategory'])->name('products.byCategory');
    Route::get('/tim-kiem', [ProductController::class, 'search'])->name('frontend.products.search');
});

Route::get('/tin-tuc', [PostController::class, 'index'])->name('frontend.posts.index');
Route::get('/dich-vu', [ServiceController::class, 'index'])->name('frontend.services.index');
Route::get('/linh-vuc', [FieldController::class, 'index'])->name('frontend.fields.index');
Route::get('/du-an', [ProjectController::class, 'index'])->name('frontend.projects.index');

Route::get('/tim-kiem', [HomeController::class, 'search'])->name('frontend.search');

Route::get('/gioi-thieu', [IntroController::class,'index'])->name('frontend.intro.index');
Route::get('/gioi-thieu/{intro:slug}', [IntroController::class,'getBySlug'])->name('frontend.intro.getBySlug');
Route::get('lien-he',[ContactController::class,'show'])->name('contact.show');
Route::post('lien-he',[ContactController::class,'store'])->name('contact.store');

// Tư vấn triển khai
Route::get('tu-van-trien-khai', [App\Http\Controllers\Frontend\ConsultingController::class, 'index'])->name('consulting.index');
Route::post('tu-van-trien-khai', [App\Http\Controllers\Frontend\ConsultingController::class, 'store'])->name('consulting.store');

// Đại lý
Route::get('dai-ly', [App\Http\Controllers\Frontend\AgencyController::class, 'index'])->name('agency.index');
Route::post('dai-ly', [App\Http\Controllers\Frontend\AgencyController::class, 'store'])->name('agency.store');
Route::middleware('auth')->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::put('/update/{cartItemId}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{cartItemId}', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
});
Route::get('/tuyen-dung', [CareerController::class, 'index'])->name('frontend.careers.index');
Route::get('/tuyen-dung/{career:slug}', [CareerController::class, 'show'])->name('frontend.careers.show');
Route::post('/tuyen-dung/{id}/nop-don', [CareerController::class, 'apply'])->name('frontend.careers.apply');
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
Route::middleware(['auth'])->group(function () {
    Route::post('/page-content/update', [PageContentController::class, 'update'])->name('page-content.update');
});
// Route cho hành động thêm/xóa wishlist (có thể đặt ngoài group trên)
Route::post('/wishlist/add/{product}', [WishlistController::class, 'add'])->name('wishlist.add');
Route::post('/wishlist/remove/{product}', [WishlistController::class, 'remove'])->name('wishlist.remove');

require __DIR__.'/admin.php';
require __DIR__.'/worker.php';
require __DIR__.'/auth.php';
Route::get('/{slug}', [SlugController::class, 'handle'])->where('slug', '.*')->name('frontend.slug.handle');
