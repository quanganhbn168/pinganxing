<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Frontend\IntroController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\ProjectController;
use App\Http\Controllers\Frontend\ServiceController;
use App\Http\Controllers\Frontend\FieldController;
use App\Http\Controllers\Frontend\SlugController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Frontend\CareerController;
use App\Http\Controllers\Frontend\ConsultingController;
use App\Http\Controllers\Frontend\AgencyController;
use App\Http\Controllers\Frontend\CommentController;

Route::get("/", [HomeController::class, "index"])->name("home");

Route::group(['prefix' => 'san-pham'], function () {
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/tim-kiem', [ProductController::class, 'search'])->name('frontend.products.search');
    Route::get('/{slug}', [ProductController::class, 'resolveBySlug'])->name('frontend.product.bySlug');
});

Route::get('/tin-tuc', [PostController::class, 'index'])->name('frontend.posts.index');
Route::get('/tin-tuc/{slug}', [PostController::class, 'resolveBySlug'])->name('frontend.post.bySlug');

Route::get('/dich-vu', [ServiceController::class, 'index'])->name('frontend.services.index');
Route::get('/dich-vu/{slug}', [ServiceController::class, 'resolveBySlug'])->name('frontend.service.bySlug');

Route::get('/linh-vuc', [FieldController::class, 'index'])->name('frontend.fields.index');
Route::get('/linh-vuc/{slug}', [FieldController::class, 'resolveBySlug'])->name('frontend.field.bySlug');

Route::get('/du-an', [ProjectController::class, 'index'])->name('frontend.projects.index');
Route::get('/du-an/{slug}', [ProjectController::class, 'resolveBySlug'])->name('frontend.project.bySlug');

Route::get('/tim-kiem', [HomeController::class, 'search'])->name('frontend.search');

Route::get('/ve-chung-toi', [IntroController::class, 'index'])->name('frontend.intro.index');
Route::get('lien-he', [ContactController::class, 'show'])->name('contact.show');
Route::post('lien-he', [ContactController::class, 'store'])->name('contact.store');

// Tư vấn triển khai
Route::get('tu-van-trien-khai', [ConsultingController::class, 'index'])->name('consulting.index');
Route::post('tu-van-trien-khai', [ConsultingController::class, 'store'])->name('consulting.store');

// Đại lý
Route::get('dai-ly', [AgencyController::class, 'index'])->name('agency.index');
Route::post('dai-ly', [AgencyController::class, 'store'])->name('agency.store');
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

// Bình luận (Comments)
Route::post('/binh-luan', [CommentController::class, 'store'])->name('comments.store');

Route::get('/gio-hang', [CartController::class, 'showCartPage'])->name('cart.page');
Route::post('/gio-hang/buy-now', [CartController::class, 'buyNow'])->name('cart.buy_now');
Route::post('/gio-hang/merge', [CartController::class, 'merge']);
Route::get('/thanh-toan', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/thanh-toan', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
Route::get('/order-success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::get('thank-you', function () {
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
// Route::post('/wishlist/add/{product}', [WishlistController::class, 'add'])->name('wishlist.add');
// Route::post('/wishlist/remove/{product}', [WishlistController::class, 'remove'])->name('wishlist.remove');

// ===================== CATCH-ALL (301 redirect cho URL cũ) =====================
Route::get('/{slug}', [SlugController::class, 'handle'])->where('slug', '.*')->name('frontend.slug.handle');
