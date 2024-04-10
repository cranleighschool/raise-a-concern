<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class SelfReflection extends Component
{
    private object $reflection;

    private object $current;

    public int $reportCycleId;

    public function mount(object $reflection, object $current, int $reportCycleId): void
    {
        $this->reflection = $reflection;
        $this->current = $current;
        $this->reportCycleId = $reportCycleId;
    }

    public function render(): View
    {
        return view('livewire.self-reflection', [
            'reflection' => $this->reflection,
            'current' => $this->current,
            'reportCycleId' => $this->reportCycleId,
        ]);
    }
}
