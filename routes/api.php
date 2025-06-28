<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\StoretypeController;
use App\Http\Controllers\HideController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\UserauthController;
use App\Http\Controllers\Apis\AuthController;
use App\Http\Controllers\Apis\OrderController;
use App\Http\Controllers\BookstadiumController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Apis\AboutusController;
use App\Http\Controllers\Apis\SectionController;
use App\Http\Controllers\ProviderauthController;
use App\Http\Controllers\Apis\ContactusController;
use App\Http\Controllers\Apis\UserstoreController;
use App\Http\Controllers\Apis\AddproductController;
use App\Http\Controllers\Apis\SportsuserController;
use App\Http\Controllers\Apis\CreatestoreController;
use App\Http\Controllers\Apis\ProviderrateController;
use App\Http\Controllers\Apis\CreatestadiumController;
use App\Http\Controllers\Apis\AvilableserviceController;
use App\Http\Controllers\TimeCalculationController;
use App\Http\Controllers\NotificationController;
// use App\Http\Controllers\Apis\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('register', [AuthController::class, 'register']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);

Route::post('usrotp', [AuthController::class, 'usrotp']);

Route::post('prvotp', [AuthController::class, 'prvotp']);

Route::middleware('auth:api')->post('/user/signup', [UserauthController::class, 'signup']);
Route::middleware('auth:api')->post('/user/editprofile/{id}', [UserauthController::class, 'updateuser']); //hasNotification
Route::middleware('auth:api')->get('/user/index', [UserauthController::class, 'index']);
Route::middleware('auth:api')->post('/provider/editprofile/{id}', [ProviderauthController::class, 'update']);//hasNotification
Route::middleware('auth:api')->post('/provider/signup', [ProviderauthController::class, 'signup']);//hasNotification
Route::middleware('auth:api')->get('/provider/index', [ProviderauthController::class, 'index']);
Route::middleware('auth:api')->post('/provider/logout', [ProviderauthController::class, 'logout']);
Route::middleware('auth:api')->post('/provider/delete-account', [ProviderauthController::class, 'deleteAccount']);
Route::middleware('auth:api')->post('/user/logout', [UserauthController::class, 'logout']);
Route::middleware('auth:api')->post('/user/delete-account', [UserauthController::class, 'deleteAccount']);
Route::middleware('auth:api')->post('/hide', [HideController::class, 'hide']);
Route::middleware('auth:api')->post('/unhide', [HideController::class, 'unhide']);
Route::middleware('auth:api')->get('provider/section', [SectionController::class, 'index']);
Route::middleware('auth:api')->post('provider/section', [SectionController::class, 'store']);
Route::middleware('auth:api')->post('provider/section/{id}', [SectionController::class, 'update']);
Route::middleware('auth:api')->delete('provider/section/{id}', [SectionController::class, 'destroy']);
Route::middleware('auth:api')->get('provider/storetype', [StoretypeController::class, 'index']);
Route::middleware('auth:api')->post('provider/storetype', [StoretypeController::class, 'store']);
Route::middleware('auth:api')->post('provider/storetype/{id}', [StoretypeController::class, 'update']);
Route::middleware('auth:api')->delete('provider/storetype/{id}', [StoretypeController::class, 'destroy']);
Route::middleware('auth:api')->get('provider/avilableservice', [AvilableserviceController::class, 'index']);
Route::middleware('auth:api')->post('provider/avilableservice', [AvilableserviceController::class, 'store']);
Route::middleware('auth:api')->post('provider/avilableservice/{id}', [AvilableserviceController::class, 'update']);
Route::middleware('auth:api')->delete('provider/avilableservice/{id}', [AvilableserviceController::class, 'destroy']);
Route::middleware('auth:api')->get('provider/rate', [ProviderrateController::class, 'index']);
Route::middleware('auth:api')->post('provider/rate', [ProviderrateController::class, 'store']);
Route::middleware('auth:api')->post('provider/rate/{id}', [ProviderrateController::class, 'update']);
Route::middleware('auth:api')->delete('provider/rate/{id}', [ProviderrateController::class, 'destroy']);
Route::middleware('auth:api')->get('provider/stadiums', [CreatestadiumController::class, 'index']);
Route::middleware('auth:api')->post('provider/stadiums', [CreatestadiumController::class, 'store']);
Route::middleware('auth:api')->get('provider/stadiums/{id}', [CreatestadiumController::class, 'show']);
Route::middleware('auth:api')->post('provider/stadiums/{id}', [CreatestadiumController::class, 'update']);
Route::middleware('auth:api')->delete('provider/stadiums/{id}', [CreatestadiumController::class, 'destroy']);
Route::middleware('auth:api')->get('provider/store', [CreatestoreController::class, 'index']);
Route::middleware('auth:api')->post('provider/store', [CreatestoreController::class, 'store']);
Route::middleware('auth:api')->get('provider/store/{id}', [CreatestoreController::class, 'show']);
Route::middleware('auth:api')->post('provider/store/{id}', [CreatestoreController::class, 'update']);
Route::middleware('auth:api')->delete('provider/store/{id}', [CreatestoreController::class, 'destroy']);
Route::middleware('auth:api')->get('user/sports', [SportsuserController::class, 'index']);
Route::middleware('auth:api')->post('user/sports', [SportsuserController::class, 'store']);
Route::middleware('auth:api')->get('user/sports/{id}', [SportsuserController::class, 'show']);
Route::middleware('auth:api')->post('user/sports/{id}', [SportsuserController::class, 'update']);
Route::middleware('auth:api')->delete('user/sports/{id}', [SportsuserController::class, 'destroy']);
Route::middleware('auth:api')->get('user/favorite-sports', [SportsuserController::class, 'getFavoriteSports']);
Route::middleware('auth:api')->post('user/favorite-sports', [SportsuserController::class, 'selectFavoriteSports']);
Route::middleware('auth:api')->get('provider/addproduct', [AddproductController::class, 'index']);
Route::middleware('auth:api')->post('provider/addproduct', [AddproductController::class, 'store']);
Route::middleware('auth:api')->get('provider/addproduct/{id}', [AddproductController::class, 'show']);
Route::middleware('auth:api')->post('provider/addproduct/{id}', [AddproductController::class, 'update']);
Route::middleware('auth:api')->delete('provider/addproduct/{id}', [AddproductController::class, 'destroy']);
Route::middleware('auth:api')->get('provider/most-ordered-products', [AddproductController::class, 'mostOrderedProducts']);
Route::middleware('auth:api')->get('provider/contactus', [ContactusController::class, 'index']);
Route::middleware('auth:api')->post('provider/contactus', [ContactusController::class, 'store']);
Route::middleware('auth:api')->delete('provider/contactus/{id}', [ContactusController::class, 'destroy']);
Route::middleware('auth:api')->get('provider/aboutus', [AboutusController::class, 'index']);
Route::middleware('auth:api')->post('provider/aboutus', [AboutusController::class, 'store']);
Route::middleware('auth:api')->post('provider/aboutus/{id}', [AboutusController::class, 'update']);
Route::middleware('auth:api')->get('provider/aboutus/{id}', [AboutusController::class, 'show']);
Route::middleware('auth:api')->delete('provider/aboutus/{id}', [AboutusController::class, 'destroy']);
Route::middleware('auth:api')->get('user/store', [UserstoreController::class, 'index']);
Route::middleware('auth:api')->post('user/store', [UserstoreController::class, 'store']);
Route::middleware('auth:api')->get('user/store/{id}', [UserstoreController::class, 'show']);
Route::middleware('auth:api')->post('user/store/{id}', [UserstoreController::class, 'update']);
Route::middleware('auth:api')->delete('user/store/{id}', [UserstoreController::class, 'destroy']);
Route::middleware('auth:api')->get('store/{storeId}/orders', [UserstoreController::class, 'showStoreOrders']);
Route::middleware('auth:api')->post('/order/{orderId}/status', [CartController::class, 'updateOrderStatus']);
Route::middleware('auth:api')->get('/user/orders', [OrderController::class, 'userOrders']);
Route::middleware('auth:api')->get('/invoice/{id}', [InvoiceController::class, 'show']);
Route::middleware('auth:api')->post('/create-invoice', [InvoiceController::class, 'createInvoice']);
Route::middleware('auth:api')->group(function () {
    Route::post('cart/add', [CartController::class, 'addToCart']);
    Route::get('cart', [CartController::class, 'getCart']);
    Route::post('cart/update', [CartController::class, 'updateCart']);
    Route::delete('cart/remove/{id}', [CartController::class, 'removeFromCart']);
    Route::post('checkout', [CartController::class, 'checkout']);
    Route::get('/orders', [OrderController::class, 'index']);
    // عرض طلب معين باستخدام المعرف
    Route::get('/order/{orderId}', [OrderController::class, 'show']);
    Route::get('/order-count/{storeId}', [CreatestoreController::class, 'getOrderCountByStore']);
    Route::post('/user-orders/{orderId}', [OrderController::class, 'updateUserOrder']);
    Route::delete('/user-orders/{orderId}', [OrderController::class, 'deleteUserOrder']);
});
Route::middleware('auth:api')->group(function () {
    Route::post('/book-stadium', [BookstadiumController::class, 'store']);
    // Route::get('/bookings', [BookstadiumController::class, 'getBookings']);
    Route::get('/bookings/individual', [BookstadiumController::class, 'getIndividualBookings']);
    Route::get('/bookings/team', [BookstadiumController::class, 'getTeamBookings']);
});
Route::middleware('auth:api')->post('/join-team-booking', [BookstadiumController::class, 'joinTeamBooking']);
Route::middleware('auth:api')->post('/join-individual-booking', [BookstadiumController::class, 'joinIndividualBooking']);
Route::middleware('auth:api')->get('/getAllStadiums', [BookstadiumController::class, 'getAllStadiums']);
Route::middleware('auth:api')->get('/getStadiumDetails/{id}', [BookstadiumController::class, 'getStadiumDetails']);
Route::middleware('auth:api')->get('/stadiums/{id}', [BookstadiumController::class, 'getStadiumById']);
Route::middleware('auth:api')->get('/team-bookings/{id}', [BookstadiumController::class, 'getTeamBookingById']);
Route::middleware('auth:api')->get('/individual-bookings/{id}', [BookstadiumController::class, 'getIndividualBookingById']);
Route::middleware('auth:api')->group(function () {
    // جلب الحجوزات لملعب معين في تاريخ محدد
    Route::get('/provider/stadium/{stadium_id}/reservations', [BookstadiumController::class, 'getStadiumReservations']);
});
Route::middleware('auth:api')->get('/reservations/categorized', [ReservationController::class, 'categorizedReservations']);
Route::middleware('auth:api')->post('/reservation/{id}/cancel', [ReservationController::class, 'cancelReservation']);
Route::middleware('auth:api')->get('/reservation/{id}', [ReservationController::class, 'reservationDetails']);
Route::middleware('auth:api')->get('/my-reservations', [ReservationController::class, 'myReservations']);
Route::middleware('auth:api')->get('user/getfavourites', [CreatestoreController::class, 'getFavourites']);
Route::middleware('auth:api')->post('user/toggleFavourite', [CreatestoreController::class, 'toggleFavourite']);
Route::middleware('auth:api')->get('user/all-stores', [CreatestoreController::class, 'getAllStoresForUser']);
Route::middleware('auth:api')->get('user/all-products', [CreatestoreController::class, 'getStoreSectionsAndProducts']);
Route::middleware('auth:api')->get('store/{store_id}', [CreatestoreController::class, 'getStoreDetails']);
Route::middleware('auth:api')->get('user/productdeatials/{product_id}', [CreatestoreController::class, 'getProductDetails']);
Route::post('/time-details', [TimeCalculationController::class, 'calculateTime']);
Route::middleware('auth:api')->group(function () {
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications', [NotificationController::class, 'store']);
    Route::get('notifications/{notification}', [NotificationController::class, 'show']);
    Route::put('notifications/{notification}', [NotificationController::class, 'update']);
    Route::patch('notifications/{notification}', [NotificationController::class, 'update']);
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy']);
});
