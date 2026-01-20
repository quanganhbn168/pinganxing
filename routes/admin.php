<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SelectController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\PostCategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProjectCategoryController;
use App\Http\Controllers\Admin\SlideController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ServiceController;

use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\IntroController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\GlobalBulkActionController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\WarrantyController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeValueController;
use App\Http\Controllers\Admin\DuplicateController;
use App\Http\Controllers\Admin\FieldCategoryController;
use App\Http\Controllers\Admin\FieldController as AdminFieldController;
use App\Http\Controllers\Admin\CareerController;
use App\Http\Controllers\Admin\SlugAjaxController;
use App\Livewire\WorkOrder\CreateWorkOrder;
use App\Livewire\WorkOrder\TaskDetail;
use App\Livewire\Warranty\CreateWarranty;
use App\Livewire\Material\MaterialList;
use App\Livewire\Warranty\WarrantyList;

Route::middleware(['auth:admin'])->prefix('admin')->as('admin.')->group(function () {

     Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
     Route::get('dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
     Route::post('/toggle', [DashboardController::class, 'toggleField'])->name('toggle');
     Route::post('global/bulk-action', [GlobalBulkActionController::class, 'handle'])
         ->name('global.bulk_action');
     // Duyệt nhật ký & Tiền nong
     
     Route::prefix('menus')->name('menus.')->controller(MenuController::class)->group(function () {
        Route::get('/', 'index')->name('index');              // Trang chính
        Route::post('/store', 'store')->name('store');        // Ajax thêm mới
        Route::post('/update-tree', 'updateTree')->name('updateTree'); // Ajax lưu sắp xếp
        Route::delete('/{id}', 'destroy')->name('destroy');   // Ajax xóa
    });
    Route::resource('intros', IntroController::class);
    Route::post('intros/bulk-action', [IntroController::class, 'bulkAction'])
         ->name('intros.bulk_action');
    Route::prefix('ajax')->name('ajax.')->group(function () {
        Route::get('slug/check', [SlugAjaxController::class, 'check'])->name('slug.check');
    });
     Route::middleware(['role:super_admin'])->group(function () {
    
          Route::get('/roles', \App\Livewire\Admin\RoleManager::class)->name('roles.index');
     });
    Route::resource('categories', CategoryController::class);
    Route::post('categories/bulk-action', [CategoryController::class, 'bulkAction'])
         ->name('categories.bulk_action');

    Route::get('products/data', [ProductController::class, 'data'])->name('products.data');
    Route::resource('products', ProductController::class);

    Route::post('products/validate-uniqueness', [ProductController::class, 'validateUniqueness'])->name('products.validate_uniqueness');
    Route::post('/ajax/attributes/{attribute}/values', [AttributeController::class, 'storeValue'])
    ->name('ajax.attributes.values.store');
    Route::post('/bulk-action', [ProductController::class, 'bulkAction'])->name('products.bulk_action');

    Route::resource('services', ServiceController::class);

    Route::resource('project-categories', ProjectCategoryController::class)
    ->names('project-categories')
    ->parameters(['project-categories' => 'project_category']);
    Route::post('project-categories/bulk-action', [ProjectCategoryController::class, 'bulkAction'])
         ->name('project-categories.bulk_action');

    Route::resource('careers', CareerController::class);

    Route::resource('post-categories', PostCategoryController::class);
    Route::post('post-categories/bulk-action', [PostCategoryController::class, 'bulkAction'])
         ->name('post-categories.bulk_action');

    Route::resource('posts', PostController::class);
    Route::post('posts/bulk-action', [PostController::class, 'bulkAction'])
         ->name('posts.bulk_action');
         
    Route::group(['prefix' => 'pages', 'as' => 'pages.'], function () {
        Route::get('/', [PageController::class, 'index'])->name('index');
        Route::put('/{id}', [PageController::class, 'update'])->name('update');
    });

    Route::resource('field-categories', FieldCategoryController::class);
    Route::post('field-categories/bulk-action', [FieldCategoryController::class, 'bulkAction'])
         ->name('field-categories.bulk_action');

    Route::resource('fields', AdminFieldController::class);
    Route::post('field/bulk-action', [FieldCategoryController::class, 'bulkAction'])
         ->name('fields.bulk_action');

    Route::resource('slides', SlideController::class);
    Route::post('slides/bulk-action', [SlideController::class, 'bulkAction'])
         ->name('slides.bulk_action');

    Route::resource('attributes', AttributeController::class);

    Route::resource('attributes.values', AttributeValueController::class)->shallow()->except(['index', 'show']);
    Route::get('select/attributes', [SelectController::class, 'attributes'])->name('select.attributes');
    Route::get('select/categories-by-type', [SelectController::class, 'categoriesByType'])->name('select.categories-by-type');

    Route::post('/slides/{slide}/toggle-status', [SlideController::class, 'toggleStatus'])
      ->name('slides.toggle-status');

    Route::resource('projects', ProjectController::class);
    Route::post('projects/bulk-action', [ProjectController::class, 'bulkAction'])
         ->name('projects.bulk_action');
    Route::post('/projects/{project}/duplicate',[ProjectController::class,'duplicate'])->name('projects.duplicate');
    
    Route::resource('users', UserController::class);

    Route::resource('orders', OrderController::class);
    Route::resource('service_categories', ServiceCategoryController::class);

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::resource('contacts', ContactController::class)->only(['index', 'destroy']);
    Route::resource('teams', TeamController::class);
    Route::resource('testimonials', TestimonialController::class);
    Route::post('testimonials/bulk-action', [TestimonialController::class, 'bulkAction'])
         ->name('testimonials.bulk_action');
    Route::resource('orders', OrderController::class)->only([
        'index', 'show', 'destroy'
    ]);

     Route::resource('brands', BrandController::class)->except('show');
     Route::post('brands/bulk-action', [BrandController::class, 'bulkAction'])
         ->name('brands.bulk_action');
     Route::match(['get', 'post'], 'ajax/brands', [BrandController::class, 'ajax'])->name('ajax.brands');

     Route::resource('tags', TagController::class)->except('show');
     Route::get('ajax/tags', [TagController::class, 'ajax'])->name('ajax.tags');
     Route::get('tag-manager', \App\Livewire\Tag\TagManager::class)->name('tag-manager.index');

     Route::match(['get', 'post'], 'ajax/attributes', [AttributeController::class, 'ajax'])->name('ajax.attributes');
     Route::match(['get', 'post'], 'ajax/attribute-values', [AttributeValueController::class, 'ajax'])->name('ajax.attribute-values');

     Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
     Route::get('/ajax/products/check-code', [ProductController::class, 'checkCodeUniqueness'])->name('ajax.products.check_code');
     Route::resource('branches', BranchController::class);

     Route::post('/duplicate', [DuplicateController::class, 'duplicate'])->name('duplicate');

     Route::get('media', [App\Http\Controllers\Admin\MediaController::class, 'index'])->name('media.index');

    // 2. Agents (Đại lý)
     Route::resource('agents', App\Http\Controllers\Admin\AgentController::class);

    // 3. Careers (Tin tuyển dụng)
     Route::resource('careers', App\Http\Controllers\Admin\CareerController::class);

    // 4. Career Applications (Hồ sơ ứng tuyển - Thường chỉ cần xem và xóa)
     Route::resource('career-applications', \App\Http\Controllers\Admin\CareerApplicationController::class)
         ->except(['create', 'store', 'edit']);
         
    // 5. Consulting Requests (Yêu cầu tư vấn)
     Route::resource('consulting-requests', \App\Http\Controllers\Admin\ConsultingRequestController::class);

     Route::get('/work-orders', \App\Livewire\WorkOrder\WorkOrderList::class)->name('work-orders.index');
     Route::get('/work-orders/create', CreateWorkOrder::class)->name('work-orders.create');
     Route::get('/work-orders/{id}/edit', \App\Livewire\WorkOrder\EditWorkOrder::class)->name('work-orders.edit');
     
     // Redirect cũ my-work-orders -> work-orders (gộp chung)
     Route::get('/my-work-orders', fn() => redirect()->route('admin.work-orders.index'))->name('my-work-orders.index');
     Route::get('/work-orders/{id}', \App\Livewire\WorkOrder\WorkOrderDetail::class)->name('work-orders.show');
     Route::get('/work-orders/{id}/print', [\App\Http\Controllers\PrintController::class, 'printWorkOrder'])->name('work-orders.print');

     Route::get('/tasks/{id}', TaskDetail::class)->name('tasks.detail');

     Route::prefix('customers')->name('customers.')->group(function () {
          Route::get('/', \App\Livewire\Customer\CustomerList::class)->name('index');
          Route::get('/create', \App\Livewire\Customer\CustomerForm::class)->name('create');
          Route::get('/{id}/edit', \App\Livewire\Customer\CustomerForm::class)->name('edit');
          Route::get('/{id}', \App\Livewire\Customer\CustomerDetail::class)->name('show');
     });
     Route::prefix('staff')->name('staff.')->group(function () {
          Route::get('/', \App\Livewire\Admin\StaffList::class)->name('index');
          Route::get('/create', \App\Livewire\Admin\StaffForm::class)->name('create');
          Route::get('/{id}/edit', \App\Livewire\Admin\StaffForm::class)->name('edit');
     });
     Route::get('/materials', MaterialList::class)->name('materials.index');

     Route::get('/warranties', WarrantyList::class)->name('warranty.index');
     Route::get('/warranty/create/{work_order_id}', CreateWarranty::class)->name('warranty.create');
     Route::get('/warranty/check', \App\Livewire\Warranty\WarrantyCheck::class)->name('warranty.check');

     Route::get('/finance', \App\Livewire\Finance\FinanceDashboard::class)->name('finance.index');
     Route::get('/finance/work-order/{id}', \App\Livewire\Finance\WorkOrderFinanceDetail::class)->name('finance.work-order');



     // Returned Materials (Vật tư thu hồi)
     Route::get('/returned-materials', \App\Livewire\Material\ReturnedMaterialList::class)->name('returned-materials.index');

     // Staff Performance (Hiệu suất nhân viên)
     Route::get('/staff/{id}/performance', \App\Livewire\Admin\StaffPerformance::class)->name('staff.performance');

     Route::get('/profile', \App\Livewire\Admin\UserProfile::class)->name('profile');
     Route::get('/notifications', \App\Livewire\Admin\NotificationList::class)->name('notifications.index');

     // Homepage Sections (Quản lý nội dung trang chủ)
     Route::prefix('homepage-sections')->name('homepage-sections.')->controller(\App\Http\Controllers\Admin\HomepageSectionController::class)->group(function () {
         Route::get('/', 'index')->name('index');
         Route::get('/{id}/edit', 'edit')->name('edit');
         Route::put('/{id}', 'update')->name('update');
         Route::post('/{id}/toggle', 'toggleActive')->name('toggle');
         Route::post('/reorder', 'reorder')->name('reorder');
     });
});
