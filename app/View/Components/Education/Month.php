<?php

namespace App\View\Components\Education;

use Illuminate\View\Component;

class Month extends Component
{
    public $month;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($month)
    {
        $this->month = $month;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.education.month');
    }
}
