<?php


use Illuminate\Support\Facades\Route;
use Lk\Classic\Http\Controllers\ClassicAppCommentController;
use Lk\Classic\Http\Controllers\ClassicEventController;
use Lk\Classic\Http\Controllers\ClassicUserApplicationController;
use Lk\Http\Controllers\AdditionalServicesController;
use Lk\Http\Controllers\AppCommentsController;
use Lk\Http\Controllers\AuthController;
use Lk\Http\Controllers\BuilderController;
use Lk\Http\Controllers\ChatController;
use Lk\Http\Controllers\EmployeeController;
use Lk\Http\Controllers\EventsController;
use Lk\Http\Controllers\HardwareController;
use Lk\Http\Controllers\InformationForPlacementController;
use Lk\Http\Controllers\MyDocumentsController;
use Lk\Http\Controllers\MyTeamController;
use Lk\Http\Controllers\NewStorageController;
use Lk\Http\Controllers\OrdersController;
use Lk\Http\Controllers\RelationController;
use Lk\Http\Controllers\SchemaOfStandController;
use Lk\Http\Controllers\StandRepresentativeController;
use Lk\Http\Controllers\StorageController;
use Lk\Http\Controllers\TimeSlotController;
use Lk\Http\Controllers\UserApplicationController;
use Lk\Http\Controllers\UserController;
use Lk\Http\Controllers\UserProfileController;
use Lk\Http\Controllers\VipGuestController;
use Lk\Http\Controllers\VisualizationCommentController;
use Lk\Http\Controllers\VisualizationController;
use Lk\Http\Controllers\WorkerController;

Route::group(['prefix' => '/auth'], function () {
    Route::post('registration', [AuthController::class, 'registration'])->name('registration');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])
        ->name('forgot-password');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])
        ->name('reset-password');

    Route::get('/get-permissions', function () {
        return auth()->check() ? auth()->user()->jsPermissions() : 0;
    });
});
Route::group(['middleware' => ['lk', 'role:participant|resident|commission']], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::delete('/user/delete', [UserController::class, 'delete']);
    Route::post('change-password', [AuthController::class, 'changePassword'])->name('change-password');
    Route::prefix('me')->group(function () {
        Route::get('/', [UserController::class, 'me'])->name("users.show");
        Route::patch('/', [UserController::class, 'update'])->name("users.update");
        Route::get('/profile', [UserProfileController::class, 'show'])->name('user-profile.show');
        Route::post('/profile', [UserProfileController::class, 'store'])->name('user-profile.store');
        Route::patch('/profile', [UserProfileController::class, 'update'])->name('user-profile.update');
    });

    Route::prefix('order')->group(function () {
        Route::get('/list', [OrdersController::class, 'list'])->name('order.index');
        Route::patch('/update/{id}', [OrdersController::class, 'update'])->name('order.update');
        Route::get('/{id}', [OrdersController::class, 'show'])->name('order.show');
    });

    Route::prefix('additional-service')->group(function () {
        Route::post('/', [AdditionalServicesController::class, 'store'])->name('additional-service.store');
        Route::get('/list', [AdditionalServicesController::class, 'list'])->name('additional-service.index');
        Route::get('/{id}', [AdditionalServicesController::class, 'show'])->name('additional-service.show');
        Route::patch('/update/{id}', [AdditionalServicesController::class, 'update'])
            ->name('additional-service.update');
    });

    Route::prefix('hardware')->group(function () {
        Route::post('/', [HardwareController::class, 'store'])->name('hardware.store');
        Route::get('/list', [HardwareController::class, 'list'])->name('hardware.index');
        Route::get('/{id}', [HardwareController::class, 'show'])->name('hardware.show');
        Route::patch('/update/{id}', [HardwareController::class, 'update'])->name('hardware.update');
    });


    Route::prefix('slot')->group(function () {
        Route::get('getlots/check_in', [TimeSlotController::class, 'getCheckInSlots']);
        Route::get('getlots/exit', [TimeSlotController::class, 'getExitSlots']);
    });

    Route::prefix('/chat')->group(function () {
        Route::prefix('/message')->group(function () {
            Route::post('/store', [ChatController::class, 'newMessage'])
                ->name('chat.store');
            Route::get('/all', [ChatController::class, 'messages'])
                ->name('chat.messages');
            Route::get('/list', [ChatController::class, 'messagesByLimit'])
                ->name('chat.messages.by-limit');
            Route::get('/show', [ChatController::class, 'show'])
                ->name('chat.show');
            Route::patch('/update', [ChatController::class, 'update'])
                ->name('chat.update');
            Route::delete('/delete', [ChatController::class, 'delete'])
                ->name('chat.delete');
            Route::get('/search', [ChatController::class, 'search'])
                ->name('chat.message.search');
            Route::patch('/status', [ChatController::class, 'status'])
                ->name('chat.message.status');
        });
        Route::get('/manager/show', [ChatController::class, 'manager'])
            ->name('chat.manager.show');
    });

    Route::prefix('/my-documents')->group(function () {
        Route::post('/store', [MyDocumentsController::class, 'store'])
            ->name('my-documents.store');
        Route::get('/show', [MyDocumentsController::class, 'show'])
            ->name('my-documents.show');
        Route::get('/agreement', [MyDocumentsController::class, 'agreementFile'])
            ->name('my-documents.agreement');
        Route::patch('/update', [MyDocumentsController::class, 'update'])
            ->name('my-documents.update');
        Route::patch('/delete-file', [MyDocumentsController::class, 'deleteFile'])
            ->name('my-documents.delete-file');
    });

    Route::prefix('/my-team')->group(function () {
        Route::post('/store', [MyTeamController::class, 'store'])
            ->name('my-team.store');
        Route::get('/show', [MyTeamController::class, 'show'])
            ->name('my-team.show');
        Route::patch('/update', [MyTeamController::class, 'update'])
            ->name('my-team.update');
        Route::delete('/delete', [MyTeamController::class, 'delete'])
            ->name('my-team.delete');

        Route::prefix('/builder')->group(function () {
            Route::post('/store', [BuilderController::class, 'store'])
                ->name('my-team.builder.store');
            Route::get('/show', [BuilderController::class, 'show'])
                ->name('my-team.builder.show');
            Route::patch('/update', [BuilderController::class, 'update'])
                ->name('my-team.builder.update');
            Route::delete('/delete', [BuilderController::class, 'delete'])
                ->name('my-team.builder.delete');
        });

        Route::prefix('/stand-representative')->group(function () {
            Route::post('/store', [StandRepresentativeController::class, 'store'])
                ->name('my-team.stand-representative.store');
            Route::get('/show', [StandRepresentativeController::class, 'show'])
                ->name('my-team.stand-representative.show');
            Route::patch('/update', [StandRepresentativeController::class, 'update'])
                ->name('my-team.stand-representative.update');
            Route::delete('/delete', [StandRepresentativeController::class, 'delete'])
                ->name('my-team.stand-representative.delete');
        });
    });

    Route::get("/schema-of-stand/show", [SchemaOfStandController::class, 'show'])->name("schema.show");
});

Route::get('/getstatus', [UserApplicationController::class, 'getStatus']);
Route::prefix('/applications')->group(function () {
    Route::get('/', [UserApplicationController::class, 'index'])->name('user-applications.index');
    Route::get('/{id}', [UserApplicationController::class, 'show'])->name('user-applications.show');
    Route::post('/', [UserApplicationController::class, 'store'])->name('user-applications.store');
    Route::patch('/{id}', [UserApplicationController::class, 'update'])->name('user-applications.update');
    Route::group(['prefix' => '{id}/comment'], function () {
        Route::get('/', [AppCommentsController::class, 'list'])->name('app-comments.list');
        Route::get('/{comment_id?}', [AppCommentsController::class, 'show'])->name('app-comments.show');
    });
    Route::group(['prefix' => '/visualization'], function () {
        Route::post('/store', [VisualizationController::class, 'store'])->name('app-visualization.store');
        Route::get('/show', [VisualizationController::class, 'show'])->name('app-visualization.show');
        Route::patch('/update ', [VisualizationController::class, 'update'])->name('app-visualization.update');
        Route::delete('/delete ', [VisualizationController::class, 'delete'])->name('app-visualization.delete');
        Route::patch('/image/delete ', [VisualizationController::class, 'deleteImage'])
            ->name('app-visualization.deleteImage');
        Route::get('/list', [VisualizationController::class, 'list'])->name('app-visualization.list');
    });

    Route::group(['prefix' => '/information-placement'], function () {
        Route::post('/store', [InformationForPlacementController::class, 'store'])
            ->name('information-placement.store');
        Route::get('/show', [InformationForPlacementController::class, 'show'])
            ->name('information-placement.show');
        Route::get('/list', [InformationForPlacementController::class, 'list'])
            ->name('information-placement.list');
        Route::patch('/update', [InformationForPlacementController::class, 'update'])
            ->name('information-placement.update');
        Route::delete('/delete', [InformationForPlacementController::class, 'delete'])
            ->name('information-placement.delete');
        Route::patch('/image/delete', [InformationForPlacementController::class, 'deleteImage'])
            ->name('information-placement.deleteImage');
    });

    Route::group(['prefix' => '/visualization-comment'], function () {
        Route::get('/show', [VisualizationCommentController::class, 'show'])->name('visualization-comment.show');
        Route::get('/list', [VisualizationCommentController::class, 'list'])->name('visualization-comment.list');
    });
});
Route::prefix('vip-guest')->group(function () {
    Route::get('/all', [VipGuestController::class, 'list'])->name('vip-guests.index');
    Route::get('/{id}', [VipGuestController::class, 'show'])->name('vip-guests.show');
    Route::post('/store', [VipGuestController::class, 'store'])->name('vip-guests.store');
    Route::patch('/update/{id}', [VipGuestController::class, 'update'])->name('vip-guests.update');
    Route::delete('/delete', [VipGuestController::class, 'delete'])->name('vip-guests.delete');
});

Route::prefix('worker')->group(function () {
    Route::post('/', [WorkerController::class, 'store'])->name('worker.store');
    Route::get('/list', [WorkerController::class, 'list'])->name('worker.index');
    Route::get('/{id}', [WorkerController::class, 'show'])->name('worker.show');
    Route::patch('/update/{id}', [WorkerController::class, 'update'])->name('worker.update');
});

Route::prefix('employee')->group(function () {
    Route::post('/', [EmployeeController::class, 'store'])->name('employee.store');
    Route::get('/list', [EmployeeController::class, 'list'])->name('employee.index');
    Route::get('/{id}', [EmployeeController::class, 'show'])->name('employee.show');
    Route::patch('/update/{id}', [EmployeeController::class, 'update'])->name('employee.update');
});

Route::prefix('event')->group(function () {
    Route::get('/', [EventsController::class, 'searchEvent'])->name('event.searchEvent');
    Route::get('/slots', [EventsController::class, 'slots'])->name('event.slots');
});

Route::post('/storage/upload', [StorageController::class, 'upload'])->withoutMiddleware('force_json');
Route::post('/storage/uploads', [NewStorageController::class, 'upload'])
    ->withoutMiddleware('force_json');


Route::get(
    '/{entity}/{id}/relationships/{relation}',
    [RelationController::class, 'relationships']
)->name('relationships.list');

Route::get(
    '/{entity}/{id}/{relation}',
    [RelationController::class, 'relations']
)->name('relations.list');

Route::post(
    '/{entity}/{id}/relationships/{relation}',
    [RelationController::class, 'attach']
)->name('relationships.attach');

Route::patch(
    '/{entity}/{id}/relationships/{relation}',
    [RelationController::class, 'sync']
)->name('relationships.sync');

Route::delete(
    '/{entity}/{id}/relationships/{relation}',
    [RelationController::class, 'detach']
)->name('relationships.detach');

/**
 * Classic event routes
 */

Route::group(['middleware' => ['lk', 'role:participant|resident|commission']], function () {
    Route::prefix('/classic/')->group(function () {
        Route::prefix('/event')->group(function () {
            Route::get('/', [ClassicEventController::class, 'searchEvent'])->name('classic.event.searchEvent');
            Route::get('/slots', [ClassicEventController::class, 'slots'])->name('event.slots');
        });

        Route::get('/applications', [ClassicUserApplicationController::class, 'index'])->name('classic-user-applications.index');
        Route::get('/getStatus', [ClassicUserApplicationController::class, 'status'])->name('classic-user-applications.status');

        Route::prefix('/application')->group(function () {
            Route::get('/', [ClassicUserApplicationController::class, 'show'])->name('classic-user-applications.show');
            Route::post('/store', [ClassicUserApplicationController::class, 'store'])->name('classic-user-applications.store');
            Route::patch('/update', [ClassicUserApplicationController::class, 'update'])->name('classic-user-applications.update');
            Route::group(['prefix' => '/{id}/comment'], function () {
                Route::get('/', [ClassicAppCommentController::class, 'list'])->name('classic-app-comments.list');
                Route::get('/{comment_id?}', [ClassicAppCommentController::class, 'show'])->name('classic-app-comments.show');
            });
        });
    });
});

