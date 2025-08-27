<?php


use Admin\Http\Controllers\ServiceCatalogController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\LocateController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\PartnerCategoryController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProjectTeamController;
use App\Http\Controllers\SpeakerController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\VacancyController;
use Illuminate\Support\Facades\Route;
use Lk\Http\Controllers\AuthController;

Route::group(['prefix' => '/front'], function () {
    Route::post('registration', [AuthController::class, 'registration']);
});

Route::group(['middleware' => 'api'], function () {

    Route::get('/participant', [ParticipantController::class, 'show'])->name('participant.show');
    Route::post('/changelang/{locate}', [LocateController::class, 'changeLocate']);
    Route::post('/checklang}', [LocateController::class, 'checkLocate']);
    Route::get('/partners', [PartnerController::class, 'list'])->name('partner.index');
    Route::get('/speakers', [SpeakerController::class, 'list'])->name('speaker.index');
    Route::get('/speaker', [SpeakerController::class, 'show'])->name('speaker.show');
    Route::get('/project-team', [ProjectTeamController::class, 'list'])->name('project-team.index');
    Route::get('/participants', [ParticipantController::class, 'list'])->name('participant.index');
    Route::get('/partner-categories', [PartnerCategoryController::class, 'list'])->name('partner-category.index');
    Route::get('/programs', [ProgramController::class, 'list'])->name('program.index');
    Route::get('/documents', [StorageController::class, 'list'])->name('documents');
});


Route::group(['middleware' => ['auth:api','role:super_admin|manager|commission']], function () {
    Route::post('/storage/upload', [StorageController::class, 'upload']);
});

Route::prefix('service_catalog')->group(function () {
    Route::patch('/', [ServiceCatalogController::class, 'update']);
    Route::get('/', [ServiceCatalogController::class, 'show']);
    Route::post('/', [ServiceCatalogController::class, 'store']);
    Route::patch('/', [ServiceCatalogController::class, 'update']);
});

Route::prefix('vacancy')->group(function () {
    Route::get('/show', [VacancyController::class, 'show'])->name('vacancy.show');
    Route::get('/list', [VacancyController::class, 'list'])->name('vacancy.list');
});

Route::get('/events', [EventsController::class, 'show'])->name('events.show');
Route::get('/event/list', [EventsController::class, 'list'])->name('events.list');
