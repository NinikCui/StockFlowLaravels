<?php

namespace App\View\Components;

use Illuminate\View\Component;

class kpiCard extends Component
{
    /**
     * Create a new component instance.
     */
    public $title;

    public $value;

    public $iconBg;

    public $iconColor;

    public $svg;

    public function __construct($title, $value, $iconBg = 'bg-gray-100', $iconColor = 'text-gray-600', $svg = '')
    {
        $this->title = $title;
        $this->value = $value;
        $this->iconBg = $iconBg;
        $this->iconColor = $iconColor;
        $this->svg = $svg;
    }

    public function render()
    {
        return view('components.kpi-card');
    }
}
