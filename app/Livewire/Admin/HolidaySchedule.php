<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Holiday;
use App\Models\PhilippineRegions;
use Livewire\WithPagination;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Holiday Schedule')]
class HolidaySchedule extends Component
{
    use WithPagination;

    public $holidayId;
    public $description;
    public $holiday_date;
    public $type;
    public $region_id;
    public $isModalOpen = false;
    public $isEditMode = false;
    public $confirmingHolidayDeletion = false;
    public $holidayToDelete;

    // Filter properties
    public $filterRegion = '';
    public $filterType = '';

    protected $rules = [
        'description' => 'required|string|max:255',
        'holiday_date' => 'required|date',
        'type' => 'required|string|in:Regular,Special',
        'region_id' => 'nullable|exists:philippine_regions,id',
    ];

    protected $messages = [
        'description.required' => 'Holiday name is required.',
        'holiday_date.required' => 'Holiday date is required.',
        'type.required' => 'Holiday type is required.',
        'type.in' => 'Holiday type must be either Regular or Special.',
        'region_id.exists' => 'Selected region is invalid.',
    ];

    public function mount()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.holiday-schedule', [
            'holidays' => $this->loadHolidays(),
            'regions' => PhilippineRegions::orderBy('region_description', 'asc')->get(),
        ]);
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->isModalOpen = true;
        $this->isEditMode = false;
        $this->resetInputFields();
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    public function saveHoliday()
    {
        $this->validate();

        Holiday::updateOrCreate(
            ['id' => $this->holidayId],
            [
                'description' => $this->description,
                'holiday_date' => $this->holiday_date,
                'type' => $this->type,
                'region_id' => $this->region_id,
            ]
        );

        $message = $this->holidayId ? 'Holiday updated successfully.' : 'Holiday created successfully.';
        
        $this->dispatch('swal', [
            'title' => $message,
            'icon' => 'success'
        ]);

        $this->closeModal();
        $this->resetPage();
    }

    public function edit($id)
    {
        $holiday = Holiday::findOrFail($id);
        $this->holidayId = $id;
        $this->description = $holiday->description;
        $this->holiday_date = $holiday->holiday_date->format('Y-m-d');
        $this->type = $holiday->type;
        $this->region_id = $holiday->region_id;
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function confirmDelete($id)
    {
        $this->holidayToDelete = $id;
        $this->confirmingHolidayDeletion = true;
    }

    public function deleteConfirmed()
    {
        Holiday::find($this->holidayToDelete)->delete();
        $this->confirmingHolidayDeletion = false;
        
        $this->dispatch('swal', [
            'title' => 'Holiday deleted successfully!',
            'icon' => 'success'
        ]);
        
        $this->resetPage();
    }

    public function closeConfirmationModal()
    {
        $this->confirmingHolidayDeletion = false;
    }

    public function updatedFilterRegion()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->filterRegion = '';
        $this->filterType = '';
        $this->resetPage();
    }

    private function resetInputFields()
    {
        $this->holidayId = null;
        $this->description = '';
        $this->holiday_date = null;
        $this->type = '';
        $this->region_id = null;
        $this->isEditMode = false;
    }

    private function loadHolidays()
    {
        $currentYear = Carbon::now()->year;

        $query = Holiday::with('region')
            ->whereYear('holiday_date', '>=', $currentYear);

        // Apply filters
        if ($this->filterRegion) {
            if ($this->filterRegion === 'nationwide') {
                $query->whereNull('region_id');
            } else {
                $query->where('region_id', $this->filterRegion);
            }
        }

        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        return $query->orderBy('holiday_date', 'asc')->paginate(10);
    }
}