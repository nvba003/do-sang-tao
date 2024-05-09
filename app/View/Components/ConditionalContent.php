<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ConditionalContent extends Component
{
    public $condition;

    public function __construct($condition)
    {
        $this->condition = $condition;
    }

    public function render()
    {
        return view('components.conditional-content');
    }
}
