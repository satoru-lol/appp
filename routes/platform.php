<?php

declare(strict_types=1);

use App\Orchid\Screens\CategoriesScreen;
use App\Orchid\Screens\CourseCreateScreen;
use App\Orchid\Screens\Examples\ExampleActionsScreen;
use App\Orchid\Screens\Examples\ExampleCardsScreen;
use App\Orchid\Screens\Examples\ExampleChartsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsAdvancedScreen;
use App\Orchid\Screens\Examples\ExampleFieldsScreen;
use App\Orchid\Screens\Examples\ExampleGridScreen;
use App\Orchid\Screens\Examples\ExampleLayoutsScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\Examples\ExampleTextEditorsScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Product\ProductListScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\RoleContent\RolecContentListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use App\Orchid\Screens\Video\VideoListScreen;
use App\Orchid\Screens\Clubs\ClubScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;
use App\Orchid\Screens\Clubs\CreateClubScreen;
use App\Orchid\Screens\Clubs\ClubEditScreen;
use App\Orchid\Screens\VideoEditScreen;
use App\Orchid\Screens\CourseContentEditScreen;
/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');
Route::screen('club/{id?}', ClubScreen::class)->name('platform.club.screen');
// Подписки
Route::screen('products', ProductListScreen::class)->name('platform.products');
//Route::screen('videos', VideoListScreen::class)->name('platform.videos');
Route::screen('rolesContents', RolecContentListScreen::class)->name('platform.roles.contents');
Route::screen('clubs', \App\Orchid\Screens\Clubs\ClubScreen::class)->name('platform.clubs');
Route::screen('courses', \App\Orchid\Screens\CourseScreen::class)->name('platform.courses');
Route::screen('create-course', CourseCreateScreen::class)->name('platform.course.create');
Route::screen('courseContent', \App\Orchid\Screens\CourseContentScreen::class)->name('platform.courseContent');
Route::screen('folders', CategoriesScreen::class)->name('platform.folders');
Route::screen('new_video', \App\Orchid\Screens\VideoScreen::class)->name('platform.new_video');
Route::screen('edit_video/{video}', VideoEditScreen::class)->name('platform.video.edit');

Route::screen('course-content/edit/{content}', CourseContentEditScreen::class)
    ->name('platform.course-content.edit');
Route::screen('donat', \App\Orchid\Screens\DonatScreen::class)->name('platform.donat');
Route::screen('bitrix', \App\Orchid\Screens\BitrixScreen::class)->name('platform.bitrix');
Route::screen('bitrix_report', \App\Orchid\Screens\BitrixReportScreen::class)->name('platform.bitrix_report');
Route::screen('modules', \App\Orchid\Screens\ModuleScreen::class)->name('platform.modules');
Route::screen('sections', \App\Orchid\Screens\SectionScreen::class)->name('platform.sections');
Route::screen('lessons', \App\Orchid\Screens\LessonsScreen::class)->name('platform.lessons');
// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name ?? $user->firstname, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn (Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));

// Example...
Route::screen('example', ExampleScreen::class)
    ->name('platform.example')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Example Screen'));

Route::screen('/examples/form/fields', ExampleFieldsScreen::class)->name('platform.example.fields');
Route::screen('/examples/form/advanced', ExampleFieldsAdvancedScreen::class)->name('platform.example.advanced');
Route::screen('/examples/form/editors', ExampleTextEditorsScreen::class)->name('platform.example.editors');
Route::screen('/examples/form/actions', ExampleActionsScreen::class)->name('platform.example.actions');

Route::screen('/examples/layouts', ExampleLayoutsScreen::class)->name('platform.example.layouts');
Route::screen('/examples/grid', ExampleGridScreen::class)->name('platform.example.grid');
Route::screen('/examples/charts', ExampleChartsScreen::class)->name('platform.example.charts');
Route::screen('/examples/cards', ExampleCardsScreen::class)->name('platform.example.cards');
Route::screen('/addPart', \App\Orchid\Screens\AddPartScreen::class)->name('platform.addPart.route');

Route::screen('/create-club', CreateClubScreen::class)
    ->name('platform.create.club');


Route::screen('clubs/{club}/edit', ClubEditScreen::class)->name('platform.club.edit');

//Route::screen('idea', Idea::class, 'platform.screens.idea');
