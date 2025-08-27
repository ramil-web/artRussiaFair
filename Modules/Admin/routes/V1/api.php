<?php

use Admin\Classic\Http\Controllers\ClassicAppCommentController;
use Admin\Classic\Http\Controllers\ClassicCommissionAssessmentController;
use Admin\Classic\Http\Controllers\ClassicEventController;
use Admin\Classic\Http\Controllers\ClassicUserApplicationController;
use Admin\Http\Controllers\AppCommentsController;
use Admin\Http\Controllers\ArtistController;
use Admin\Http\Controllers\AuthController;
use Admin\Http\Controllers\CategoryProductController;
use Admin\Http\Controllers\ChatController;
use Admin\Http\Controllers\CommissionAssessmentsController;
use Admin\Http\Controllers\CuratorController;
use Admin\Http\Controllers\EmployeeController;
use Admin\Http\Controllers\EventsController;
use Admin\Http\Controllers\GalleryController;
use Admin\Http\Controllers\InformationForPlacementController;
use Admin\Http\Controllers\ManagerController;
use Admin\Http\Controllers\ManagerProfileController as ManagerProfile;
use Admin\Http\Controllers\MyDocumentController as DocumentController;
use Admin\Http\Controllers\MyTeamController;
use Admin\Http\Controllers\OrdersController;
use Admin\Http\Controllers\PartnerCategoryController;
use Admin\Http\Controllers\PartnerController;
use Admin\Http\Controllers\PermissionController;
use Admin\Http\Controllers\PhotographerController;
use Admin\Http\Controllers\ProductController;
use Admin\Http\Controllers\ProgramController;
use Admin\Http\Controllers\ProjectTeamController;
use Admin\Http\Controllers\RelationController;
use Admin\Http\Controllers\RemoveParticipantImageController;
use Admin\Http\Controllers\RoleController;
use Admin\Http\Controllers\SchemaOfStandController;
use Admin\Http\Controllers\SculptorController;
use Admin\Http\Controllers\ServiceCatalogController;
use Admin\Http\Controllers\SpeakersController;
use Admin\Http\Controllers\StorageController;
use Admin\Http\Controllers\TimeSlotController;
use Admin\Http\Controllers\UserApplicationController;
use Admin\Http\Controllers\UserController;
use Admin\Http\Controllers\UserProfileController as UserProfile;
use Admin\Http\Controllers\VacancyController;
use Admin\Http\Controllers\VipGuestController;
use Admin\Http\Controllers\VisualizationAssessmentController;
use Admin\Http\Controllers\VisualizationCommentController;
use Admin\Http\Controllers\WorkerController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])
        ->name('forgot-password');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->withoutMiddleware('force_json')
        ->name('reset-password');

    Route::get('/get-permissions', function () {
        return auth()->check() ? auth()->user()->jsPermissions() : 0;
    });

});

#Роуты которые доступны без авторизации
Route::get('/speaker/all', [SpeakersController::class, 'list'])->name('speaker.index');
Route::get('/partner/all', [PartnerController::class, 'list'])->name('partner.index');
Route::get('/project-team/all', [ProjectTeamController::class, 'list'])->name('project-team.index');
Route::get('/curator/all', [CuratorController::class, 'list'])->name('curator.index');

Route::group(['middleware' => 'admin'], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('change-password', [AuthController::class, 'changePassword'])->name('change-password');

    Route::patch(
        'participant-images/delete/{id}',
        [RemoveParticipantImageController::class, 'delete']
    )->name('participant-image.delete');

    /**
     * Доступно супер админу и менеджерам
     */
    Route::group(['middleware' => ['role:super_admin|manager']], function () {
        Route::prefix('slot')->group(function () {
            Route::post('/timeslot', [TimeSlotController::class, 'index']);
            Route::get('/interval', [TimeSlotController::class, 'get']);
            Route::get('/export', [TimeSlotController::class, 'export']);
            Route::patch('/interval/update', [TimeSlotController::class, 'update']);
        });

        Route::prefix('order')->group(function () {
            Route::get('/export', [OrdersController::class, 'export']);
            Route::get('/list', [OrdersController::class, 'list'])->name('order.index');
            Route::patch('/update/{id}', [OrdersController::class, 'update'])->name('order.update');
            Route::get('/{id}', [OrdersController::class, 'show'])->name('order.show');
        });

        Route::prefix('speaker')->group(function () {
            Route::get('/{id}', [SpeakersController::class, 'show'])->name('speaker.show');
            Route::post('/store', [SpeakersController::class, 'store'])->name('speaker.store');
            Route::patch('/update/{id}', [SpeakersController::class, 'update'])->name('speaker.update');
            Route::delete('/delete/{id}', [SpeakersController::class, 'delete'])->name('speaker.delete');
            Route::delete('/archive/{id}', [SpeakersController::class, 'archive'])->name('speaker.archive');
            Route::patch('/restore/{id}', [SpeakersController::class, 'restore'])->name('speaker.restore');
        });

        Route::prefix('project-team')->group(function () {
            Route::get('/{id}', [ProjectTeamController::class, 'show'])->name('project-team.show');
            Route::post('/store', [ProjectTeamController::class, 'store'])->name('project-team.store');
            Route::patch('/update/{id}', [ProjectTeamController::class, 'update'])->name('project-team.update');
            Route::delete('/delete/{id}', [ProjectTeamController::class, 'delete'])->name('project-team.delete');
            Route::delete('/archive/{id}', [ProjectTeamController::class, 'archive'])->name('project-team.archive');
            Route::patch('/restore/{id}', [ProjectTeamController::class, 'restore'])->name('project-team.restore');
        });


        Route::prefix('curator')->group(function () {
            Route::get('/{id}', [CuratorController::class, 'show'])->name('curator.show');
            Route::post('/store', [CuratorController::class, 'store'])->name('curator.store');
            Route::patch('/update/{id}', [CuratorController::class, 'update'])->name('curator.update');
            Route::delete('/delete/{id}', [CuratorController::class, 'delete'])->name('curator.delete');
            Route::delete('/archive/{id}', [CuratorController::class, 'archive'])->name('curator.archive');
            Route::patch('/restore/{id}', [CuratorController::class, 'restore'])->name('curator.restore');
        });

        Route::prefix('artist')->group(function () {
            Route::get('/all', [ArtistController::class, 'list'])->name('artist.index');
            Route::get('/{id}', [ArtistController::class, 'show'])->name('artist.show');
            Route::post('/store', [ArtistController::class, 'store'])->name('artist.store');
            Route::patch('/update/{id}', [ArtistController::class, 'update'])->name('artist.update');
            Route::delete('/delete/{id}', [ArtistController::class, 'delete'])->name('artist.delete');
            Route::delete('/archive/{id}', [ArtistController::class, 'archive'])->name('artist.archive');
            Route::patch('/restore/{id}', [ArtistController::class, 'restore'])->name('artist.restore');
        });

        Route::prefix('sculptor')->group(function () {
            Route::get('/all', [SculptorController::class, 'list'])->name('sculptor.index');
            Route::get('/{id}', [SculptorController::class, 'show'])->name('sculptor.show');
            Route::post('/store', [SculptorController::class, 'store'])->name('sculptor.store');
            Route::patch('/update/{id}', [SculptorController::class, 'update'])->name('sculptor.update');
            Route::delete('/delete/{id}', [SculptorController::class, 'delete'])->name('sculptor.delete');
            Route::delete('/archive/{id}', [SculptorController::class, 'archive'])->name('sculptor.archive');
            Route::patch('/restore/{id}', [SculptorController::class, 'restore'])->name('sculptor.restore');
        });

        Route::prefix('photographer')->group(function () {
            Route::get('/all', [PhotographerController::class, 'list'])->name('photographer.index');
            Route::get('/{id}', [PhotographerController::class, 'show'])->name('photographer.show');
            Route::post('/store', [PhotographerController::class, 'store'])->name('photographer.store');
            Route::patch('/update/{id}', [PhotographerController::class, 'update'])->name('photographer.update');
            Route::delete('/delete/{id}', [PhotographerController::class, 'delete'])->name('photographer.delete');
            Route::delete('/archive/{id}', [PhotographerController::class, 'archive'])->name('photographer.archive');
            Route::patch('/restore/{id}', [PhotographerController::class, 'restore'])->name('photographer.restore');
        });

        Route::prefix('gallery')->group(function () {
            Route::get('/all', [GalleryController::class, 'list'])->name('gallery.index');
            Route::get('/{id}', [GalleryController::class, 'show'])->name('gallery.show');
            Route::post('/store', [GalleryController::class, 'store'])->name('gallery.store');
            Route::patch('/update/{id}', [GalleryController::class, 'update'])->name('gallery.update');
            Route::delete('/delete/{id}', [GalleryController::class, 'delete'])->name('gallery.delete');
            Route::delete('/archive/{id}', [GalleryController::class, 'archive'])->name('gallery.archive');
            Route::patch('/restore/{id}', [GalleryController::class, 'restore'])->name('gallery.restore');
        });

        Route::prefix('partner')->group(function () {
            Route::get('/{id}', [PartnerController::class, 'show'])->name('partner.show');
            Route::post('/store', [PartnerController::class, 'store'])->name('partner.store');
            Route::patch('/update/{id}', [PartnerController::class, 'update'])->name('partner.update');
            Route::delete('/delete/{id}', [PartnerController::class, 'delete'])->name('partner.delete');
            Route::delete('/archive/{id}', [PartnerController::class, 'archive'])->name('partner.archive');
            Route::patch('/restore/{id}', [PartnerController::class, 'restore'])->name('partner.restore');
        });

        Route::prefix('partner-category')->group(function () {
            Route::get('/all', [PartnerCategoryController::class, 'list'])->name('partner-category.index');
            Route::post('/store', [PartnerCategoryController::class, 'store'])->name('partner-category.store');
            Route::get('/{id}', [PartnerCategoryController::class, 'show'])->name('partner-category.show');
            Route::patch('/update/{id}', [PartnerCategoryController::class, 'update'])->name('partner-category.update');
            Route::delete('/delete/{id}', [PartnerCategoryController::class, 'delete'])
                ->name('partner-category.delete');
            Route::delete('/archive/{id}', [PartnerCategoryController::class, 'archive'])
                ->name('partner-category.archive');
            Route::patch('/restore/{id}', [PartnerCategoryController::class, 'restore'])
                ->name('partner-category.restore');
        });

        Route::prefix('program')->group(function () {
            Route::get('/all', [ProgramController::class, 'list'])->name('program.index');
            Route::post('/store', [ProgramController::class, 'store'])->name('program.store');
            Route::get('/{id}', [ProgramController::class, 'show'])->name('program.show');
            Route::patch('/update/{id}', [ProgramController::class, 'update'])->name('program.update');
            Route::delete('/delete/{id}', [ProgramController::class, 'delete'])->name('program.delete');
            Route::delete('/archive/{id}', [ProgramController::class, 'archive'])->name('program.archive');
            Route::patch('/restore/{id}', [ProgramController::class, 'restore'])->name('program.restore');
        });


        Route::prefix('vip-guest')->group(function () {
            Route::get('/export', [VipGuestController::class, 'export']);
            Route::get('/all', [VipGuestController::class, 'list'])->name('vip-guest.index');
            Route::get('/{id}', [VipGuestController::class, 'show'])->name('vip-guest.show');
        });

        Route::prefix('worker')->group(function () {
            Route::get('/export', [WorkerController::class, 'export']);
            Route::get('/list', [WorkerController::class, 'list'])->name('worker.index');
            Route::get('/{id}', [WorkerController::class, 'show'])->name('worker.show');
        });

        Route::prefix('employee')->group(function () {
            Route::get('/export', [EmployeeController::class, 'export']);
            Route::get('/list', [EmployeeController::class, 'list'])->name('employee.index');
            Route::get('/{id}', [EmployeeController::class, 'show'])->name('employee.show');
        });

        Route::prefix('schema-of-stand')->group(function () {
            Route::post("/store", [SchemaOfStandController::class, "store"])->name("schema.store");
            Route::delete("/delete", [SchemaOfStandController::class, "delete"])->name("schema.delete");
            Route::get("/show", [SchemaOfStandController::class, "show"])->name("schema.show");
        });

        Route::prefix('/my-team')->group(function () {
            Route::get("/list", [MyTeamController::class, "list"])->name("my-team.list");
            Route::get("/export", [MyTeamController::class, "export"])->name("my-team.export");
        });

        Route::prefix('/information-placement')->group(function () {
            Route::get('/list', [InformationForPlacementController::class, 'list'])->name('information-for-replacement');
        });
    });

    Route::group(['middleware' => ['role:super_admin|manager|commission']], function () {
        Route::post('/storage/upload', [StorageController::class, 'upload']);
        Route::post('/storage/doc', [StorageController::class, 'uploadDoc'])->name('admin-document.upload');
        Route::patch('/storage/doc/reload', [StorageController::class, 'reload'])->name('admin-document.reload');
        Route::get('/get/docs', [StorageController::class, 'getAdminDocs']);
        Route::delete('/doc/delete', [StorageController::class, 'deleteDoc']);
        Route::delete('/storage/file/delete', [StorageController::class, 'deleteFile']);

        Route::prefix('me')->group(function () {
            Route::get('/', [ManagerController::class, 'me']);
            Route::patch('/', [ManagerController::class, 'updateSelf']);
            Route::get('/profile', [ManagerProfile::class, 'showSelf']);
            Route::post('/profile', [ManagerProfile::class, 'store']);
            Route::patch('/profile', [ManagerProfile::class, 'update']);
        });

        Route::group(['middleware' => ['role:super_admin|manager']], function () {
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
            Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
            Route::patch('/users/{id}', [UserController::class, 'update']);
            Route::delete('/users/{id}/archive', [UserController::class, 'archive']);
            Route::patch('/users/{id}/restore', [UserController::class, 'restore']);
            Route::delete('/users/{id}', [UserController::class, 'destroy']);

            Route::get('/users/{id}/profile', [UserProfile::class, 'show'])->name('user-profile.show');
            Route::group(['prefix' => 'applications'], function () {
                Route::get('/', [UserApplicationController::class, 'index'])->name('user-applications.index');
                Route::get('/{id}', [UserApplicationController::class, 'show'])->name('user-applications.show');
            });
        });
        Route::group(['prefix' => 'roles'], function () {
            Route::get('/', [RoleController::class, 'index'])->name('roles.index');
            Route::post('/', [RoleController::class, 'store'])->name('roles.store');
            Route::get('/{id}', [RoleController::class, 'show'])->name('roles.show');
            Route::patch('/{id}', [RoleController::class, 'update'])->name('roles.update');
            Route::delete('/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
        });
        Route::group(['prefix' => 'permissions'], function () {
            Route::get('/', [PermissionController::class, 'index'])->name('permissions.index');
            Route::post('/', [PermissionController::class, 'store'])->name('permissions.store');
            Route::get('/{id}', [PermissionController::class, 'show'])->name('permissions.show');
            Route::patch('/{id}', [PermissionController::class, 'update'])->name('permissions.update');
            Route::delete('/{id}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
        });


        Route::group(['middleware' => ['role:super_admin']], function () {
            Route::get('/managers', [ManagerController::class, 'index'])->name('manager.index');
            Route::post('/manager/store', [ManagerController::class, 'store'])->name('manager.store');
            Route::get('/managers/{id}', [ManagerController::class, 'show'])->name('manager.show');
            Route::patch('/managers/{id}', [ManagerController::class, 'update'])->name('manager.update');
            Route::delete('/managers/{id}/archive', [ManagerController::class, 'softDelete'])->name(
                'manager.soft-delete'
            );
            Route::delete('/participant/delete', [AuthController::class, 'destroy'])->name('participant.delete');
            Route::get('/managers/{id}/restore', [ManagerController::class, 'restore'])->name('manager.restore');
            Route::delete('/managers/{id}', [ManagerController::class, 'destroy'])->name('manager.destroy');
            Route::get('/managers/{id}/profile', [ManagerProfile::class, 'show'])->name('manager-profile.show');
        });

        Route::group(['prefix' => 'events', 'middleware' => ['role:super_admin|manager']], function () {
            Route::get('/', [EventsController::class, 'index'])->name('events.index');
            Route::post('/', [EventsController::class, 'store'])->name('events.store');
            Route::get('/show', [EventsController::class, 'show'])->name('events.show');
            Route::get('/slots', [EventsController::class, 'slots'])->name('events.slots');
            Route::patch('/{id}', [EventsController::class, 'update'])->name('events.update');
            Route::delete('/{id}', [EventsController::class, 'destroy'])->name('events.destroy');
            Route::delete('/archive/{id}', [EventsController::class, 'archive'])->name('events.archive');
            Route::patch('/restore/{id}', [EventsController::class, 'restore'])->name('events.restore');
            Route::get('/copy', [EventsController::class, 'copyData']);
            Route::post('/add_bar_codes', [EventsController::class, 'addBarCodes']);
        });
        Route::group(
            ['prefix' => 'applications', 'middleware' => ['role:super_admin|manager|commission']],
            function () {
                Route::patch('/visitor', [UserApplicationController::class, 'visitor'])
                    ->name('user-applications.visitor');
                Route::get('/', [UserApplicationController::class, 'index'])
                    ->name('user-applications.index');
                Route::post('/', [UserApplicationController::class, 'store'])
                    ->name('user-applications.store');
                Route::get('/{id}', [UserApplicationController::class, 'show'])
                    ->name('user-applications.show');
                Route::patch('/{id}', [UserApplicationController::class, 'update'])
                    ->name('user-applications.update');
                Route::delete('/{id}', [UserApplicationController::class, 'destroy'])
                    ->name('user-applications.destroy');
                Route::group(['prefix' => '{id}/comment'], function () {
                    Route::get('/', [AppCommentsController::class, 'list'])
                        ->name('app-comments.list');
                    Route::post('/', [AppCommentsController::class, 'store'])
                        ->name('app-comments.store');
                    Route::get('/{comment_id?}', [AppCommentsController::class, 'show'])
                        ->name('app-comments.show');
                    Route::patch('/{comment_id}', [AppCommentsController::class, 'update'])
                        ->name('app-comments.update');
                    Route::delete('/{comment_id}', [AppCommentsController::class, 'destroy'])
                        ->name('app-comment.destroy');
                });
                Route::group(['prefix' => '{id}/assessment'], function () {
                    Route::get('/', [CommissionAssessmentsController::class, 'list'])
                        ->name('commission-assessments.list');
                    Route::post('/', [CommissionAssessmentsController::class, 'store'])
                        ->name('commission-assessments.store');
                    Route::get('/{assessment_id?}', [CommissionAssessmentsController::class, 'show'])
                        ->name('commission-assessments.show');
                    Route::patch('/{assessment_id}', [CommissionAssessmentsController::class, 'update'])
                        ->name('commission-assessments.update');
                    Route::delete('/{assessment_id}', [CommissionAssessmentsController::class, 'destroy'])
                        ->name('commission-assessments.destroy');
                })->middleware('role:commission');

                Route::prefix('visualization-assessment')->group(function () {
                    Route::post("/store", [VisualizationAssessmentController::class, 'store'])
                        ->name('visualization-assessment.store');
                    Route::get("/show", [VisualizationAssessmentController::class, 'show'])
                        ->name('visualization-assessment.show');
                    Route::get("/list", [VisualizationAssessmentController::class, 'list'])
                        ->name('visualization-assessment.list');
                    Route::patch("/update", [VisualizationAssessmentController::class, 'update'])
                        ->name('visualization-assessment.update');
                    Route::delete("/delete", [VisualizationAssessmentController::class, 'delete'])
                        ->name('visualization-assessment.delete');
                });

                Route::prefix('visualization-comment')->group(function () {
                    Route::post("/store", [VisualizationCommentController::class, 'store'])
                        ->name('visualization-comment.store');
                    Route::get("/show", [VisualizationCommentController::class, 'show'])
                        ->name('visualization-comment.show');
                    Route::get("/list", [VisualizationCommentController::class, 'list'])
                        ->name('visualization-comment.list');
                    Route::patch("/update", [VisualizationCommentController::class, 'update'])
                        ->name('visualization-comment.update');
                    Route::delete("/delete", [VisualizationCommentController::class, 'delete'])
                        ->name('visualization-comment.delete');
                });
            });

        Route::group(
            ['prefix' => '/application', 'middleware' => ['role:super_admin|manager|commission']],
            function () {
                Route::get('/export', [UserApplicationController::class, 'export'])
                    ->name('user-applications.export');
            });


        Route::group(['prefix' => 'service-catalogs'], function () {
            Route::post('/', [ServiceCatalogController::class, 'store'])->name('service-catalogs.store');
            Route::patch('/{id}', [ServiceCatalogController::class, 'update'])->name('service-catalogs.update');
            Route::delete('/{id}', [ServiceCatalogController::class, 'destroy'])->name('service-catalogs.destroy');
            Route::delete('/archive/{id}', [ServiceCatalogController::class, 'archive'])
                ->name('service-catalogs.archive');
            Route::get('/', [ServiceCatalogController::class, 'list'])->name('service-catalogs.index');
            Route::get('/{id}', [ServiceCatalogController::class, 'show'])->name('service-catalogs.show');
            Route::patch('/restore/{id}', [ServiceCatalogController::class, 'restore'])
                ->name('service-catalogs.restore');
        });

        Route::prefix('category-products')->group(function () {
            Route::post('/', [CategoryProductController::class, 'store'])->name('category-products.store');
            Route::patch('/{id}', [CategoryProductController::class, 'update'])->name('category-products.update');
            Route::delete('/{id}', [CategoryProductController::class, 'destroy'])->name('category-products.destroy');
            Route::delete('/archive/{id}', [CategoryProductController::class, 'archive'])
                ->name('category-products.archive');
            Route::get('/', [CategoryProductController::class, 'list'])->name('category-products.index');
            Route::get('/{id}', [CategoryProductController::class, 'show'])->name('category-products.show');
            Route::patch('/restore/{id}', [CategoryProductController::class, 'restore'])
                ->name('category-products.restore');
        });

        Route::prefix('products')->group(function () {
            Route::get('/all', [ProductController::class, 'list'])->name('products.index');
            Route::post('/', [ProductController::class, 'store'])->name('products.store');
            Route::get('/', [ProductController::class, 'show'])->name('products.show');
            Route::patch('/{id}', [ProductController::class, 'update'])->name('products.update');
            Route::delete('{id}', [ProductController::class, 'destroy'])->name('products.destroy');
            Route::delete('/archive/{id}', [ProductController::class, 'archive'])->name('products.archive');
            Route::patch('/restore/{id}', [ProductController::class, 'restore'])->name('products.restore');
        });

        Route::get('/{entity}/{id}/relationships/{relation}', [RelationController::class, 'relationships'])
            ->name('relationships.list');

        Route::get(
            '/{entity}/{id}/{relation}',
            [RelationController::class, 'relations']
        )->name('relations.list');

        Route::post(
            '/{entity}/{id}/relationships/{relation}',
            [RelationController::class, 'attach']
        )->name('relationships.attach');


        Route::patch('/{entity}/{id}/relationships/{relation}', [RelationController::class, 'sync'])
            ->name('relationships.sync');

        Route::delete(
            '/{entity}/{id}/relationships/{relation}',
            [RelationController::class, 'detach']
        )->name('relationships.detach');

        Route::prefix('/my-documents')->group(function () {
            Route::post('/agreement/upload', [DocumentController::class, 'upload'])
                ->name('my-documents.agreement.upload');
            Route::get('/agreements', [DocumentController::class, 'agreements'])
                ->name('my-documents.agreements');
            Route::delete('/agreement/delete', [DocumentController::class, 'delete'])
                ->name('my-documents.agreement.delete');
            Route::get('/show', [DocumentController::class, 'show'])
                ->name('my-documents.show');
            Route::get('/list', [DocumentController::class, 'list'])
                ->name('my-documents.index');
        });

        Route::prefix('/chat')->group(function () {
            Route::get('/participants', [ChatController::class, 'chatParticipants'])->name('admin.chat.participants');
            Route::get('/messages', [ChatController::class, 'messages'])->name('admin.chat.messages');
            Route::get('/messages/by-limit', [ChatController::class, 'messagesByLimit'])->name('admin.chat.messages.by-limit');
            Route::post('/store', [ChatController::class, 'newMessage'])->name('chat.store');
            Route::get('/show', [ChatController::class, 'show'])->name('chat.show');
            Route::patch('/update', [ChatController::class, 'update'])->name('chat.update');
            Route::delete('/delete', [ChatController::class, 'delete'])->name('chat.delete');
            Route::get('/search', [ChatController::class, 'search'])->name('chat.search');
            Route::patch('/status', [ChatController::class, 'status'])->name('chat.status');
        });

        Route::prefix('/vacancy')->group(function () {
            Route::post('/store', [VacancyController::class, 'store'])->name('vacancy.create');
            Route::get('/show', [VacancyController::class, 'show'])->name('vacancy.show');
            Route::get('/list', [VacancyController::class, 'list'])->name('vacancy.list');
            Route::patch('/update', [VacancyController::class, 'update'])->name('vacancy.update');
            Route::delete('/destroy', [VacancyController::class, 'destroy'])->name('vacancy.delete');
        });
    });

    /**
     * Classic event
     */

    Route::group(['prefix' => '/classic', 'middleware' => ['role:super_admin|manager|commission']], function () {

        Route::group(['middleware' => ['role:super_admin|manager']], function () {
            Route::get('/events', [ClassicEventController::class, 'list'])->name('classic-event.index');
        });

        Route::group(['prefix' => '/event', 'middleware' => ['role:super_admin|manager']], function () {
            Route::get('/', [ClassicEventController::class, 'show'])->name('classic-event.show');
            Route::post('/store', [ClassicEventController::class, 'store'])->name('classic-event.store');
            Route::patch('/update', [ClassicEventController::class, 'update'])->name('classic-event.update');
            Route::delete('/delete', [ClassicEventController::class, 'destroy'])->name('classic-event.destroy');
            Route::delete('/archive', [ClassicEventController::class, 'archive'])->name('classic-event.archive');
            Route::patch('/restore', [ClassicEventController::class, 'restore'])->name('classic-event.restore');
        });

        Route::get('/applications', [ClassicUserApplicationController::class, 'list'])->name('classic-user-application.index');
        Route::group(
            ['prefix' => 'application', 'middleware' => ['role:super_admin|manager|commission']],
            function () {

                Route::get('/', [ClassicUserApplicationController::class, 'show'])->name('classic-user-applications.show');

                Route::group(['middleware' => ['role:super_admin|manager']], function () {
                    Route::patch('/update', [ClassicUserApplicationController::class, 'update'])->name('classic-user-applications.update');
                });


                Route::group(['prefix' => '/{id}/comment'], function () {
                    Route::get('/', [ClassicAppCommentController::class, 'list'])
                        ->name('classic-app-comments.list');
                    Route::post('/', [ClassicAppCommentController::class, 'store'])
                        ->name('classic-app-comments.store');
                    Route::get('/{comment_id?}', [ClassicAppCommentController::class, 'show'])
                        ->name('classic-app-comments.show');
                    Route::patch('/{comment_id}', [ClassicAppCommentController::class, 'update'])
                        ->name('classic-app-comments.update');
                    Route::delete('/{comment_id}', [ClassicAppCommentController::class, 'destroy'])
                        ->name('classic-app-comment.destroy');
                });

                Route::group(['prefix' => '/{id}/assessment'], function () {
                    Route::get('/list', [ClassicCommissionAssessmentController::class, 'list'])
                        ->name('classic-commission-assessments.list');
                    Route::post('/', [ClassicCommissionAssessmentController::class, 'store'])
                        ->name('classic-commission-assessments.store');
                    Route::get('/{assessment_id?}', [ClassicCommissionAssessmentController::class, 'show'])
                        ->name('classic-commission-assessments.show');
                    Route::patch('/{assessment_id}', [ClassicCommissionAssessmentController::class, 'update'])
                        ->name('classic-commission-assessments.update');
                    Route::delete('/{assessment_id}', [ClassicCommissionAssessmentController::class, 'destroy'])
                        ->name('classic-commission-assessments.destroy');
                })->middleware('role:commission');
            });
    });

});
