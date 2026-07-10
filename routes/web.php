<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BoController;
use App\Http\Controllers\BoDraftController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RemarkController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);

// Pending / disabled accounts land here (see EnsureUserIsActive middleware).
Route::middleware('auth')->get('/account-status', function () {
    if (auth()->user()->status === 'active') {
        return redirect()->route('home');
    }

    return view('account-status');
})->name('account.status');

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::view('/profile', 'profile')->name('profile');

    Route::get('/bo', [BoController::class, 'index'])->name('bo.index');
    Route::get('/bo/history', [BoController::class, 'history'])->name('bo.history');

    // Shared collaborative draft (the editor polls + mutates this).
    Route::get('/bo/draft', [BoDraftController::class, 'show'])->name('bo.draft');
    Route::patch('/bo/draft/meta', [BoDraftController::class, 'updateMeta'])->name('bo.draft.meta');
    Route::post('/bo/draft/entries', [BoDraftController::class, 'addEntry'])->name('bo.draft.entries.add');
    Route::put('/bo/draft/entries/{entry}', [BoDraftController::class, 'updateEntry'])->name('bo.draft.entries.update');
    Route::delete('/bo/draft/entries/{entry}', [BoDraftController::class, 'removeEntry'])->name('bo.draft.entries.remove');
    Route::delete('/bo/draft/entries', [BoDraftController::class, 'clearEntries'])->name('bo.draft.entries.clear');
    Route::post('/bo/draft/load/{bo}', [BoDraftController::class, 'loadRecord'])->name('bo.draft.load');
    Route::post('/bo/draft/save', [BoDraftController::class, 'saveRecord'])->name('bo.draft.save');
    Route::get('/bo/history/export', [BoController::class, 'export'])->name('bo.export');
    Route::get('/bo/history/{bo}', [BoController::class, 'show'])->name('bo.show');
    Route::patch('/bo/history/{bo}/archive', [BoController::class, 'archive'])->name('bo.archive');
    Route::patch('/bo/history/{bo}/unarchive', [BoController::class, 'unarchive'])->name('bo.unarchive');

    Route::resource('products', ProductController::class)->except('show');
    Route::resource('stores', StoreController::class)->only(['index', 'store', 'destroy']);
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'destroy']);
    Route::resource('remarks', RemarkController::class)->only(['index', 'store', 'destroy']);

    // Admin only: user approval & enable/disable.
    Route::middleware('admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
        Route::patch('/users/{user}/role', [UserController::class, 'toggleRole'])->name('users.role');
        Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit.index');
        Route::get('/audit-log/export', [AuditLogController::class, 'export'])->name('audit.export');
        Route::delete('/audit-log/prune', [AuditLogController::class, 'prune'])->name('audit.prune');
    });
});
