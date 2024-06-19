<?php

namespace App\View\Components\Education;

use Illuminate\View\Component;

class Weeks extends Component
{
    public $weeks;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($weeks)
    {
        $this->weeks = $weeks;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.education.weeks');
    }
}
