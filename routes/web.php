<?php

use App\Http\Controllers\OrganizationRouteController;
use App\Http\Controllers\SmartContractController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Tabel DataTables (serverSide)
// Route::get('/protected/view-content-blockchain', [SmartContractController::class, 'index'])
//      ->name('showContentBlockchainOrg');

// Simpan data setelah panggilan addContent berhasil



Route::get('/', function () {
    return view('welcome');
});

Route::redirect('/', env('XBUG_URL').'/login')->name('login');
Route::get('/smart-contract-redirect', [OrganizationRouteController::class, 'redirectSmartContractOrg'])->name('redirectSmartContractOrg');

Route::prefix('protected')->middleware(['auth','custom.auth'])->group(function () {
    Route::get('/dashboard', [OrganizationRouteController::class, 'showDashboard'])->name('showDashboardOrganization');
    Route::post('/smart-contract/save-deployed', [SmartContractController::class, 'saveDeployedData'])
     ->name('saveDeployedData');
    Route::get('/view-content-blockchain', [SmartContractController::class, 'showContentBlockchainOrg'])->name('showContentBlockchainOrg');
    Route::post('/deploy-smart-contract/{id}', [SmartContractController::class, 'deploySmartContract'])->name('deploySmartContract');
    Route::get('/view-content-blockchain-logs', [SmartContractController::class, 'showContentBlockchainlogsOrg'])->name('showContentBlockchainlogsOrg');
    Route::get('/smart-contract/{id}/logs', [SmartContractController::class, 'getLogs'])->name('smartContract.getLogs');


    Route::get('/notifications', [OrganizationRouteController::class, 'showNotificationOrg'])->name('showNotificationOrg');
    Route::post('/logout', [OrganizationRouteController::class, 'logout'])->name('organization.logout');
});
