<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Audit Logs')]
class LogIndex extends Component
{
    public function render()
    {
        return view('livewire.log-index');
    }

}
