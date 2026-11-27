<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Users\LoginController;
use App\Http\Controllers\Admin\Users\UserController;
use App\Http\Controllers\Admin\MainController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\InternalCategoryController;
use App\Http\Controllers\Admin\CartController;
use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\InternalPostController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SharedDbConfigController;
use App\Http\Controllers\Admin\SsoAppController;
use App\Http\Controllers\Admin\SsoSharedTableController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\DuanController;

use App\Http\Controllers\Admin\ProvinceController;
use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\WardController;
use App\Http\Controllers\Admin\StreetController;

use App\Http\Controllers\Admin\UploadController;

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HomeSystemController;
use App\Http\Controllers\SsoController;

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});

Route::get('admin', [LoginController::class, 'index'])->name('login');
Route::post('admin', [LoginController::class, 'store']);
Route::get('logout', [LoginController::class, 'logout'])->name('logout');

// Centralized SSO Portal
Route::prefix('sso')->name('sso.')->group(function () {
    Route::get('login', [SsoController::class, 'showLogin'])->name('login');
    Route::post('login', [SsoController::class, 'login'])->name('login.submit');
    Route::get('google', [SsoController::class, 'redirectToGoogle'])->name('google');
    Route::get('profile', [SsoController::class, 'showProfile'])->name('profile');
    Route::post('profile', [SsoController::class, 'updateProfile'])->name('profile.update');
    Route::get('apps', [SsoController::class, 'apps'])->name('apps');
    Route::get('launch/{app}', [SsoController::class, 'launch'])->name('launch');
    Route::get('local-login', [SsoController::class, 'localLogin'])->name('local-login');
    Route::get('logout', [SsoController::class, 'logout'])->name('logout');
});
Route::get('auth/google/callback', [SsoController::class, 'handleGoogleCallback'])->name('sso.google.callback');

Route::post('/upload', [UploadController::class, 'upload'])->name('upload');

Route::get('admin/get-section', function () {
    return view('admin.post.add_section')->render();
});

// ajax
Route::group(['prefix' => 'ajax'], function () {
    Route::get('change_province/{id}', [AjaxController::class, 'change_province']);
    Route::get('change_district/{id}', [AjaxController::class, 'change_district']);
    Route::get('change_district_street/{id}', [AjaxController::class, 'change_district_street']);
    Route::get('change_SortBy/{id}', [AjaxController::class, 'change_SortBy']);
    Route::get('change_parent/{id}', [AjaxController::class, 'change_parent']);
    Route::get('update_category_view/{id}/{view}', [AjaxController::class, 'update_category_view']);
    Route::get('update_menu_view/{id}/{view}', [AjaxController::class, 'update_menu_view']);
    Route::get('del_img_detail/{id}', [AjaxController::class, 'del_img_detail']);
    Route::get('name_img_detail/{id}/{name}', [AjaxController::class, 'name_img_detail']);
    Route::get('del_section/{id}', [AjaxController::class, 'del_section']);
    Route::get('update_status_category/{id}/{status}', [AjaxController::class, 'update_status_category']);
    Route::get('update_status_post/{id}/{status}', [AjaxController::class, 'update_status_post']);
    Route::get('update_status_province/{id}/{status}', [AjaxController::class, 'update_status_province']);
    Route::get('update_home_province/{id}/{status}', [AjaxController::class, 'update_home_province']);
    Route::get('update_hot_post/{id}/{hot}', [AjaxController::class, 'update_hot_post']);
    Route::get('change_category/{id}', [AjaxController::class, 'change_category']);
    Route::get('change_arrange_mat/{id}', [AjaxController::class, 'change_arrange_mat']);
    Route::get('change_arrange_day/{id}', [AjaxController::class, 'change_arrange_day']);
    Route::get('change_arrange_khoa/{id}', [AjaxController::class, 'change_arrange_khoa']);
    Route::get('change_arrange_cat/{id}/{catid}', [AjaxController::class, 'change_arrange_cat']);
});

Route::prefix('admin')->group(function () {
    Route::middleware(['admin:1'])->group(function () {
        // user
        Route::resource('users', UserController::class);
        Route::get('users/member/list', [UserController::class, 'member'])->name('users.member');
        Route::post('user/change-status', [UserController::class, 'changeStatus'])->name('user.changeStatus');
        Route::post('users/update-name', [UserController::class, 'updateName'])->name('users.updateName');
        Route::post('user/update-work-status', [UserController::class, 'updateWorkStatus'])->name('admin.user.updateWorkStatus');

        // khách hàng
        Route::resource('customer', CustomerController::class);
        Route::post('customer/bulk-delete', [CustomerController::class, 'bulkDelete'])->name('customer.bulkDelete');
    });

    Route::middleware(['admin:2'])->group(function () {
        // cấu hình hệ thống
        Route::resource('setting', SettingController::class);
        Route::get('sso-apps', [SsoAppController::class, 'index'])->name('admin.sso-apps.index');
        Route::post('sso-apps/update-all', [SsoAppController::class, 'updateAll'])->name('admin.sso-apps.update-all');
        Route::post('sso-apps', [SsoAppController::class, 'store'])->name('admin.sso-apps.store');
        Route::delete('sso-apps/{id}', [SsoAppController::class, 'destroy'])->name('admin.sso-apps.destroy');
        Route::get('sso-shared-tables', [SsoSharedTableController::class, 'index'])->name('admin.sso-shared-tables.index');
        Route::post('sso-shared-tables/update-all', [SsoSharedTableController::class, 'updateAll'])->name('admin.sso-shared-tables.update-all');
        Route::post('sso-shared-tables', [SsoSharedTableController::class, 'store'])->name('admin.sso-shared-tables.store');
        Route::delete('sso-shared-tables/{id}', [SsoSharedTableController::class, 'destroy'])->name('admin.sso-shared-tables.destroy');

        // Cấu hình DB & Bảng dùng chung (giống web learning)
        Route::post('shared-db-configs/test-connection', [SharedDbConfigController::class, 'testConnection'])->name('admin.shared-db-configs.test-connection');
        Route::patch('shared-db-configs/{sharedDbConfig}/toggle-status', [SharedDbConfigController::class, 'toggleStatus'])->name('admin.shared-db-configs.toggle-status');
        Route::resource('shared-db-configs', SharedDbConfigController::class)->names('admin.shared-db-configs');
        Route::resource('menu', MenuController::class);
        Route::resource('category', CategoryController::class);
        Route::resource('province', ProvinceController::class);
        Route::resource('district', DistrictController::class);
        Route::resource('ward', WardController::class);
        Route::resource('street', StreetController::class);
        Route::resource('slider', SliderController::class);

        Route::resource('internalcategory', InternalCategoryController::class);
        Route::resource('internalpost', InternalPostController::class);

        Route::resource('option', OptionController::class);
        Route::get('option/double/{id}', [OptionController::class, 'double']);

        // dự án (dùng chung)
        Route::resource('duan', DuanController::class);
        Route::post('duan/update-rate', [DuanController::class, 'updateRate'])->name('duan.updateRate');
        Route::post('duan/{id}/update-name', [DuanController::class, 'updateName'])->name('duan.updateName');
    });

    Route::middleware(['admin:3'])->group(function () {
        Route::get('main', [MainController::class, 'index'])->name('admin');
        Route::resource('post', PostController::class);
        Route::post('post/quick-create', [PostController::class, 'quickCreate'])->name('post.quickCreate');
        Route::get('post/search-projects', [PostController::class, 'searchProjects'])->name('post.searchProjects');
        Route::post('post/upfile', [PostController::class, 'upfile'])->name('post.upfile');

        Route::resource('news', NewsController::class);
        Route::get('post/post_up/{id}', [PostController::class, 'post_up'])->name('post_up');
        Route::group(['prefix' => 'section'], function () {
            Route::get('index/{pid}', [SectionController::class, 'index']);
        });
    });
});

// home view
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('sitemap.xml', [HomeController::class, 'sitemap'])->name('pages.sitemap');

// home system
Route::get('sendmail', [HomeSystemController::class, 'sendmail'])->name('sendmail');
Route::post('question', [HomeSystemController::class, 'question'])->name('question');

// add to cart
Route::prefix('product')->group(function () {
    Route::get('add-to-cart/{id}', [HomeController::class, 'addTocart'])->name('addTocart');
    Route::get('addtocart_munti', [HomeController::class, 'addTocart_munti'])->name('addTocart_munti');
    Route::get('showCart', [HomeController::class, 'showCart'])->name('showCart');
    Route::POST('updateCart', [HomeController::class, 'updateCart'])->name('updateCart');
    Route::get('delCart', [HomeController::class, 'delCart'])->name('delCart');
    Route::get('checkout', [HomeController::class, 'checkout'])->name('checkout');
    Route::get('get_checkout', [HomeController::class, 'checkout'])->name('get_checkout');
    Route::POST('order', [HomeController::class, 'order'])->name('order');
});

Route::get('location/{slug}', [HomeController::class, 'province']);
Route::get('{slug}', [HomeController::class, 'category']);
Route::get('{catslug}/{slug}', [HomeController::class, 'post']);
