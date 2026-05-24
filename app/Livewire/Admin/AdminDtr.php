<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('DTR')]
class AdminDtr extends Component
{
    public function render()
    {
        return view('livewire.admin.admin-dtr');
    }
}
