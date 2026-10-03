<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\ProjectManagerController;
use App\Http\Controllers\QAController;
use App\Http\Controllers\DesignerController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

// ── Authentication ──────────────────────────────────────────────────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// ── Root redirect (by role) ─────────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return match(auth()->user()->role) {
            'pm'        => redirect('/pm/dashboard'),
            'client'    => redirect('/client/dashboard'),
            'developer' => redirect('/developer/dashboard'),
            'designer'  => redirect('/designer/dashboard'),
            'qa'        => redirect('/qa/dashboard'),
            default     => redirect('/login'),
        };
    }
    return redirect('/login');
});

// ── Shared Routes ─────────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::post('/projects/{project}/chat', [ChatController::class, 'store'])->name('chat.store');
});

// ── PM Routes ───────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:pm'])->prefix('pm')->name('pm.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [ProjectManagerController::class, 'dashboard'])->name('dashboard');

    // Project CRUD
    Route::post('/projects',                      [ProjectManagerController::class, 'storeProject'])->name('projects.store');
    Route::delete('/projects/{project}',          [ProjectManagerController::class, 'deleteProject'])->name('projects.delete');
    Route::post('/projects/{project}/status',     [ProjectManagerController::class, 'updateStatus'])->name('projects.status');
    Route::post('/projects/{project}/finish',     [ProjectManagerController::class, 'markFinished'])->name('projects.finish');
    Route::get('/projects/{project}/proposal',    [ProjectManagerController::class, 'downloadProposal'])->name('projects.proposal');

    // Tasks
    Route::post('/projects/{project}/tasks',      [ProjectManagerController::class, 'storeTask'])->name('tasks.store');
});

// ── Client Routes ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard',                         [ClientController::class, 'dashboard'])->name('dashboard');
    Route::post('/projects/{project}/ui',            [ClientController::class, 'updateUiStatus'])->name('ui.update');
});

// ── Developer Routes ────────────────────────────────────────
Route::middleware(['auth', 'role:developer'])->prefix('developer')->name('developer.')->group(function () {
    Route::get('/dashboard',                    [DeveloperController::class, 'dashboard'])->name('dashboard');
    Route::post('/tasks',                       [DeveloperController::class, 'storeTask'])->name('tasks.store');
    Route::post('/tasks/{task}',                [DeveloperController::class, 'updateTask'])->name('tasks.update');
});

// ── QA Routes ────────────────────────────────────────
Route::middleware(['auth', 'role:qa'])->prefix('qa')->name('qa.')->group(function () {
    Route::get('/dashboard',              [QAController::class, 'dashboard'])->name('dashboard');
    Route::post('/tasks/{task}/approve',  [QAController::class, 'approveTask'])->name('tasks.approve');
    Route::post('/tasks/{task}/reject',   [QAController::class, 'rejectTask'])->name('tasks.reject');
});

// ── Designer Routes ────────────────────────────────────────
Route::middleware(['auth', 'role:designer'])->prefix('designer')->name('designer.')->group(function () {
    Route::get('/dashboard',                    [DesignerController::class, 'dashboard'])->name('dashboard');
    Route::post('/projects/{project}/ui-link',  [DesignerController::class, 'submitUiLink'])->name('ui.submit');
});