<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
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
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\PromotionController;

use App\Http\Controllers\Admin\ProvinceController;
use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\WardController;
use App\Http\Controllers\Admin\StreetController;

use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Auth\GoogleController;

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HomeSystemController;

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
     \UniSharp\LaravelFilemanager\Lfm::routes();
 });

Route::get('admin', [LoginController::class, 'index'])->name('login');
Route::post('admin', [LoginController::class, 'store']);
Route::get('logout', [LoginController::class, 'logout'])->name('logout');
Route::post('account/register', [LoginController::class, 'register'])->name('register');

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

Route::post('/upload', [UploadController::class, 'upload'])->name('upload');

Route::get('admin/get-section', function () {
    return view('admin.post.add_section')->render();
});


// ajax
Route::group(['prefix'=>'ajax'],function(){
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




Route::middleware(['admin'])->group(function () {
    Route::prefix('admin')->group(function () {
        // main
        Route::get('main', [MainController::class, 'index'])->name('admin');

        Route::resource('province',ProvinceController::class);
        Route::resource('district',DistrictController::class);
        Route::resource('ward',WardController::class);
        Route::resource('street',StreetController::class);

        Route::resource('menu',MenuController::class);
        Route::resource('category',CategoryController::class);

        Route::resource('internalcategory',InternalCategoryController::class);
        Route::resource('internalpost',InternalPostController::class);
        
        Route::resource('cart',CartController::class);

        Route::resource('option',OptionController::class);
        Route::get('option/double/{id}', [OptionController::class, 'double']);

        Route::resource('post',PostController::class);
        Route::resource('news',NewsController::class);
        Route::get('post/post_up/{id}', [PostController::class, 'post_up'])->name('post_up');

        Route::resource('product',ProductController::class);
        Route::resource('customer',CustomerController::class);
        Route::resource('promotion',PromotionController::class);

        Route::resource('setting',SettingController::class);
        Route::resource('slider',SliderController::class);

        Route::resource('users',UserController::class);

        Route::group(['prefix'=>'section'],function(){
            Route::get('index/{pid}', [SectionController::class, 'index']);
        });
    });
});

// account
Route::get('dangnhap', [AccountController::class, 'dangnhap'])->name('dangnhap');
Route::middleware(['user'])->group(function () {
    Route::prefix('account')->group(function () {
        Route::get('main', [AccountController::class, 'index'])->name('account.main');
        
    });
});

// Route::prefix('account')->group(function () {
//     Route::get('info', [HomeController::class, 'account'])->name('account');
//     Route::POST('update/{id}', [HomeController::class, 'update_account'])->name('update_account'); // cập nhật thông tin người dùng
//     Route::get('order', [HomeController::class, 'account_cart'])->name('account_cart');
//     Route::get('order/{id}', [HomeController::class, 'account_order_dital'])->name('account_order_dital');
// });

// home view
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');
// Route::get('search', [HomeController::class, 'search'])->name('search');

// home system
Route::get('sendmail', [HomeSystemController::class, 'sendmail'])->name('sendmail');
Route::post('question', [HomeSystemController::class, 'question'])->name('question');
// Route::get('seach/filter/posts', [HomeSystemController::class, 'filterPosts'])->name('posts.filter');

// add to cart
Route::prefix('product')->group(function () {
    Route::get('add-to-cart/{id}', [HomeController::class, 'addTocart'])->name('addTocart'); // thêm sản phẩm vào giỏ hàng
    Route::get('addtocart_munti', [HomeController::class, 'addTocart_munti'])->name('addTocart_munti'); // thêm sản phẩm vào giỏ hàng
    Route::get('showCart', [HomeController::class, 'showCart'])->name('showCart'); // show giỏ hàng
    Route::POST('updateCart', [HomeController::class, 'updateCart'])->name('updateCart'); // update giỏ hàng
    Route::get('delCart', [HomeController::class, 'delCart'])->name('delCart'); // delete sản phẩm trong giỏ hàng
    Route::get('checkout', [HomeController::class, 'checkout'])->name('checkout'); // thanh toán
    Route::get('get_checkout', [HomeController::class, 'checkout'])->name('get_checkout'); // thanh toán
    Route::POST('order', [HomeController::class, 'order'])->name('order'); // thanh toán
});



Route::get('location/{slug}', [HomeController::class, 'province']);
Route::get('{slug}', [HomeController::class, 'category']);
Route::get('{catslug}/{slug}', [HomeController::class, 'post']);


