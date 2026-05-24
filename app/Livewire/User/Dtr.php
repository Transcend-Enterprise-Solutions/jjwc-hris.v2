<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('DTR')]
class Dtr extends Component
{
    public function render()
    {
        return view('livewire.user.dtr');
    }
}
