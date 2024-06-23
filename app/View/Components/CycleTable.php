<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CycleTable extends Component
{
    public $cycles, $plan, $const, $subjectNotes;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($cycles, $plan, $const, $subjectNotes)
    {
        $this->cycles = $cycles;
        $this->plan = $plan;
        $this->const = $const;
        $this->subjectNotes = $subjectNotes;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.cycle-table');
    }
}
