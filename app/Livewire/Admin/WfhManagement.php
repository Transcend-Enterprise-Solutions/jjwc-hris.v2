<?php

namespace App\Livewire\Admin;

use App\Models\Notification;
use App\Models\WfhLocation;
use App\Models\WfhLocationRequests;
use Livewire\Component;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('WFH Management')]
class WfhManagement extends Component
{
    use WithPagination;
    public $registeredLatitude;
    public $registeredLongitude;
    public $employeeName;
    public $search;
    public $search2;
    public $search3;
    public $confirmId;
    public $address;
    public $approvedBy;
    public $approvedDate;
    public $disapprovedBy;
    public $disapprovedDate;
    public $confirmMessage;
    public $thisWFHLocId;
    public $approveOnly;
    public $pageSize = 10; 
    public $pageSizes = [10, 20, 30, 50, 100]; 

    public function render()
    {
        $employees = User::where('user_role', 'emp')
                    ->join('user_data', 'user_data.user_id', 'users.id')
                    ->join('wfh_locations', 'wfh_locations.user_id', 'users.id')
                    ->join('wfh_location_requests', 'wfh_location_requests.id', 'wfh_locations.wfh_loc_req_id')
                    ->when($this->search, function ($query) {
                        return $query->search2(trim($this->search));
                    })
                    ->orderBy('user_data.surname', 'ASC')
                    ->select([
                        'wfh_location_requests.*',
                        'users.name',
                        'users.emp_code',
                        'user_data.surname',
                        'user_data.first_name',
                        'user_data.middle_name',
                        'user_data.name_extension',
                    ])
                    ->paginate($this->pageSize);

        $locRequesters = User::where('user_role', 'emp')
                    ->join('user_data', 'user_data.user_id', 'users.id')
                    ->join('wfh_location_requests', 'wfh_location_requests.user_id', 'users.id')
                    ->when($this->search2, function ($query) {
                        return $query->search2(trim($this->search2));
                    })
                    ->where('wfh_location_requests.status', 0)
                    ->orderBy('user_data.surname', 'ASC')
                    ->select([
                        'wfh_location_requests.*',
                        'users.name',
                        'user_data.surname',
                        'user_data.first_name',
                        'user_data.middle_name',
                        'user_data.name_extension',
                    ])
                    ->paginate($this->pageSize);

        $history = User::where('user_role', 'emp')
                    ->join('user_data', 'user_data.user_id', 'users.id')
                    ->join('wfh_location_requests', 'wfh_location_requests.user_id', 'users.id')
                    ->when($this->search3, function ($query) {
                        return $query->search2(trim($this->search3));
                    })
                    ->where('wfh_location_requests.status', '!=', 0)
                    ->orderBy('wfh_location_requests.created_at', 'ASC')
                    ->select([
                        'wfh_location_requests.*',
                        'users.name',
                        'user_data.surname',
                        'user_data.first_name',
                        'user_data.middle_name',
                        'user_data.name_extension',
                    ])
                    ->paginate($this->pageSize);

        return view('livewire.admin.wfh-management', [
            'employees' => $employees,
            'locRequesters' => $locRequesters,
            'history' => $history,
        ]);
    }

    public function viewPreviousEmployeeLocation($id){
        try{
            $wfhLocRequest = WfhLocationRequests::where('wfh_location_requests.id', $id)
                ->join('users', 'users.id', 'wfh_location_requests.user_id')
                ->select([
                    'wfh_location_requests.*',
                    'users.name'
                ])
                ->first();
            if($wfhLocRequest->status){
                $this->thisWFHLocId = null;
                if($wfhLocRequest->status == 2){
                    $this->approveOnly = true;
                }else{
                    $this->approveOnly = false;

                }
            }else{
                $this->thisWFHLocId = $wfhLocRequest->user_id;
            }
            $this->registeredLatitude = floatval($wfhLocRequest->curr_lat);
            $this->registeredLongitude = floatval($wfhLocRequest->curr_lng);
            $this->employeeName = $wfhLocRequest->name;
            $this->address = $wfhLocRequest->address;
            $this->approvedBy = $wfhLocRequest->approver;
            $this->approvedDate = $wfhLocRequest->date_approved;
            $this->disapprovedBy = $wfhLocRequest->disapprover;
            $this->disapprovedDate = $wfhLocRequest->date_disapproved;

            $this->dispatch('location-updated');
        }catch(Exception $e){
            throw $e;
        }
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
            $wfhLoc = WfhLocation::where('user_id', $this->confirmId)->first();
            $wfhLocReq = WfhLocationRequests::where('user_id', $this->confirmId)
                        ->where('status', 0)
                        ->first();

            if($wfhLoc && $wfhLocReq){
                $wfhLoc->delete();
                WfhLocation::create([
                    'user_id' => $this->confirmId,
                    'address' => $wfhLocReq->address,
                    'latitude' => $wfhLocReq->curr_lat,
                    'longitude' => $wfhLocReq->curr_lng,
                    'wfh_loc_req_id' => $wfhLocReq->id,
                ]);

                $wfhLocReq->update([
                    'status' => 1,
                    'approver' => Auth::user()->name,
                    'date_approved' => now(),
                ]);

                // Mark as read notification entry
                $query = Notification::where('read', false)
                                ->where('type', 'locrequest')
                                ->where('user_id', $this->confirmId)
                                ->first();
                $query->update(['read' => true]);

                // Create a notification entry for employee
                Notification::create([
                    'user_id' => $this->confirmId,
                    'type' => 'approvedlocrequest',
                    'notif' => 'location',
                    'read' => 0,
                ]);

                $this->dispatch('swal', [
                    'title' => 'Change WFH request successfully approved',
                    'icon' => 'success'
                ]);
            }else{
                $this->dispatch('swal', [
                    'title' => 'No request record for the selected employee',
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
            $wfhLocReq = WfhLocationRequests::where('user_id', $this->confirmId)
                        ->where('status', 0)
                        ->first();

            if($wfhLocReq){
                $wfhLocReq->update([
                    'status' => 2,
                    'disapprover' => Auth::user()->name,
                    'date_disapproved' => now(),
                ]);

                // Mark as read notification entry
                $query = Notification::where('read', false)
                                ->where('type', 'locrequest')
                                ->where('user_id', $this->confirmId)
                                ->first();
                $query->update(['read' => true]);

                // Create a notification entry for employee
                Notification::create([
                    'user_id' => $this->confirmId,
                    'type' => 'disapprovedlocrequest',
                    'notif' => 'location',
                    'read' => 0,
                ]);

                $this->dispatch('swal', [
                    'title' => 'Change WFH location request disapproved successfully',
                    'icon' => 'success'
                ]);
            }else{
                $this->dispatch('swal', [
                    'title' => 'No request record for the selected employee',
                    'icon' => 'error'
                ]);
            }
            $this->resetVariables();
        }catch(Exception $e){
            throw $e;
        }
    }

    public function resetVariables(){
        $this->confirmId = null;
        $this->thisWFHLocId = null;
        $this->registeredLatitude = null;
        $this->registeredLongitude = null;
        $this->employeeName = null;
        $this->address = null;
    }
}
