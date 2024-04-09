<?php

namespace App\Livewire;

use Livewire\Component;

class SelfReflection extends Component
{
    public object $reflection;
    public object $current;
    public int $reportCycleId;

    public function mount(object $reflection, object $current, int $reportCycleId)
    {
        $this->reflection = $reflection;
        $this->current = $current;
        $this->reportCycleId = $reportCycleId;
    }

    public function render()
    {
        return view('livewire.self-reflection', [
            'reflection' => $this->reflection,
            'current' => $this->current,
            'reportCycleId' => $this->reportCycleId,
        ]);
    }
}
