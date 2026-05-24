<?php

namespace App\Livewire\Admin;

use App\Models\Notification;
use App\Models\OfficeDivisions;
use App\Models\OfficeDivisionUnits;
use App\Models\OfficialBusiness;
use App\Models\WfhLocation;
use App\Models\WfhLocationRequests;
use Livewire\Component;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Official Business')]
class AdminOfficialBusiness extends Component
{
    use WithPagination;
    public $registeredLatitude;
    public $registeredLongitude;
    public $employeeName;
    public $company;
    public $address;
    public $obDate;
    public $obStartTime;
    public $obEndTime;
    public $obTimeIn;
    public $obTimeOut;
    public $obPurpose;
    public $canApprove;
    public $approvedBy;
    public $approvedDate;
    public $duration;
    public $disapprovedBy;
    public $disapprovedDate;
    public $approvedBySup;
    public $supApprovedDate;
    public $disapprovedBySup;
    public $supDisapprovedDate;
    public $thisObId;
    public $approveOnly;
    public $search;
    public $search2;
    public $search3;
    public $confirmId;
    public $confirmMessage;
    public $selectedTab = 'ob';
    public $pageSize = 10; 
    public $attachment;
    public $pageSizes = [10, 20, 30, 50, 100]; 
    public $viewOB;
    public $pdfContent;

    public function mount(){
        $this->obDate = collect();
        $this->selectedTab = request('tab', 'ob');
    }

    public function render(){
        $obs = $this->obs();
        $obRequests = $this->obRequests();
        $disapprovedObs = $this->disapprovedObs();

        return view('livewire.admin.admin-official-business', [
            'obs' => $obs,
            'obRequests' => $obRequests,
            'disapprovedObs' => $disapprovedObs,
        ]);
    }

    public function obs(){
        $obs = OfficialBusiness::with(['dates', 'user.userData']) // Eager load the dates relationship
            ->join('users', 'official_businesses.user_id', 'users.id')
            ->join('user_data', 'user_data.user_id', 'official_businesses.user_id')
            ->where('official_businesses.status', 1)
            ->when($this->search, function ($query) {
                return $query->search5(trim($this->search));
            })
            ->select([
                'official_businesses.*',
                'user_data.surname',
                'user_data.first_name',
                'user_data.middle_name',
                'user_data.name_extension',
            ])
            ->orderBy('official_businesses.id', 'DESC') // Changed from 'date'
            ->paginate($this->pageSize);

        foreach($obs as $ob){
            $sup = User::where('id', $ob->sup_approver)->first();
            $hr = User::where('id', $ob->approver)->first();

            $ob->supervisor = $sup ? $sup->name : null;
            $ob->hr = $hr ? $hr->name : null;
            $supOfficeDiv = OfficeDivisions::where('id', ($sup ? $sup->office_division_id : null))->first();
            $ob->supOfficeDiv = $supOfficeDiv ? $supOfficeDiv->office_division : null;

            $supUnit = OfficeDivisionUnits::where('id', ($sup ? $sup->unit_id : null))->first();
            $ob->supUnit = $supUnit ? $supUnit->unit : null;
        }

        return $obs;
    }

    public function disapprovedObs(){
        $disapprovedObs = OfficialBusiness::with(['dates', 'user.userData']) // Eager load the dates relationship
            ->join('users', 'official_businesses.user_id', 'users.id')
            ->join('user_data', 'user_data.user_id', 'official_businesses.user_id')
            ->where('official_businesses.status', 2)
            ->when($this->search3, function ($query) {
                return $query->search5(trim($this->search3));
            })
            ->select([
                'official_businesses.*',
                'user_data.surname',
                'user_data.first_name',
                'user_data.middle_name',
                'user_data.name_extension',
            ])
            ->orderBy('official_businesses.id', 'DESC') // Changed from 'date'
            ->paginate($this->pageSize);

        foreach($disapprovedObs as $ob){
            $sup = User::where('id', $ob->sup_disapprover)->first();
            $hr = User::where('id', $ob->disapprover)->first();

            $ob->supervisor = $sup->name;
            $ob->hr = $hr ? $hr->name : null;
            $ob->supOfficeDiv = OfficeDivisions::where('id', $sup->office_division_id)->first()->office_division;
            $ob->supUnit = OfficeDivisionUnits::where('id', $sup->unit_id)->first()->unit;
        }

        return $disapprovedObs;
    }

    public function obRequests(){
        $user = Auth::user();

        $obRequests = OfficialBusiness::with(['dates', 'user.userData']) // Eager load the dates relationship
            ->join('users', 'official_businesses.user_id', 'users.id')
            ->join('user_data', 'user_data.user_id', 'official_businesses.user_id')
            ->where('official_businesses.status', 0)
            ->when($this->search2, function ($query) {
                return $query->search5(trim($this->search2));
            })
            ->select([
                'official_businesses.*',
                'user_data.surname',
                'user_data.first_name',
                'user_data.middle_name',
                'user_data.name_extension',
            ])
            ->orderBy('official_businesses.id', 'DESC'); // Changed from 'date' since date column no longer exists

        if($user->user_role != 'sv'){
            $obRequests = $obRequests->where(function($query) {
                $query->whereNotNull('official_businesses.date_sup_approved')
                    ->orWhereNotNull('official_businesses.date_sup_disapproved');
            })->paginate($this->pageSize);

            foreach($obRequests as $ob){
                $sup = User::where('id', $ob->sup_approver)->first();
                $ob->supervisor = $sup->name;
                $ob->supOfficeDiv = OfficeDivisions::where('id', $sup->office_division_id)->first()->office_division;
                $ob->supUnit = OfficeDivisionUnits::where('id', $sup->unit_id)->first()->unit;
            }
        }else {
            $obRequests = $obRequests->where('official_businesses.sup_approver', $user->id)
                    ->whereNull('official_businesses.approver')
                    ->where(function($query) {
                        $query->whereNull('official_businesses.date_sup_approved')
                            ->orWhereNull('official_businesses.date_sup_disapproved');
                    })
                    ->paginate($this->pageSize);

            foreach($obRequests as $ob){
                if($ob->date_sup_approved){
                    $ob->supervisor = $user->name;
                    $ob->isApproved = true;
                }
            }
        }

        return $obRequests;
    }

    public function showOb($obId, $tab = ''){
        try{
            $ob = OfficialBusiness::where('id', $obId)->first();

            $user = User::where('id', $ob->user_id)->first();
            $userName = $user->name;
            $workGoup = OfficeDivisions::where('id', $user->office_division_id)->first(); 
            $unit = OfficeDivisionUnits::where('id', $user->unit_id)->first(); 
            if($ob){

                $this->viewThisOB($obId, $tab);

                $pdf = PDF::loadView('pdf.ob', [
                    'ob' => $ob,
                    'userName' => $userName,
                    'workGroup' => $workGoup,
                    'unit' => $unit,
                ]);
        
                $this->pdfContent = base64_encode($pdf->output());
            }else{
              $this->dispatch('swal', [
                    'title' => 'Official Business not found',
                    'icon' => 'error'
                ]);  
            }  
        }catch(Exception $e){
            throw $e;
        }
    }

    public function closeOb(){
        $this->pdfContent = null;
    }

    public function toogleConfirmModal($id, $type){
        $this->confirmId = $id;
        if($type === 'approve'){
            $this->confirmMessage = 'approve';
        }else{
            $this->confirmMessage = 'disapprove';
        }
    }

    public function approveEmployeeLocation(){
        try{
            $user = Auth::user();
            $ob = OfficialBusiness::where('id', $this->confirmId)->first();
            if($ob){

                if($user->user_role == 'sv'){
                    $ob->update([
                        'date_sup_approved' => now(),
                    ]);
                }else{
                    $ob->update([
                        'status' => 1,
                        'approver' => $user->id,
                        'date_approved' => now(),
                    ]);

                    // Mark as read notification entry
                    $query = Notification::where('read', false)
                                    ->where('type', 'obrequest')
                                    ->where('user_id', $ob->user_id)
                                    ->first();
                    $query->update(['read' => true]);
                }
                $this->dispatch('swal', [
                    'title' => 'Official Business approved successfully',
                    'icon' => 'success'
                ]);
            }else{
                $this->dispatch('swal', [
                    'title' => 'Official Business approval was unsuccessful',
                    'icon' => 'error'
                ]);
            }
            $this->resetVariables();
        }catch(Exception $e){
            throw $e;
        }
    }

    public function disapproveEmployeeLocation(){
        try{
            $user = Auth::user();
            $ob = OfficialBusiness::where('id', $this->confirmId)->first();
            if($ob){
                if($user->user_role == 'sv'){
                    $ob->update([
                        'status' => 2,
                        'date_sup_disapproved' => now(),
                    ]);
                }else{
                    $ob->update([
                        'status' => 2,
                        'disapprover' => $user->id,
                        'date_disapproved' => now(),
                    ]);
                
                    // Mark as read notification entry
                    $query = Notification::where('read', false)
                                    ->where('type', 'obrequest')
                                    ->where('user_id', $ob->user_id)
                                    ->first();
                    $query->update(['read' => true]);
                }
                $this->dispatch('swal', [
                    'title' => 'Official Business disapproved successfully',
                    'icon' => 'success'
                ]);
            }else{
                $this->dispatch('swal', [
                    'title' => 'Official Business disapproval was unsuccessful',
                    'icon' => 'error'
                ]);
            }
            $this->resetVariables();
        }catch(Exception $e){
            throw $e;
        }
    }

    public function viewThisOB($id, $tab){
        try{
            $ob = OfficialBusiness::where('official_businesses.id', $id)
                ->join('user_data', 'user_data.user_id', 'official_businesses.user_id')
                ->first();
                
            if($ob){
                $o = OfficialBusiness::where('id', $id)->first();

                // $user = Auth::user();

                // if($user && $user->user_role == 'sv'){
                //     if($user->id == $ob->sup_approver){
                //         $this->canApprove = false;
                //     }
                //     if($user->id == $ob->sup_disapprover){
                //         $this->canApprove = false;
                //     }
                // }

                // if($user && ($user->user_role == 'hr' || $user->user_role == 'sa')){
                //     if($user->id == $ob->approver){
                //         $this->canApprove = false;
                //     }
                //     if($user->id == $ob->disapprover){
                //         $this->canApprove = false;
                //     }
                // }

                $this->viewOB = true;
                if($tab == 'request'){
                    $this->thisObId = $id;
                    $this->approveOnly = null;
                }else if($tab == 'disapproved'){
                    $this->thisObId = $id;
                    $this->approveOnly = true;
                }else{
                    $this->thisObId = null;
                    $this->approveOnly = null;
                }

                // Safely build employee name with null checks
                $firstName = $ob->first_name ?? '';
                $middleName = $ob->middle_name ? $ob->middle_name . ' ' : '';
                $nameExtension = $ob->name_extension ?? '';
                $surname = $ob->surname ?? '';
                
                $this->employeeName = trim($surname . ', ' . $firstName . ' ' . $middleName . $nameExtension);
                
                $this->company = $ob->company ?? 'N/A';
                $this->address = $ob->address ?? 'N/A';
                $this->registeredLatitude = $ob->lat ?? null;
                $this->registeredLongitude = $ob->lng ?? null;
                $this->duration = $ob->duration ?? null;
                $this->obDate = $o->dates ?? null;
                $this->obStartTime = $ob->time_start ?? null;
                $this->obEndTime = $ob->time_end ?? null;
                $this->obTimeIn = $ob->time_in ?? null;
                $this->obTimeOut = $ob->time_out ?? null;
                $this->obPurpose = $ob->purpose ?? 'N/A';
                $this->attachment = $ob->attachment ?? null;

                // Safely get approver info with null checks
                $this->approvedBy = 'N/A';
                if($ob->approver){
                    $approver = User::where('id', $ob->approver)->first();
                    $this->approvedBy = $approver ? $approver->name : 'N/A';
                }
                
                $this->approvedDate = $ob->date_approved 
                    ? Carbon::parse($ob->date_approved)->format('F d, Y')
                    : 'N/A';
                
                // Safely get disapprover info with null checks
                $this->disapprovedBy = 'N/A';
                if($ob->disapprover){
                    $disapprover = User::where('id', $ob->disapprover)->first();
                    $this->disapprovedBy = $disapprover ? $disapprover->name : 'N/A';
                }
                
                $this->disapprovedDate = $ob->date_disapproved 
                    ? Carbon::parse($ob->date_disapproved)->format('F d, Y')
                    : 'N/A';
                
                // Safely get supervisor approver info with null checks
                $this->approvedBySup = 'N/A';
                if($ob->sup_approver){
                    $supApprover = User::where('id', $ob->sup_approver)->first();
                    $this->approvedBySup = $supApprover ? $supApprover->name : 'N/A';
                }
                
                $this->supApprovedDate = $ob->date_sup_approved 
                    ? Carbon::parse($ob->date_sup_approved)->format('F d, Y')
                    : 'N/A';
                
                // Safely get supervisor disapprover info with null checks
                $this->disapprovedBySup = 'N/A';
                if($ob->sup_disapprover){
                    $supDisapprover = User::where('id', $ob->sup_disapprover)->first();
                    $this->disapprovedBySup = $supDisapprover ? $supDisapprover->name : 'N/A';
                }
                
                $this->supDisapprovedDate = $ob->date_sup_disapproved 
                    ? Carbon::parse($ob->date_sup_disapproved)->format('F d, Y')
                    : 'N/A';
                    
                $this->dispatch('location-updated');
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function downloadAttachment($obId){
        try {
            $ob = OfficialBusiness::where('id', $obId)->first();
            
            if (!$ob || !$ob->attachment) {
                $this->dispatch('swal', [
                    'title' => 'Attachment not found!',
                    'icon' => 'error'
                ]);
                return;
            }

            if (Storage::disk('public')->exists($ob->attachment)) {
                $fileContent = Storage::disk('public')->get($ob->attachment);
                $fileName = $ob->attachment_original_name ?: 'ob_attachment.pdf';

                return response()->streamDownload(function () use ($fileContent) {
                    echo $fileContent;
                }, $fileName);
            } else {
                $this->dispatch('swal', [
                    'title' => 'File not found!',
                    'icon' => 'error'
                ]);
            }
        } catch (Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error downloading file: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function resetVariables(){
        $this->confirmId = null;
        $this->thisObId = null;
        $this->employeeName = null;
        $this->company = null;
        $this->address = null;
        $this->registeredLatitude = null;
        $this->registeredLongitude = null;
        $this->obDate = collect();
        $this->obStartTime = null;
        $this->obEndTime = null;
        $this->obTimeIn = null;
        $this->obTimeOut = null;
        $this->obPurpose = null;
        $this->approvedBy = null;
        $this->approvedDate = null;
        $this->approveOnly = null;
        $this->viewOB = null;
    }
}
