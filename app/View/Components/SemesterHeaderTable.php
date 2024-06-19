<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SemesterHeaderTable extends Component
{
    public $plan;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($plan)
    {
        $this->plan = $plan;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.semester-header-table');
    }
}
