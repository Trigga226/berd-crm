<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrintDashboardStatsController;

Route::get('/', function () {
    return redirect('/admin');
});
Route::get('/manifestations/print-stats', \App\Http\Controllers\PrintManifestationStatsController::class)->name('manifestations.print-stats');
Route::get('/offers/print-stats', \App\Http\Controllers\PrintOfferStatsController::class)->name('offers.print-stats');
Route::get('/dashboard/print-stats', PrintDashboardStatsController::class)->name('dashboard.print-stats');
