<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WfhMonitoringController;

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

Route::middleware('wfh.monitoring.key')
    ->prefix('wfh-monitoring')
    ->name('api.wfh-monitoring.')
    ->group(function () {
        Route::get('/sessions', [WfhMonitoringController::class, 'index'])->name('sessions.index');
        Route::get('/sessions/{session}', [WfhMonitoringController::class, 'show'])->name('sessions.show');
        Route::get('/sessions/{session}/gps', [WfhMonitoringController::class, 'gps'])->name('sessions.gps');
        Route::get('/rules', [WfhMonitoringController::class, 'rules'])->name('rules.index');
        Route::get('/screenshots/{screenshot}', [WfhMonitoringController::class, 'screenshot'])->name('screenshots.show');

        Route::post('/sessions/{session}/live-screen/request', [WfhMonitoringController::class, 'requestLiveScreen'])->name('live-screen.request');
        Route::post('/sessions/{session}/live-screen/offer', [WfhMonitoringController::class, 'publishLiveScreenOffer'])->name('live-screen.offer');
        Route::get('/sessions/{session}/live-screen/signal', [WfhMonitoringController::class, 'liveScreenSignal'])->name('live-screen.signal');
        Route::post('/sessions/{session}/live-screen/stop', [WfhMonitoringController::class, 'stopLiveScreen'])->name('live-screen.stop');

        Route::post('/sessions/{session}/live-media/request', [WfhMonitoringController::class, 'requestLiveMedia'])->name('live-media.request');
        Route::post('/sessions/{session}/live-media/offer', [WfhMonitoringController::class, 'publishLiveMediaOffer'])->name('live-media.offer');
        Route::get('/sessions/{session}/live-media/signal', [WfhMonitoringController::class, 'liveMediaSignal'])->name('live-media.signal');
        Route::post('/sessions/{session}/live-media/stop', [WfhMonitoringController::class, 'stopLiveMedia'])->name('live-media.stop');

        Route::post('/sessions/{session}/snapshot/request', [WfhMonitoringController::class, 'requestSnapshot'])->name('snapshot.request');
        Route::post('/sessions/{session}/live-snapshots/start', [WfhMonitoringController::class, 'startLiveSnapshots'])->name('live-snapshots.start');
        Route::get('/sessions/{session}/live-snapshots/latest', [WfhMonitoringController::class, 'latestScreenshot'])->name('live-snapshots.latest');
        Route::post('/sessions/{session}/live-snapshots/stop', [WfhMonitoringController::class, 'stopLiveSnapshots'])->name('live-snapshots.stop');
    });
