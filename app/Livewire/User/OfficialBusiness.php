<?php

namespace App\Livewire\User;

use App\Models\Notification;
use App\Models\OfficeDivisions;
use App\Models\OfficeDivisionUnits;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\OfficialBusiness as OB;
use App\Models\OfficialBusinessDate;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Official Business')]
class OfficialBusiness extends Component
{
    use WithFileUploads;

    public $search4;
    public $search5;
    public $search6;
    public $editOB;
    public $addOB;
    public $deleteId;
    public $editId;
    public $company;
    public $address;
    public $dates = [];
    public $dateInput;
    public $startTime;
    public $endTime;
    public $timeIn;
    public $timeOut;
    public $purpose;
    public $registeredLatitude = null;
    public $registeredLongitude = null;
    public $newLatitude = null;
    public $newLongitude = null;
    public $isWithinRadius;
    public $isTodayIsOb;
    public $latitude = null;
    public $longitude = null;
    public $formattedTime = null;
    public $obStatus;
    public $viewOB;
    public $approvedBy;
    public $approvedDate;
    public $disapprovedBy;
    public $disapprovedDate;
    public $approvedBySup;
    public $supApprovedDate;
    public $disapprovedBySup;
    public $supDisapprovedDate;
    public $showConfirmation = false;
    public $punchState;
    public $punchObId;
    public $verifyType;
    public $hasObTimeIn;
    public $hasObTimeOut;
    public $ongoingObs;
    public $upcomingObs;
    public $duration = '';
    public $selectedTab = 'approved';
    public $pdfContent;
    public $attachment;
    public $existingAttachment;
    public $pageSize = 10; 
    public $pageSizes = [10, 20, 30, 50, 100]; 

    public function mount(){
        $this->ongoingObs = $this->ongoingOfficialBusinesses();
        $this->upcomingObs = $this->upcomingOfficialBusinesses();

        if (!$this->ongoingObs) {
            $this->ongoingObs = $this->upcomingObs->first();
            $this->obStatus = 'UPCOMING';
        }else{
            $this->obStatus = 'ONGOING';
        }

        $this->registeredLatitude = $this->ongoingObs ? $this->ongoingObs->lat : null;
        $this->registeredLongitude = $this->ongoingObs ? $this->ongoingObs->lng : null;

        $this->selectedTab = request('tab', 'approved');
    }


    public function render(){
        // $this->showOb(5);
        $obRequests = $this->obRequests();
        $disapprovedObs = $this->disapprovedObs();
        $approvedObs = $this->approvedObs();
            

        return view('livewire.user.official-business', [
            'obRequests' => $obRequests,
            'disapprovedObs' => $disapprovedObs,
            'approvedObs' => $approvedObs,
        ]);
    }

    public function showOb($obId)
    {
        try{
            $ob = OB::where('id', $obId)->first();
            $userName = Auth::user()->name;
            $workGoup = OfficeDivisions::where('id', Auth::user()->office_division_id)->first(); 
            $unit = OfficeDivisionUnits::where('id', Auth::user()->unit_id)->first(); 
            if($ob){
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

    public function closeOb()
    {
        $this->pdfContent = null;
    }

    public function ongoingOfficialBusinesses(){
        $user = Auth::user();
        $ongoingObs = OB::where('date', '=', now()->toDateString())
            ->where('time_start', '<=', now()->toTimeString())
            ->where('time_end', '>=', now()->toTimeString())
            ->whereNull('time_out')  
            ->whereNull('date_sup_disapproved')
            ->where('user_id', $user->id)
            ->first();

        // if ($ongoingObs && $upcomingObs->contains('id', $ongoingObs->id)) {
        //     $upcomingObs = $upcomingObs->filter(function ($ob) use ($ongoingObs) {
        //         return $ob->id !== $ongoingObs->id;
        //     });
        // }

        if($ongoingObs){
            if (now()->isSameDay(Carbon::parse($ongoingObs->date))) {
                $this->isTodayIsOb = true;
            }

            if($ongoingObs->time_in){
                $this->hasObTimeIn = $ongoingObs->time_in;
            }
            if($ongoingObs->time_out){
                $this->hasObTimeOut = $ongoingObs->time_out;
            }
        }

        return $ongoingObs;
    }

    public function upcomingOfficialBusinesses(){
        $user = Auth::user();
        $upcomingObs = OB::where(function ($query) {
            $query->where('date', '>', now()->toDateString())
                ->orWhere(function ($subQuery) {
                    $subQuery->where('date', '=', now()->toDateString())
                        ->where('time_start', '>', now()->toTimeString());
                });
            })
            ->whereNull('date_sup_disapproved')
            ->where('user_id', $user->id)
            ->whereNull('time_out')  
            ->orderBy('date', 'asc')
            ->orderBy('time_start', 'asc')
            ->get();

        return $upcomingObs;
    }

    public function completedObs(){
        $user = Auth::user();
        $completedObs = OB::where('time_in', '!=', null)
            ->where('user_id', $user->id)
            ->where('time_out', '!=', null)
            ->paginate($this->pageSize);
        
        return $completedObs;
    }

    public function unattendedObs(){
        $user = Auth::user();
        $unattendedObs = OB::where('time_in', '=', null)
            ->where('user_id', $user->id)
            ->where('time_out', '=', null)
            ->where('date', '<', now()->toDateString())
            ->paginate($this->pageSize);
        
        return $unattendedObs;
    }

    public function obRequests(){
        $user = Auth::user();
        $obRequests = OB::where(function($query) {
            $query->where(function($q) {
                $q->whereNull('date_sup_approved')
                ->whereNull('date_sup_disapproved')
                ->whereNull('date_approved')
                ->whereNull('date_disapproved');
            })
            ->orWhere(function($q) {
                    $q->where(function($subQ) {
                        $subQ->whereNotNull('date_sup_approved')
                            ->orWhereNotNull('date_sup_disapproved');
                    })
                    ->where(function($subQ) {
                        $subQ->whereNull('date_approved')
                            ->whereNull('date_disapproved');
                    });
                });
            })
            ->where('user_id', $user->id)
            ->where('status', 0)
            ->when($this->search4, function ($query) {
                return $query->search(trim($this->search4));
            })
            ->paginate($this->pageSize);

        foreach ($obRequests as $obReq) {
            $sup = User::where('id', $obReq->sup_approver)->first();
            $hr = User::where('id', $obReq->approver)->first();

            if(!$hr){
                $hr = User::where('id', $obReq->disapprover)->first();
            }

            $obReq->supervisor = $sup->name;
            $obReq->hr = $hr ? $hr->name : null;
        }

        return $obRequests;
    }

    public function disapprovedObs(){
        $user = Auth::user();
        $disapprovedObs = OB::where('status', 2)
        ->where('user_id', $user->id)
        ->when($this->search6, function ($query) {
            return $query->search(trim($this->search6));
        })
        ->paginate($this->pageSize);

        foreach ($disapprovedObs as $obs) {
            $sup = User::where('id', $obs->sup_disapprover)->first();
            $hr = User::where('id', $obs->disapprover)->first();

            $obs->supervisor = $sup->name;
            $obs->hr = $hr ? $hr->name : null;
        }

        return $disapprovedObs;
    }

    public function approvedObs(){
        $user = Auth::user();
        $approvedObs = OB::where('status', 1)
        ->where('user_id', $user->id)
        ->when($this->search5, function ($query) {
            return $query->search(trim($this->search5));
        })
        ->paginate($this->pageSize);

        foreach ($approvedObs as $obs) {
            $sup = User::where('id', $obs->sup_approver)->first();
            $hr = User::where('id', $obs->approver)->first();

            $obs->supervisor = $sup->name;
            $obs->hr = $hr ? $hr->name : null;
        }

        return $approvedObs;
    }




    #[On('locationUpdated')] 
    public function handleLocationUpdate($locationData)
    {
        if (is_string($locationData)) {
            $locationData = json_decode($locationData, true);
        }
        
        $this->latitude = $locationData['latitude'] ?? null;
        $this->longitude = $locationData['longitude'] ?? null;
        $this->formattedTime = $locationData['formattedTime'] ?? null;
        
        // Check if within allowed radius and update UI accordingly
        $this->isWithinRadius = $this->isWithinAllowedRadius();
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        // Radius of the Earth in meters
        $R = 6371000;

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Differences in coordinates
        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        // Haversine formula
        $a = sin($dLat/2) * sin($dLat/2) +
            cos($lat1) * cos($lat2) * 
            sin($dLon/2) * sin($dLon/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        // Distance in meters
        $distance = $R * $c;
        
        return $distance;
    }

    private function isWithinAllowedRadius(){
        if (!$this->latitude || !$this->longitude) {
            return false;
        }

        $distance = $this->calculateDistance(
            $this->registeredLatitude,
            $this->registeredLongitude,
            $this->latitude,
            $this->longitude
        );
        return $distance <= 300;
    }

    public function toggleAddOB(){
        $this->addOB = true;
        $this->editOB = true;
    }

    public function toggleEditOB($id){
        $this->editOB = true;
        $this->editId = $id;
        $this->addOB = false; // Make sure this is set to false for editing
        
        try{
            $ob = OB::where('id', $id)->first();
            if($ob){
                $this->company = $ob->company;
                $this->address = $ob->address;
                $this->registeredLatitude = $ob->lat;
                $this->registeredLongitude = $ob->lng;
                
                // Load multiple dates from the relationship
                $this->dates = $ob->dates()->pluck('date')->map(function($date) {
                    return $date->format('Y-m-d');
                })->toArray();
                
                $this->startTime = $ob->time_start;
                $this->endTime = $ob->time_end;
                $this->purpose = $ob->purpose;
                $this->duration = $ob->duration;
                $this->existingAttachment = $ob->attachment;
                
                // Set map coordinates if needed
                $this->newLatitude = $ob->lat;
                $this->newLongitude = $ob->lng;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function toggleDeleteOB($id){
        $this->deleteId = $id;
    }

    public function addDate()
    {
        $this->validate([
            'dateInput' => 'required|date',
        ]);

        if (in_array($this->dateInput, $this->dates)) {
            $this->addError('dateInput', 'This date has already been added.');
            return;
        }
        $this->dates[] = $this->dateInput;
        sort($this->dates);
        $this->dateInput = null;
    }

    public function removeDate($index)
    {
        if (isset($this->dates[$index])) {
            unset($this->dates[$index]);
            $this->dates = array_values($this->dates);
        }
    }

    public function saveOB(){
        try{
            $user = Auth::user();
            
            // Validation rules
            $validationRules = [
                'company' => 'required',
                'address' => 'required',
                'dates' => 'required|array|min:1',
                'dates.*' => 'date',
                'purpose' => 'required',
                'newLatitude' => 'required',
            ];

            // Add attachment validation for new OB
            if($this->addOB) {
                $validationRules['attachment'] = 'required|file|mimes:pdf|max:10240'; // 10MB max, PDF only
            } else {
                // For edits, attachment is optional
                $validationRules['attachment'] = 'nullable|file|mimes:pdf|max:10240';
            }

            if($this->duration == 'half_day'){      
                $validationRules['startTime'] = 'required';
                $validationRules['endTime'] = 'required|after:startTime';
            }

            $this->validate($validationRules);

            $supervisor = User::where('user_role', 'sv')
                    ->where('office_division_id', $user->office_division_id)
                    ->orderByRaw("CASE 
                        WHEN unit_id IS NOT NULL AND unit_id = ? THEN 1
                        WHEN unit_id IS NULL THEN 2
                        ELSE 3
                    END", [$user->unit_id])
                    ->first();
        
            if(!$supervisor){
                $this->resetVariables();
                $this->dispatch('swal', [
                    'title' => 'No assigned supervisor for your division or unit. Please contact the administrator for assistance.',
                    'icon' => 'error'
                ]);
                return;
            }

            // Handle file upload
            $attachmentPath = null;
            $originalFileName = null;
            
            if ($this->attachment) {
                // Store the file
                $attachmentPath = $this->attachment->store('official-business-attachments', 'public');
                $originalFileName = $this->attachment->getClientOriginalName();
                
                // If editing and there's an existing file, delete it
                if (!$this->addOB && $this->existingAttachment) {
                    if (Storage::disk('public')->exists($this->existingAttachment)) {
                        Storage::disk('public')->delete($this->existingAttachment);
                    }
                }
            } elseif (!$this->addOB) {
                // Keep existing attachment if not uploading new one during edit
                $attachmentPath = $this->existingAttachment;
            }

            if($this->addOB){
                // Generate a 12-digit random reference number
                $referenceNumber = str_pad(random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);

                $ob = OB::create([
                    'user_id' => $user->id,
                    'reference_number' => $referenceNumber,
                    'company' => $this->company,
                    'address' => $this->address,
                    'lat' => $this->newLatitude,        
                    'lng' => $this->newLongitude,        
                    'duration' => $this->duration,  
                    'time_start' => $this->startTime,  
                    'time_end' => $this->endTime,  
                    'purpose' => $this->purpose,  
                    'sup_approver' => $supervisor->id,  
                    'sup_disapprover' => $supervisor->id,  
                    'attachment' => $attachmentPath,
                    'attachment_original_name' => $originalFileName,
                ]);

                // Create date records for each selected date
                foreach($this->dates as $date) {
                    OfficialBusinessDate::create([
                        'official_business_id' => $ob->id,
                        'date' => $date,
                    ]);
                }

                // Create a notification entry
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'obrequest',
                    'notif' => 'obrequest',
                    'read' => 0,
                ]);
            } else {
                $ob = OB::where('id', $this->editId)->first();
                if($ob){
                    $updateData = [
                        'company' => $this->company,
                        'address' => $this->address,
                        'lat' => $this->newLatitude,        
                        'lng' => $this->newLongitude,        
                        'duration' => $this->duration,
                        'time_start' => $this->startTime,  
                        'time_end' => $this->endTime,  
                        'purpose' => $this->purpose,
                    ];

                    // Only update attachment fields if a new file was uploaded
                    if ($this->attachment) {
                        $updateData['attachment'] = $attachmentPath;
                        $updateData['attachment_original_name'] = $originalFileName;
                    }

                    $ob->update($updateData);

                    // Delete existing dates and create new ones
                    $ob->dates()->delete();
                    
                    foreach($this->dates as $date) {
                        OfficialBusinessDate::create([
                            'official_business_id' => $ob->id,
                            'date' => $date,
                        ]);
                    }
                }
            }

            $this->resetVariables();
            $this->dispatch('swal', [
                'title' => 'Official Business ' . ($this->addOB ? 'added' : 'updated') . ' successfully',
                'icon' => 'success'
            ]);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function deleteData(){
        try{
            $ob = OB::where('id', $this->deleteId)->first();
            if($ob){
                // Delete the attached file if it exists
                if ($ob->attachment && Storage::disk('public')->exists($ob->attachment)) {
                    Storage::disk('public')->delete($ob->attachment);
                }
                
                $ob->delete();
                $this->dispatch('swal', [
                    'title' => 'Official Business deleted successfully',
                    'icon' => 'success'
                ]);
            }else{
                $this->dispatch('swal', [
                    'title' => 'Official Business deletion was unsuccessful',
                    'icon' => 'error'
                ]);
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function downloadAttachment($obId)
    {
        try {
            $ob = OB::where('id', $obId)->where('user_id', Auth::id())->first();
            
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

    public function confirmPunch($id, $state, $verifyType){
        $this->punchObId = $id;
        $this->punchState = $state;
        $this->verifyType = $verifyType;
        $this->showConfirmation = true;
    }

    public function recordObAttendance(){
        try{
            $ob = OB::where('id', $this->punchObId)->first();
            if($ob){
                if($this->punchState == 'timeIn'){
                    if($this->hasObTimeIn){
                        $this->dispatch('swal', [
                            'title' => 'You have already recorded your time in for this Official Business',
                            'icon' => 'error'
                        ]);
                        return;
                    }
                    $ob->update([
                        'time_in' => now()->toTimeString(),
                    ]);
                }elseif($this->punchState == 'timeOut'){
                    if($this->hasObTimeOut){
                        $this->dispatch('swal', [
                            'title' => 'You have already recorded your time out for this Official Business',
                            'icon' => 'error'
                        ]);
                        return;
                    }
                    $ob->update([
                        'time_out' => now()->toTimeString(),
                    ]);
                }else{
                    $this->dispatch('swal', [
                        'title' => 'Invalid action',
                        'icon' => 'error'
                    ]);
                    return;
                }
                $this->dispatch('swal', [
                    'title' => 'Official Business attendance recorded successfully',
                    'icon' => 'success'
                ]);
            }else{
                $this->dispatch('swal', [
                    'title' => 'Official Business attendance recording was unsuccessful',
                    'icon' => 'error'
                ]);
            }
                   $this->ongoingObs = $this->ongoingOfficialBusinesses();
        $this->upcomingObs = $this->upcomingOfficialBusinesses();
            $this->resetVariables();
        }catch(Exception $e){
            throw $e;
        }
    }

    public function resetVariables(){
        $this->editOB = null;
        $this->addOB = null;
        $this->deleteId = null;
        $this->editId = null;
        $this->company = null;
        $this->address = null;
        $this->registeredLatitude = null;        
        $this->registeredLongitude = null;        
        $this->dates = [];  
        $this->dateInput = null;
        $this->startTime = null;  
        $this->endTime = null;  
        $this->purpose = null;
        $this->viewOB = null;
        $this->newLatitude = null;
        $this->newLongitude = null;
        $this->punchState = null;
        $this->verifyType = null;
        $this->showConfirmation = null;
        $this->attachment = null;
        $this->existingAttachment = null;
    }
}
