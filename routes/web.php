<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Frontend\IntroController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\ProjectController;
use App\Http\Controllers\Frontend\ServiceController;
use App\Http\Controllers\Frontend\SlugController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Frontend\CareerController;
use App\Http\Controllers\Frontend\ConsultingController;
use App\Http\Controllers\Frontend\AgencyController;
use App\Http\Controllers\Frontend\CommentController;

use App\Http\Controllers\Frontend\TourController;

Route::get("/", [HomeController::class, "index"])->name("home");

Route::group(['prefix' => 'tour'], function () {
    Route::get('/', [TourController::class, 'index'])->name('frontend.tours.index');
    Route::get('/{slug}', [TourController::class, 'category'])->name('frontend.tours.category');
    Route::get('/{categorySlug}/{slug}', [TourController::class, 'show'])->name('frontend.tours.show');
});

Route::group(['prefix' => 'san-pham'], function () {
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/tim-kiem', [ProductController::class, 'search'])->name('frontend.products.search');
    Route::get('/{slug}', [ProductController::class, 'productBySlug'])->name('frontend.product.bySlug');
});
Route::get('/danh-muc-san-pham/{slug}', [ProductController::class, 'categoryBySlug'])->name('frontend.product-category.bySlug');

Route::get('/tin-tuc', [PostController::class, 'index'])->name('frontend.posts.index');
Route::get('/tin-tuc/{slug}', [PostController::class, 'postBySlug'])->name('frontend.post.bySlug');
Route::get('/danh-muc-tin-tuc/{slug}', [PostController::class, 'categoryBySlug'])->name('frontend.post-category.bySlug');

Route::get('/dich-vu', [ServiceController::class, 'index'])->name('frontend.services.index');
Route::get('/dich-vu/{slug}', [ServiceController::class, 'serviceBySlug'])->name('frontend.service.bySlug');
Route::get('/danh-muc-dich-vu/{slug}', [ServiceController::class, 'categoryBySlug'])->name('frontend.service-category.bySlug');

Route::get('/du-an', [ProjectController::class, 'index'])->name('frontend.projects.index');
Route::get('/du-an/{slug}', [ProjectController::class, 'projectBySlug'])->name('frontend.project.bySlug');
Route::get('/danh-muc-du-an/{slug}', [ProjectController::class, 'categoryBySlug'])->name('frontend.project-category.bySlug');

Route::get('/tim-kiem', [HomeController::class, 'search'])->name('frontend.search');
Route::post('/tim-kiem', [HomeController::class, 'postSearch'])->name('frontend.search.post');

Route::get('/ve-chung-toi', [IntroController::class, 'index'])->name('frontend.intro.index');
Route::get('lien-he', [ContactController::class, 'show'])->name('contact.show');
Route::post('lien-he', [ContactController::class, 'store'])->name('contact.store');

// tư vấn triển khai
Route::get('tu-van-trien-khai', [ConsultingController::class, 'index'])->name('consulting.index');
Route::post('tu-van-trien-khai', [ConsultingController::class, 'store'])->name('consulting.store');

// đại lý
Route::get('dai-ly', [AgencyController::class, 'index'])->name('agency.index');
Route::post('dai-ly', [AgencyController::class, 'store'])->name('agency.store');
Route::get('/tuyen-dung', [CareerController::class, 'index'])->name('frontend.careers.index');
Route::get('/tuyen-dung/{career:slug}', [CareerController::class, 'show'])->name('frontend.careers.show');
Route::post('/tuyen-dung/{id}/nop-don', [CareerController::class, 'apply'])->name('frontend.careers.apply');

// Bnh lun (Comments)
Route::post('/binh-luan', [CommentController::class, 'store'])->name('comments.store');

Route::get('thank-you', function () {
    return view('page/thank-you');
})->name('thank-you');
Route::middleware(['auth:web'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

});
// ===================== CATCH-ALL (301 redirect cho URL c) =====================
Route::get('/{slug}', [SlugController::class, 'handle'])->where('slug', '.*')->name('frontend.slug.handle');
