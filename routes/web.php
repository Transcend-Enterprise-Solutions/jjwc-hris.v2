<?php

use App\Http\Controllers\AccountSwitchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataFeedController;
use App\Livewire\Admin\AdminLeaveRequest;
use App\Livewire\ApplicationTracker;
use App\Livewire\Documentation;
use App\Livewire\JobListings;
use App\Livewire\User\DocRequest;
use App\Livewire\User\DownloadableForms;
use App\Livewire\User\Dtr;
use App\Livewire\User\EmpAdministrativeCases;
use App\Livewire\User\Home;
use App\Livewire\User\LeaveApplication;
use App\Livewire\User\LeaveCredits;
use App\Livewire\User\LeaveMonetization;
use App\Livewire\User\MyDocuments;
use App\Livewire\User\MySchedule;
use App\Livewire\User\WfhMonitoringSession;
use App\Livewire\User\OfficialBusiness;
use App\Livewire\User\PersonalDataSheet;
use App\Livewire\User\WfhSched as UserWfhSched;
use App\Livewire\User\WorkExperienceSheet;
use App\Models\CaseTracking;
use App\Services\RouteService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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

Route::redirect('/', '/login');
Route::get('/careers', JobListings::class)->name('careers');
Route::get('/track-application', ApplicationTracker::class)->name('track-application');

Route::get('/register', function () {
    return view('registeraccount');
})->name('register');

Route::get('/data-privacy-policy', function () {
    return view('data-privacy-policy');
})->name('data-privacy-policy');

Route::get('/contact-us', function () {
    return view('contact-us');
})->name('contact-us');


Route::middleware(['auth', 'checkrole:sa,hr,sv,pa'])->get('/dashboard', [DashboardController::class, 'index'])->name('/dashboard');
Route::middleware(['auth', 'checkrole:sa,hr,sv,pa'])->get('/json-data-feed', [DataFeedController::class, 'getDataFeed'])->name('json_data_feed');
Route::middleware('auth')->group(function () {
    RouteService::registerDynamicRoutes();
});

Route::middleware(['auth'])->group(function () {
    Route::get('/account/available', [AccountSwitchController::class, 'getAvailableAccounts']);
    Route::post('/account/switch', [AccountSwitchController::class, 'switchAccount']);
    Route::post('/account/switch-back', [AccountSwitchController::class, 'switchBackToOriginal']);
    
    Route::get('/documentation', Documentation::class)->name('documentation');
});

Route::middleware(['auth', 'checkrole:emp'])->group(function () {
    Route::get('/home', Home::class)->name('home');

    // My Records Tabs ----------------------------------------------------------------------------------------------------------------------------------- //
    Route::get('/my-records/personal-data-sheet', PersonalDataSheet::class)->name('/my-records/personal-data-sheet');
    Route::get('/my-records/work-experience-sheet', WorkExperienceSheet::class)->name('/my-records/work-experience-sheet');
    Route::get('/my-records/my-documents', MyDocuments::class)->name('/my-records/my-documents');
    Route::get('/my-records/doc-request', DocRequest::class)->name('/my-records/doc-request');

    // Daily Time Records Tabs --------------------------------------------------------------------------------------------------------------------------- //
    Route::get('/daily-time-record/dtr', Dtr::class)->name('/daily-time-record/dtr');
    Route::get('/daily-time-record/official-business', OfficialBusiness::class)->name('/daily-time-record/official-business');
    Route::get('/daily-time-record/my-schedule', MySchedule::class)->name('/daily-time-record/my-schedule');
    Route::get('/daily-time-record/wfh-sched', UserWfhSched::class)->name('/daily-time-record/wfh-sched');
    Route::get('/daily-time-record/wfh-monitoring', WfhMonitoringSession::class)->name('/daily-time-record/wfh-monitoring');

    // Labor Employee Relations Tabs -------------------------------------------------------------------------------------------------------------------------- //
    Route::get('/labor-employee-relations/my-administrative-cases', EmpAdministrativeCases::class)->name('/labor-employee-relations/my-administrative-cases');

    // Filing and Approval Tabs -------------------------------------------------------------------------------------------------------------------------- //
    Route::get('/filing-and-approval/leave-application', LeaveApplication::class)->name('/filing-and-approval/leave-application');
    Route::get('/filing-and-approval/admin-leave-request', AdminLeaveRequest::class)
        ->middleware(['auth', 'checkrole:emp', 'checkoic'])
        ->name('/filing-and-approval/admin-leave-request');
    Route::get('/filing-and-approval/leave-credits', LeaveCredits::class)->name('/filing-and-approval/leave-credits');
    Route::get('/filing-and-approval/leave-monetization', LeaveMonetization::class)->name('/filing-and-approval/leave-monetization');

    Route::get('/downloadable', DownloadableForms::class)->name('downloadable');
});

Route::get('/download-attachment/{path}', function ($path) {
    try {
        // Decode the path
        $decodedPath = base64_decode($path);

        // Security check - ensure the path is within job-attachments directory
        if (! str_starts_with($decodedPath, 'job-attachments/')) {
            abort(403, 'Unauthorized access');
        }

        if (! Storage::disk('public')->exists($decodedPath)) {
            abort(404, 'File not found');
        }

        $filePath = Storage::disk('public')->path($decodedPath);
        $fileName = basename($decodedPath);

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    } catch (\Exception $e) {
        abort(500, 'Error downloading file');
    }
})->name('download.attachment')->middleware('auth');

Route::get('/download-document/{path}/{name}', function ($path, $name) {
    try {
        $decodedPath = base64_decode($path);

        if (! Storage::disk('public')->exists($decodedPath)) {
            abort(404, 'File not found');
        }

        $fullPath = Storage::disk('public')->path($decodedPath);
        $extension = pathinfo($decodedPath, PATHINFO_EXTENSION);
        $fileName = $name.'.'.$extension;
        $mimeType = Storage::disk('public')->mimeType($decodedPath);

        return response()->download($fullPath, $fileName, [
            'Content-Type' => $mimeType,
        ]);

    } catch (\Exception $e) {
        abort(500, 'Error downloading file: '.$e->getMessage());
    }
})->name('download.document')->middleware('auth');

// Route::get('/signature/{filename}', function ($filename) {
//     $path = 'signatures/' . $filename;  // Note: plural "signatures"

//     if (!Storage::disk('public')->exists($path)) {
//         abort(404);
//     }

//     $file = Storage::disk('public')->get($path);
//     $type = Storage::disk('public')->mimeType($path);

//     return response($file, 200)->header('Content-Type', $type);
// })->name('signature.file');
Route::get('/signature/{filename}', function ($filename) {
    $path = 'signatures/'.$filename;

    if (! Storage::disk('public')->exists($path)) {
        abort(404);
    }

    return Storage::disk('public')->response($path);
})->name('signature.file');

Route::get('/signatory-signature/{filename}', function ($filename) {
    $path = storage_path('app/public/signatory-signatures/'.$filename);

    if (! file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
})->name('signatory-signature.file');

Route::get('/pds-photo/{filename}', function ($filename) {
    $path = 'pds-photos/'.$filename;

    if (! Storage::disk('public')->exists($path)) {
        abort(404);
    }

    $file = Storage::disk('public')->get($path);
    $type = File::mimeType(storage_path('app/public/'.$path));

    return response($file, 200)->header('Content-Type', $type);
})->name('pds-photo.file');

Route::get('/profile-photo/{filename}', function ($filename) {
    $path = 'profile-photos/'.$filename;

    if (! Storage::disk('public')->exists($path)) {
        abort(404);
    }

    $file = Storage::disk('public')->get($path);
    $type = File::mimeType(storage_path('app/public/'.$path));

    return response($file, 200)->header('Content-Type', $type);
})->name('profile-photo.file');

Route::get('/download-corrective-action/{caseId}', function ($caseId) {
    $case = CaseTracking::findOrFail($caseId);
    $correctiveAction = $case->correctiveActions()->firstOrFail();

    if (! Storage::exists($correctiveAction->pdf_path)) {
        abort(404);
    }

    return Storage::download($correctiveAction->pdf_path,
        ($correctiveAction->action_taken_name ?? 'Notice').'.pdf');
})->name('download.corrective_action');

Route::get('/wfh/download/{id}', [App\Livewire\User\WfhSched::class, 'downloadAttachment'])
    ->middleware('auth')
    ->name('wfh.download');
Route::get('/admin/wfh/download/{id}', [App\Livewire\Admin\WfhSched::class, 'downloadAttachment'])
    ->middleware('auth')
    ->name('admin.wfh.download');
