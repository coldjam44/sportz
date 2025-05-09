<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AboutusController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ContactusController;
use App\Http\Controllers\UserstoreController;
use App\Http\Controllers\AddproductController;
use App\Http\Controllers\SportsuserController;
use App\Http\Controllers\CreatestoreController;
use App\Http\Controllers\ProviderrateController;
use App\Http\Controllers\CreatestadiumController;
use App\Http\Controllers\AvilableserviceController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]
    ], function(){

        Auth::routes();

        Route::group(['middleware' => 'guest'],function(){
            Route::get('/', function () {
                return view('auth.login');
            });
        });
        Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

        Route::resource('sportsusers',SportsuserController::class);
        Route::resource('providerrates',ProviderrateController::class);
        Route::resource('sections',SectionController::class);
        Route::resource('addproducts', AddproductController::class);
        Route::resource('avilableservices',AvilableserviceController::class);
        Route::resource('contactuss',ContactusController::class);
        Route::resource('aboutuss',AboutusController::class);
        Route::resource('userstores',UserstoreController::class);




        Route::prefix('createstadiums')->group(function () {
            // عرض جميع البيانات
            Route::get('/', [CreatestadiumController::class, 'index'])->name('createstadiums.index');

            // إضافة بيانات جديدة
            Route::get('/create', [CreatestadiumController::class, 'create'])->name('createstadiums.create');
            Route::post('/', [CreatestadiumController::class, 'store'])->name('createstadiums.store');

            // تعديل البيانات
            Route::get('/{id}/edit', [CreatestadiumController::class, 'edit'])->name('createstadiums.edit');
            Route::put('/{id}', [CreatestadiumController::class, 'update'])->name('createstadiums.update');

            // حذف البيانات
            Route::delete('/{id}', [CreatestadiumController::class, 'destroy'])->name('createstadiums.destroy');
        });

        Route::prefix('createstores')->group(function () {
            // عرض جميع البيانات
            Route::get('/', [CreatestoreController::class, 'index'])->name('createstores.index');

            //إضافة بيانات جديدة
            Route::get('/create', [CreatestoreController::class, 'create'])->name('createstores.create');
            Route::post('/', [CreatestoreController::class, 'store'])->name('createstores.store');

            // تعديل البيانات
            Route::get('/{id}/edit', [CreatestoreController::class, 'edit'])->name('createstores.edit');
            Route::put('/{id}', [CreatestoreController::class, 'update'])->name('createstores.update');

            // حذف البيانات
            Route::delete('/{id}', [CreatestoreController::class, 'destroy'])->name('createstores.destroy');
        });

});


