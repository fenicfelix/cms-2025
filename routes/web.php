<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\TagsController;
use App\Http\Controllers\Migration\CIController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\ShowsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VideosController;
use App\Http\Controllers\WidgetsController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatatablesController;
use App\Http\Controllers\UserGroupsController;
use App\Http\Controllers\Migration\WordpressController;
use App\Http\Controllers\ProgramLineupController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', [WordpressController::class, 'index'])->middleware('guest')->name('/');

Route::post('api/hide-breaking-news', [PostsController::class, 'hide_breaking_news'])->name('hide_breaking_news');


Route::middleware(['auth'])->group(
    function () {
        Route::get('/', [DashboardController::class, 'index'])->name('/');
        Route::get('/wp/{type}/{page?}', [WordpressController::class, 'index'])->name('wp');
        Route::get('/ci/{type}/{page?}', [CIController::class, 'index'])->name('ci');

        Route::post('upload-profile-image', [UsersController::class, 'upload_profile_image'])->name('upload_profile_image');

        Route::resource('tags', TagsController::class)->except(['create', 'show', 'edit', 'update']);
        Route::resource('categories', CategoriesController::class)->except(['create', 'show', 'edit', 'update', 'destroy']);
        Route::delete('delete-category', [CategoriesController::class, 'delete_category'])->name('delete_category');
        Route::resource('posts', PostsController::class)->except(["update", "store"]);
        Route::resource('media', MediaController::class)->except(['create', 'store', 'show', 'edit', 'update', 'destroy']);
        Route::post('delete-image', [MediaController::class, 'destroy'])->name('delete_image');
        Route::resource('users', UsersController::class)->except(["destroy"]);
        Route::resource('pages', PagesController::class);
        Route::resource('videos', VideosController::class)->except(['create', 'show', 'edit', 'update']);
        Route::resource('shows', ShowsController::class);

        Route::post('update-user-group', [UserGroupsController::class, 'update_user_group'])->name('update_user_group');

        Route::get('/my-profile', [UsersController::class, 'my_profile'])->name('profile');
        Route::get('/get-menu-items', [MenuController::class, 'get_menu_items'])->name('get_menu_items');

        Route::get('/filter-posts/{type}', [PostsController::class, 'index'])->name('posts.index');
        Route::post('/recover-post', [PostsController::class, 'recover_post'])->name('post.recover');
        Route::post('/posts/delete-permanently', [PostsController::class, 'delete_permanently'])->name('post.delete_permanently');
        Route::post('/take-over-post', [PostsController::class, 'take_over_post'])->name('take_over_post');

        Route::get('/fetch-tags', [TagsController::class, 'fetch_tags'])->name('fetch_tags');

        Route::post('store-options', [SettingsController::class, 'store_options'])->name('store_options');
        Route::post('update-category', [CategoriesController::class, 'update_category'])->name('update_category');
        Route::post('update-tag', [TagsController::class, 'update_tag'])->name('update_tag');
        Route::post('update-user', [UsersController::class, 'update_user'])->name('update_user');
        Route::post('delete-user', [UsersController::class, 'destroy'])->name('delete_user');
        Route::post('update-profile', [UsersController::class, 'update_profile'])->name('update_profile');
        Route::post('change-password', [UsersController::class, 'change_password'])->name('change_password');
        Route::post('upload-file', [PostsController::class, 'upload_file'])->name('upload_file');
        Route::post('upload-intext-file', [PostsController::class, 'upload_intext_file'])->name('upload_intext_file');

        Route::post('update-image-tags', [PostsController::class, 'update_image_tags'])->name('update_image_tags');
        Route::post('posts/update-post', [PostsController::class, 'create_update_post'])->name('update_post');
        Route::post('widgets/update-widget', [WidgetsController::class, 'create_update_widget'])->name('update_widget');

        Route::post('store-settings', [SettingsController::class, 'store'])->name('create_settings');
        Route::post('update-settings', [SettingsController::class, 'update'])->name('update_settings');
        Route::delete('delete-settings/{id}', [SettingsController::class, 'destroy'])->name('delete_setting');

        Route::get('media/images/fetch', [MediaController::class, 'fetch_images'])->name('media.fetch_images');

        Route::prefix('tv')->as('tv.')->group(function () {
            Route::resource('shows', ShowsController::class);
            Route::resource('program_lineup', ProgramLineupController::class)->except(["create", "show", "edit", "update"]);
        });

        Route::prefix('datatable')->as('datatable.')->group(function () {
            Route::get('/get-users', [DatatablesController::class, 'get_users'])->name('get_users');
            Route::get('/get-shows', [DatatablesController::class, 'get_shows'])->name('get_shows');
            Route::get('/get-users-groups', [DatatablesController::class, 'get_user_groups'])->name('get_user_groups');
            Route::get('/get-categories', [DatatablesController::class, 'get_categories'])->name('get_categories');
            Route::get('/get-products', [DatatablesController::class, 'get_products'])->name('get_products');
            Route::get('/get-tags', [DatatablesController::class, 'get_tags'])->name('get_tags');
            Route::get('/get-pages', [DatatablesController::class, 'get_pages'])->name('get_pages');
            Route::get('/get-videos', [DatatablesController::class, 'get_videos'])->name('get_videos');
            Route::get('/get-settings', [DatatablesController::class, 'get_settings'])->name('get_settings');
            Route::get('/get-widgets', [DatatablesController::class, 'get_widgets'])->name('get_widgets');
            Route::get('/get-posts/{type}', [DatatablesController::class, 'get_posts'])->name('get_posts');
        });

        Route::prefix('settings')->as('settings.')->group(function () {
            Route::resource('/widgets', WidgetsController::class)->except(["create", "show", "update"]);
            Route::get('/general', [SettingsController::class, 'general'])->name('general');
            Route::get('/advertisements', [SettingsController::class, 'advertisements'])->name('advertisements');
            Route::resource('user_groups', UserGroupsController::class)->except(['create', 'show', 'edit', 'update', 'destroy']);
            Route::resource('menus', MenuController::class)->except(['create', 'show', 'edit']);
            Route::resource('menu_items', MenuItemController::class)->except(['index', 'create', 'show', 'edit', 'destroy']);
        });
    }
);

Route::post('/admin/custom-login', [AdminController::class, 'login'])->name('custom_login');

Route::get('/{category}/article/{id}/{slug}', function ($category, $id, $slug) {
    $url = config('cms.home_url') . "/{$category}/article/{$id}/{$slug}";
    return redirect()->away($url);
})->name('post');

Route::get('preview/{id}', function ($id) {
    $url = config('cms.home_url') . "/preview/{$id}";
    return redirect()->away($url);
})->name('preview');

require __DIR__ . '/auth.php';
