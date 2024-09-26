<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Carriers\CarrierController;
use App\Http\Controllers\Api\Carriers\CorreosController;
use App\Http\Controllers\Api\Carriers\CttController;
use App\Http\Controllers\Api\Carriers\SelfShippingController;
use App\Http\Controllers\Api\Carriers\TrackingController;
use App\Http\Controllers\Api\Carriers\VaspController;
use App\Http\Controllers\Api\Carriers\VolumesController;
use App\Http\Controllers\Api\Chart\ChartController;
use App\Http\Controllers\Api\Configuration\Account\AccountController;
use App\Http\Controllers\Api\Configuration\Email\EmailController;
use App\Http\Controllers\Api\Configuration\Permission\PermissionController;
use App\Http\Controllers\Api\Configuration\Role\RoleController;
use App\Http\Controllers\Api\Configuration\User\UserController;
use App\Http\Controllers\Api\Customer\CustomerController;
use App\Http\Controllers\Api\Order\KuantoKusta\KuantoKustaController;
use App\Http\Controllers\Api\Order\Magento\MagentoController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Order\WooCommerce\WooCommerceController;
use App\Http\Controllers\Api\Order\Worten\WortenController;
use App\Http\Controllers\Api\OrderStatus\OrderStatusController;
use App\Http\Controllers\Api\Tokens\TokenController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByRequestData;

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

Route::group(['prefix' => 'auth'], function () {
    Route::post('/id', [AuthController::class, 'checkId']);
    Route::middleware([InitializeTenancyByRequestData::class])->group(function () {
        Route::post('/login', [AuthController::class, 'loginUser']);
    });
    Route::middleware(['auth:sanctum', InitializeTenancyByRequestData::class])->group(function () {
        Route::post('/register', [AuthController::class, 'store']);
        Route::post('/logout', [AuthController::class, 'logoutUser']);
        Route::delete('/delete/{userId}', [AuthController::class, 'destroy']);
    });
});

Route::resource('customer', CustomerController::class);

Route::middleware([InitializeTenancyByRequestData::class])->group(function () {
    Route::post('check-session', [AuthController::class, 'checkSession']);

    Route::group(['prefix' => 'chart'], function () {
        Route::get('/', [ChartController::class, 'index']);
        Route::get('/show', [ChartController::class, 'show']);
    });

    Route::group(['prefix' => 'order'], function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/show', [OrderController::class, 'show']);
        Route::put('/update-order-status', [OrderController::class, 'updateOrderStatus']);
        Route::post('/update-order-data/{orderId}', [OrderController::class, 'updateOrderData']);
        Route::patch('/{orderId}', [OrderController::class, 'update']);
        Route::post('/pdf', [OrderController::class, 'print']);

        Route::group(['prefix' => 'status'], function () {
            Route::get('/', [OrderStatusController::class, 'index']);
            Route::post('/create', [OrderStatusController::class, 'create']);
            Route::put('/toggle-active/{statusId}', [OrderStatusController::class, 'toggleActive']);
            Route::put('/delete/{statusId}', [OrderStatusController::class, 'deleteStatus']);
            Route::put('/update/{statusId}', [OrderStatusController::class, 'update']);
            Route::post('/reorder', [OrderStatusController::class, 'reorder']);
        });
    });

    Route::group(['prefix' => 'token'], function () {
        Route::get('/', [TokenController::class, 'index']);
        Route::post('/create', [TokenController::class, 'create']);
        Route::get('/show', [TokenController::class, 'show']);
        Route::put('/update/{tokenId}', [TokenController::class, 'update']);
        Route::put('/toggle/{tokenId}', [TokenController::class, 'toggle']);
        Route::put('/delete/{tokenId}', [TokenController::class, 'destroy']);
    });

    Route::group(['prefix' => 'magento'], function () {
        Route::post('/sync', [MagentoController::class, 'sync']);
    });

    Route::group(['prefix' => 'woocommerce'], function () {
        Route::post('/sync', [WooCommerceController::class, 'sync']);
    });

    Route::group(['prefix' => 'kuanto-kusta'], function () {
        Route::post('/sync', [KuantoKustaController::class, 'sync']);
    });

    Route::group(['prefix' => 'woocommerce'], function () {
        Route::post('/sync', [WooCommerceController::class, 'sync']);
    });

    Route::group(['prefix' => 'worten'], function () {
        Route::post('/sync', [WortenController::class, 'sync']);
    });

    Route::get('carriers', [CarrierController::class, 'getAll']);

    Route::group(['prefix' => 'shipping'], function () {
        Route::get('/volumes/{orderCode}', [VolumesController::class, 'show']);
        Route::get('/tracking/{orderId}', [TrackingController::class, 'show']);
        Route::patch('/tracking/{orderId}', [TrackingController::class, 'update']);

        Route::group(['prefix' => 'vasp'], function () {
            Route::post('/label', [VaspController::class, 'label']);
            Route::post('/service-types', [VaspController::class, 'serviceTypes']);
            Route::post('/pdf', [VaspController::class, 'download']);
        });

        Route::group(['prefix' => 'correos'], function () {
            Route::post('/label', [CorreosController::class, 'label']);
            Route::get('/service-types', [CorreosController::class, 'serviceTypes']);
            Route::post('/pdf', [CorreosController::class, 'download']);
        });

        Route::group(['prefix' => 'ctt'], function () {
            Route::post('/label', [CttController::class, 'label']);
            Route::get('/service-types', [CttController::class, 'serviceTypes']);
            Route::post('/pdf', [CttController::class, 'download']);
        });

        Route::group(['prefix' => 'selfshipping'], function () {
            Route::post('/label', [SelfShippingController::class, 'label']);
            Route::post('/pdf', [SelfShippingController::class, 'download']);
        });
    });

    Route::group(['prefix' => 'configuration'], function () {
        Route::group(['prefix' => 'user'], function () {
            Route::get('/users', [UserController::class, 'listAll']);
        });

        Route::group(['prefix' => 'email'], function () {
            Route::get('/available-statuses', [EmailController::class, 'getAvailableStatuses']);

            Route::get('/', [EmailController::class, 'listAll']);
            Route::get('/show/{emailId}', [EmailController::class, 'show']);
            Route::post('/update/{emailId}', [EmailController::class, 'update']);
            Route::post('/delete/{emailId}', [EmailController::class, 'delete']);
            Route::post('/create', [EmailController::class, 'create']);
        });

        Route::group(['prefix' => 'role'], function () {
            Route::get('/roles', [RoleController::class, 'listAll']);
            Route::get('/show', [RoleController::class, 'show']);
            Route::post('/update', [RoleController::class, 'update']);
            Route::post('/create', [RoleController::class, 'create']);
            Route::post('/delete', [RoleController::class, 'delete']);
        });

        Route::group(['prefix' => 'permission'], function () {
            Route::get('/permissions', [PermissionController::class, 'listAll']);
        });

        Route::group(['prefix' => 'profile'], function () {
            Route::get('/{userId}', [UserController::class, 'index']);
            Route::post('/update/{userId}', [UserController::class, 'update']);
            Route::post('/remove-image/{userId}', [UserController::class, 'removeImage']);
        });

        Route::group(['prefix' => 'account'], function () {
            Route::get('/', [AccountController::class, 'index']);
            Route::post('/update/', [AccountController::class, 'update']);
            Route::post('/remove-image/', [AccountController::class, 'removeImage']);
        });
    });
});
