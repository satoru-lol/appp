<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController as OldProfileController;
use App\Http\Controllers\V2\ProfileController as NewProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\SpecialistController;
use App\Http\Controllers\PolygonController;
use App\Http\Controllers\OurMeetingController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\VideoLibraryController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\GenerateController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\IntroductionController;
use App\Http\Controllers\PaymentController;

use App\Exports\RandomData;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactFormSubmissionController;
use App\Http\Controllers\V2\AdminController;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Spatie\Honeypot\ProtectAgainstSpam;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use Illuminate\Support\Facades\Artisan;

Route::get('/sitemap.xml', [HomeController::class, 'sitemap']);

// Подключаем маршруты для видеотеки v2
require __DIR__.'/v2.php';

// Подключаем маршруты для курсов v2
require __DIR__.'/web-courses-v2.php';

// Подключаем рефакторенные V2 маршруты
require __DIR__.'/v2-refactored.php';

Route::get('/clear-cache', function() {
    Artisan::call('optimize:clear');
    return "Cache is cleared";
});

Route::get('/test-mail', function () {
    $url = route('introductionConfirm', 'testhash');
    Mail::to('nf.morkovin@gmail.com')->send(new \App\Mail\Introduction($url));
    return 'Mail sent!';
});

Route::get('/getDataBitrix',[BonusController::class,'getData']);
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/test', [HomeController::class, 'test'])->name('test');
Route::post('/addcomment', [BlogController::class, 'addcomment'])->name('addcomment');
Route::get('/addlike/{id}', [BlogController::class, 'addlike'])->name('addlike');
Route::get('/adddislike/{id}', [BlogController::class, 'adddislike'])->name('adddislike');

Route::get('/about', [HomeController::class, 'about'])->name('about');

Route::post('/pages/{page}', [HomeController::class, 'pages'])->name('pages');

// Редирект со старой видеотеки на новую
Route::get('/videostream', function () {
    return redirect()->route('v2.video.index');
})->name('videostream');

Route::get('/terms', [HomeController::class, 'terms'])->name('terms');

Route::get('/privacy_policy', [HomeController::class, 'privacy_policy'])->name('privacy_policy');
Route::get('/regmerop', [HomeController::class, 'regmerop'])->name('regmerop');
Route::get('/regmerop/delete/{id}', [HomeController::class, 'deleteRegmerop']);

Route::group(['middleware' => 'guest'], function () {

    // Авторизация
    Route::get('/auth', [AuthController::class, 'index'])->name('login');
    Route::post('/auth', [AuthController::class, 'Auth'])->middleware(ProtectAgainstSpam::class)->name('Auth');

    // Вступление
    Route::get('/introduction', [HomeController::class, 'introduction'])->name('introduction');
    Route::post('/introduction', [HomeController::class, 'introductionSend'])->name('introductionSend');
    Route::get('/introduction/confirm/{hash}', [HomeController::class, 'introductionConfirm'])->name('introductionConfirm');
    //авторизация тест
    // Route::get('/auth-test', [HomeController::class, 'authTest'])->name('login-test');
    // Route::post('/auth-test', [HomeController::class, 'authLogin'])->name('login-auth');
});
Route::get('/video_library/{id}',[\App\Http\Controllers\CategoryController::class,'showCategories'])->name('show.category');
Route::get('/categories/{id}/videos',[\App\Http\Controllers\CategoryController::class,'getVideos']);

// Профиль пользователя
Route::get('/avatar/{user}', function (User $user) {
    $file = glob(public_path('img/avatars/') . md5($user->id . $user->phone) . '.*');

    if (!$file)
        $file[0] = './img/avatar.png';

    header('Content-type: ' . image_type_to_mime_type(exif_imagetype($file[0])));
    readfile($file[0]);
});

Route::group(['middleware' => 'auth'], function () {

    // Бонусная программа
    Route::get('/bonus', [BonusController::class, 'index'])->name('bonus');

    // Перенаправление на обновлённые маршруты профиля v2
    Route::get('/profile', fn() => redirect()->route('v2.profile.index'))->name('profile');
    // Для удаления аватара старый endpoint перенаправляет на новый v2
    Route::post('/removeAvatar', fn() => redirect()->route('v2.profile.remove-avatar'))->name('removeAvatar');

    // Сохраняем старый маршрут доната, так как он используется на странице профиля v2
    Route::post('/profile/donat', [OldProfileController::class, 'donat'])->name('donat');

    // Подписки
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions');

    // Блог
    Route::get('/blog/add', [BlogController::class, 'showAdd'])->name('blog.add');
    Route::post('/blog/add', [BlogController::class, 'add'])->name('blog.add');

    Route::post('/blog/category', [BlogController::class, 'addCategory'])->where('id', '(\d+)')->name('blog.category.add');
    Route::post('/blog/category/{id}', [BlogController::class, 'updateCategory'])->where('id', '(\d+)')->name('blog.category.update');
    Route::get('/blog/category/destroy/{id}', [BlogController::class, 'destroyCategory'])->where('id', '(\d+)')->name('blog.category.destroy');


    // Регулярные выражения
    Route::get('/reg/add', [RegController::class, 'showAdd'])->name('reg.add');
    Route::post('/reg/add', [RegController::class, 'add'])->name('reg.add');


    // Форум
    Route::post('/forum/category', [ForumController::class, 'addCategory'])->name('forum.addCategory');
    Route::post('/forum/category/{id}', [ForumController::class, 'updateCategory'])->where('id', '(\d+)')->name('forum.updateCategory');
    Route::get('/forum/category/destroy/{id}', [ForumController::class, 'destroyCategory'])->where('id', '(\d+)')->name('forum.destroyCategory');

    Route::post('/forum/topic', [ForumController::class, 'addTopic'])->name('forum.addTopic');
    Route::post('/forum/topic/{id}', [ForumController::class, 'updateTopic'])->where('id', '(\d+)')->name('forum.updateTopic');

    Route::post('/forum/comment', [ForumController::class, 'addComment'])->name('forum.addComment');
    Route::post('/forum/comment/destroy/{id}', [ForumController::class, 'destroyComment'])->where('id', '(\d+)')->name('forum.destroyComment');
    Route::post('/forum/comment/{id}', [ForumController::class, 'updateComment'])->where('id', '(\d+)')->name('forum.updateComment');
    Route::get('/forum/delete/{id}', [ForumController::class, 'deleteForum']);
    
    // Специалисты
    /*Route::get('/specialists/add', [SpecialistController::class, 'showAdd'])->name('specialists.showAdd');
    Route::post('/specialists/add', [SpecialistController::class, 'add'])->name('specialists.add');

    Route::post('/specialists/approve', [SpecialistController::class, 'approve'])->name('specialists.approve');

    Route::post('/specialists/category', [SpecialistController::class, 'addCategory'])->where('id', '(\d+)')->name('specialists.category.add');
    Route::post('/specialists/category/{id}', [SpecialistController::class, 'updateCategory'])->where('id', '(\d+)')->name('specialists.category.update');
     Route::get('/specialists/category/destroy/{id}', [SpecialistController::class, 'destroyCategory'])->where('id', '(\d+)')->name('specialists.category.destroy');
*/
    // Курсы
    Route::post('/courses/store', [CourseController::class, 'store'])->name('courses.store');

    Route::get('/courses/add', [CourseController::class, 'showAdd'])->name('courses.add');
    Route::get('/courses/category/destroy/{id}', [CourseController::class, 'destroyCategory'])->where('id', '(\d+)')->name('courseCategoryDestroy');

    Route::post('/courses/category', [CourseController::class, 'addCategory'])->name('courseCategoryAdd');
    
    
    Route::post('/courses/category/{id}', [CourseController::class, 'updateCategory'])->where('id', '(\d+)')->name('courseCategoryUpdate');
    Route::get('/courses/delete/{id}', [CourseController::class, 'deleteCourse']);
    Route::get('/courses/{id}/subscribe', [CourseController::class, 'subscribeToCourse']);

    Route::get('/video/{id}', [\App\Http\Controllers\VideoLibraryController::class, 'show'])->name('video.detail');
    Route::resource('video-libraries', \App\Http\Controllers\VideoLibraryController::class);


    // Выход из аккаунта
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    /*
    Route::post('/ourMeetings/add', [\App\Http\Controllers\OurMeetingsController::class, 'add'])->name('ourMeetings.add');
    Route::post('/ourMeetings/search', [\App\Http\Controllers\OurMeetingsController::class, 'search'])->name('our_meetings.search');
    Route::get('/ourMeetings/edit/{id}', [\App\Http\Controllers\OurMeetingsController::class, 'editOurMeetings'])->name('ourMeetings.edit');
    Route::post('/ourMeetings/update', [\App\Http\Controllers\OurMeetingsController::class, 'updateOurMeetings'])->name('ourMeetings.update');
    Route::get('/ourMeetings/delete/{id}', [\App\Http\Controllers\OurMeetingsController::class, 'deleteOurMeeting']);
    Route::get('/ourMeetings/add', [\App\Http\Controllers\OurMeetingsController::class, 'showAdd'])->name('ourMeetings.add');
    */
});
//регулярные мероприятия
Route::get('/reg/{id}', [RegController::class, 'show'])->where('id', '(\d+)')->name('reg.show');
Route::get('/reg/category/{id}', [RegController::class, 'index'])->where('id', '(\d+)')->name('reg.category');


// Блог
Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/blog/{id}', [BlogController::class, 'show'])->where('id', '(\d+)')->name('blog.show');
Route::get('/blog/category/{id}', [BlogController::class, 'index'])->where('id', '(\d+)')->name('blog.category');


// Форум
/*
Route::get('/forum', [ForumController::class, 'index'])->name('forum');
Route::get('/forum/category/{id}', [ForumController::class, 'index'])->where('id', '(\d+)')->name('forum.category');
Route::get('/forum/topic/{id}', [ForumController::class, 'showTopic'])->where('id', '(\d+)')->name('forum.showTopic');
Route::post('/forum/search', [ForumController::class, 'search'])->name('forum.search');
*/

// Специалисты
/*Route::get('/specialists', [SpecialistController::class, 'index'])->name('specialists');
Route::get('/specialists/{id}', [SpecialistController::class, 'show'])->where('id', '(\d+)')->name('specialists.show');
Route::post('/specialists/search', [SpecialistController::class, 'search'])->name('specialists.search');
Route::get('/specialists/category/{id}', [SpecialistController::class, 'index'])->name('specialists.category');*/

// Курсы
// Скрытые старые маршруты курсов заменены на переадресацию на новые страницы
Route::get('/courses', fn() => redirect()->route('courses-v2.index'));
Route::get('/courses/category/{id}', fn($id) => redirect()->route('courses-v2.category', $id))->where('id', '(\d+)');
Route::get('/courses/{id}', fn($id) => redirect()->route('courses-v2.show', $id))->where('id', '(\d+)');
// Клуб
// Скрытые старые маршруты клубов заменены на переадресацию на новые страницы
Route::get('/club', fn() => redirect()->route('v2.club.index'));
Route::get('/club/{id}', fn($id) => redirect()->route('v2.club.show', $id))->where('id', '(\d+)');

Route::group(['prefix' => 'v2/club', 'as' => 'v2.club.'], function() {
    Route::get('/', [ClubController::class, 'indexTest'])->name('index');
    Route::get('/{id}', [ClubController::class, 'showTest'])->where('id', '(\d+)')->name('show');
});

Route::post('/club/donate', [ClubController::class, 'donate'])->name('club.donate');

Route::get('/api/select-options', [CourseController::class, 'getOptions'])->name('select.options');
Route::get('/api/getParts', [CourseController::class, 'getParts']);
Route::get('/payment/success', [App\Http\Controllers\V2\ProfileController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment/fail', [App\Http\Controllers\V2\ProfileController::class, 'paymentFail'])->name('payment.fail');

Route::match(['get', 'post'], '/webhook/robokassa', [App\Http\Controllers\Api\WebhookController::class, 'handleRobokassa'])->name('webhook.robokassa');

Route::get('/delEnt/{id}/{entity}', [HomeController::class, 'deleteEntRow']);
Route::get('/hideCourse/{id}/{action}', [HomeController::class, 'hideCourse']);

/*
Route::get('/ourMeetings', [\App\Http\Controllers\OurMeetingsController::class, 'index'])->name('ourMeetings');
Route::get('/ourMeetings/deleteImage/{image}', [\App\Http\Controllers\OurMeetingsController::class, 'delete']);
Route::get('/ourMeetings_previous', [\App\Http\Controllers\OurMeetingsController::class, 'previous'])->name('ourMeetings_previous');
*/
Route::get('/ourMeetings', function () {
    return redirect()->route('v2.meetings.index', [], 301);
});

Route::get('/ourMeetings/{id}', function ($id) {
    return redirect()->route('v2.meetings.show', ['id' => $id], 301);
})->where('id', '(\d+)');


Route::post('/club/store', [ClubController::class, 'store'])->name('club.store');
Route::get('/club/create/show', [ClubController::class, 'createShow'])->name('club.crate.show');
Route::get('/club/{id}', [ClubController::class, 'show'])->where('id', '(\d+)')->name('clubShow');
Route::get('/hideClub/{id}/{action}', [HomeController::class, 'hideClub']);
Route::get('/club/old', [ClubController::class, 'indexOld'])->name('clubOld');
Route::match(['get', 'post'], '/club/edit/{id}', [ClubController::class, 'editClub'])->name('editClub');

Route::post('/user/edit/role', [ProfileController::class, 'editRole'])->name('editRole');
Route::get('/user/edit/perms/{settings}', [ProfileController::class, 'editPerms'])->name('editPerms');
Route::post('/user/get/perms', [ProfileController::class, 'getPerms'])->name('getPerms');
Route::post('/user/admin', [ProfileController::class, 'userAdmin'])->name('userAdmin');

Route::get('/course/edit/{id}', [CourseController::class, 'editCourse'])->name('editCourse');
Route::post('/course/update', [CourseController::class, 'updateCourse'])->name('updateCourse');


Route::get('/polygons', [\App\Http\Controllers\PolygonController::class, 'index'])->where('id', '(\d+)')->name('polygons');

Route::get('/polygon/{id}', [\App\Http\Controllers\PolygonController::class, 'show'])->name('polygon.show');


Route::post('/product/get', [\App\Http\Controllers\ProfileController::class, 'productGet'])->name('productGet');
Route::group(['middleware' => ['auth', 'admin']], function () {
    Route::get('/product/edit', [\App\Http\Controllers\ProfileController::class, 'productEdit'])->name('productEdit');
    Route::post('/product/create', [\App\Http\Controllers\ProfileController::class, 'productCreate'])->name('productCreate');

    Route::get('/blog/delete/{id}', [BlogController::class, 'deleteBlog']);
    Route::get('/blog/edit/{id}', [BlogController::class, 'editBlog'])->name('editBlog');
    Route::get('/regmerop/edit/{id}', [RegController::class, 'editRegMerop'])->name('editRegmerop');
    Route::post('/regmerop/update', [RegController::class, 'updateReg'])->name('updateReg');
    Route::post('/polygon/add', [\App\Http\Controllers\PolygonController::class, 'add'])->name('polygon.add');
    Route::get('/show/add', [\App\Http\Controllers\PolygonController::class, 'showAdd'])->name('polygon.show.add');
    Route::get('/polygon/edit/{id}', [\App\Http\Controllers\PolygonController::class, 'editPolygon'])->name('editPolygon');
    Route::post('/polygon/update', [\App\Http\Controllers\PolygonController::class, 'updatePolygon'])->name('updatePolygon');
    Route::get('/product/delete/{id}', [\App\Http\Controllers\ProfileController::class, 'productDelete'])->name('productDelete');

    Route::get('/product/hide/{id}', [\App\Http\Controllers\ProfileController::class, 'productHide'])->name('productHide');


    Route::post('/subscriptions/{id}/update', [SubscriptionController::class, 'update']);

    // Route::get('/export-users/{id}', [ProfileController::class, 'exportUsers'])->name('export.users');
});
Route::get('/takePart/delete/{objectId}/{userId}', [\App\Http\Controllers\HomeController::class, 'takePartDelete']);
Route::post('/send/mail', [\App\Http\Controllers\PolygonController::class, 'sendMail'])->name('sendMail');
Route::get('/user/info/{id}', [\App\Http\Controllers\ProfileController::class, 'userInfo'])->name('userInfo');


Route::get('/subscriptions', [SubscriptionController::class, 'index']);
Route::post('/subscriptions/confirm', [SubscriptionController::class, 'confirmPurchase']);
Route::post('/subscriptions/cancel', [SubscriptionController::class, 'cancel']);
  
///
Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
Route::post('/cancelSubscribe', [SubscriptionController::class, 'cancelSubscribe'])->name('cancelSubscribe');
  Route::post('/changeToHigher', [SubscriptionController::class, 'changeToHigher'])->name('changeToHigher');
Route::post('/verify-code', [SubscriptionController::class, 'verifyCode'])->name('verify-code');
Route::post('/verify-code/success', [SubscriptionController::class, 'verifyCodeSuccess'])->name('verify-code-success');
Route::post('/pay', [SubscriptionController::class, 'redirectToPayment']);
Route::post('/cancelMonthPay', [ProfileController::class, 'cancelMonthPay']);
Route::get('/removeAvatar', [ProfileController::class, 'removeAvatar'])->name('profileDelete');

/*
Route::get('/ourMeetings', [\App\Http\Controllers\OurMeetingsController::class, 'index'])->name('ourMeetings');
Route::get('/ourMeetings/deleteImage/{image}', [\App\Http\Controllers\OurMeetingsController::class, 'delete']);
Route::get('/ourMeetings_previous', [\App\Http\Controllers\OurMeetingsController::class, 'previous'])->name('ourMeetings_previous');
*/
Route::get('/authPay/{product_id}/{user_id}/{auto?}', [SubscriptionController::class, 'authPay']);
Route::post('/confirm-code', [AuthController::class, 'confirmCode'])->name('confirmCode');
/*
Route::get('/ourMeetings/{id}', [\App\Http\Controllers\OurMeetingsController::class, 'show'])->where('id', '(\d+)')->name('ourMeetings.show');
*/

Route::get('/takePart/{object}/{id}', [\App\Http\Controllers\HomeController::class, 'takePart']);

Route::get('/admin/search-user', [ProfileController::class, 'searchUserAjax'])->name('admin.search.user');


/*Route::get('/video/{id}', [\App\Http\Controllers\VideoLibraryController::class, 'show'])->name('video.detail');
Route::resource('video-libraries', \App\Http\Controllers\VideoLibraryController::class);*/

Route::get('password/reset', [AuthController::class, "passwordReset"])->name('password.request');
Route::post('password/email', [AuthController::class, "passwordEmail"])->name('password.email');
Route::get('password/reset/{token}/{email}', [\App\Http\Controllers\ResetPasswordController::class, "showResetForm"])->name('password.reset');
Route::post('password/reset', [\App\Http\Controllers\ResetPasswordController::class, "reset"])->name('password.update');

//`/takePart/delete/{{$blog->id}}/{{auth()->user()->id}}
/*// Панель администратора
Route::group(['middleware' => 'admin'], function () {

    // ---- Разработка после запуска



    // ---- Разработка после запуска

    Route::name('admin.')->prefix('admin')->group(function () {

        // Настройки
        Route::get('/settings/add', [SettingsController::class, 'showAdd'])->name('settings.add');
        Route::get('/settings/destroy/{id}', [SettingsController::class, 'destroy'])->where('id', '(\d+)')->name('settings.destroy');
        Route::post('/settings/add', [SettingsController::class, 'add']);
        Route::resource('/settings', SettingsController::class)->only(['index', 'update']);

    });

});*/

Route::get('/php', function(){
   echo phpinfo();
});

Route::get('/export-random', function () {
    return Excel::download(new RandomData, 'random_data.xlsx');
})->name('export.random');
Route::get('users/export/', function (){
    return Excel::download(new UsersExport, 'data.xlsx');
})->name('users.export');

Route::get('generate',function(){
    return view('generate.index');
});

Route::post('generateCourse',[BonusController::class,'generateCourse'])->name('generateCourse');

// V2 Profile Routes
Route::middleware('auth')->prefix('v2/profile')->name('v2.profile.')->group(function () {
    Route::get('/', [NewProfileController::class, 'index'])->name('index');
    Route::put('/update', [NewProfileController::class, 'update'])->name('update');
    Route::post('/avatar', [NewProfileController::class, 'updateAvatar'])->name('avatar.update');
    Route::delete('/avatar', [NewProfileController::class, 'removeAvatar'])->name('avatar.remove');
    Route::get('/transactions', [NewProfileController::class, 'fetchTransactions'])->name('transactions');
    Route::post('/generate-qr-link', [NewProfileController::class, 'generateQrLink'])->name('generate-qr-link');
    Route::post('/subscription', [NewProfileController::class, 'handleSubscription'])->name('subscription.handle');
});

Route::middleware(['auth', 'admin'])->prefix('v2/admin')->name('v2.admin.')->group(function () {
    Route::get('/subscription-diagnostics', [AdminController::class, 'subscriptionDiagnostics'])->name('subscription.diagnostics');
    Route::post('/subscription-diagnostics/fix', [AdminController::class, 'fixSubscription'])->name('subscription.fix');
    Route::post('/subscription-diagnostics/revoke-transitional', [AdminController::class, 'revokeTransitionalSubscriptions'])->name('subscription.revoke_transitional');
});

Route::get('/v2/avatar/{userId}', [NewProfileController::class, 'getAvatar'])->name('v2.avatar');

// Balance Top-up
Route::post('/balance/add', [NewProfileController::class, 'addBalance'])->middleware('auth')->name('balance.add');


// Route::get('/home', [HomeController::class, 'index'])->name('home'); // Дублирующий маршрут
Route::get('/admin/users_excel', [UsersController::class, 'excel'])->name('admin.users_excel');

// Встречи v2
Route::group(['prefix' => 'v2/meetings', 'as' => 'v2.meetings.'], function () {
    Route::get('/', [\App\Http\Controllers\V2\MeetingsController::class, 'index'])->name('index');
    Route::get('/previous', [\App\Http\Controllers\V2\MeetingsController::class, 'previous'])->name('previous');
    Route::get('/create', [\App\Http\Controllers\V2\MeetingsController::class, 'create'])->name('create')->middleware('auth');
    Route::post('/', [\App\Http\Controllers\V2\MeetingsController::class, 'store'])->name('store')->middleware('auth');
    Route::get('/{id}', [\App\Http\Controllers\V2\MeetingsController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [\App\Http\Controllers\V2\MeetingsController::class, 'edit'])->name('edit')->middleware('auth');
    Route::put('/{id}', [\App\Http\Controllers\V2\MeetingsController::class, 'update'])->name('update')->middleware('auth');
    Route::delete('/{id}', [\App\Http\Controllers\V2\MeetingsController::class, 'destroy'])->name('delete')->middleware('auth');
    Route::get('/{id}/take-part', [\App\Http\Controllers\V2\MeetingsController::class, 'takePart'])->name('takePart')->middleware('auth');
    Route::get('/{id}/cancel-part', [\App\Http\Controllers\V2\MeetingsController::class, 'cancelPart'])->name('cancelPart')->middleware('auth');
    Route::post('/{id}/comment', [\App\Http\Controllers\V2\MeetingsController::class, 'addComment'])->name('addComment')->middleware('auth');
    Route::post('/{id}/like', [\App\Http\Controllers\V2\MeetingsController::class, 'addLike'])->name('addLike')->middleware('auth');
    Route::post('/{id}/dislike', [\App\Http\Controllers\V2\MeetingsController::class, 'addDislike'])->name('addDislike')->middleware('auth');
});

Route::get('/pay/{service_id}',[PaymentController::class,'create'])->name('pay');

// V2 Home Route
Route::get('/v2/home', [HomeController::class, 'indexV2'])->name('v2.home');
