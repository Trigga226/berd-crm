<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrintDashboardStatsController;
use App\Http\Controllers\PrintProjectDetailController;

Route::get('/', function () {
    return redirect('/admin');
});
Route::get('/manifestations/print-stats', \App\Http\Controllers\PrintManifestationStatsController::class)->name('manifestations.print-stats');
Route::get('/offers/print-stats', \App\Http\Controllers\PrintOfferStatsController::class)->name('offers.print-stats');
Route::get('/dashboard/print-stats', PrintDashboardStatsController::class)->name('dashboard.print-stats');
Route::get('/projects/{project}/print', PrintProjectDetailController::class)->name('projects.print');

Route::get('/private-file', [\App\Http\Controllers\PrivateFileController::class, 'show'])
    ->name('private.file.show')
    ->middleware('auth');

