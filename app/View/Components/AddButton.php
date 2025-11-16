<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AddButton extends Component
{
    public $href;
    public $text;
    public $variant;
    public $size;
    public $icon;

    public function __construct(
        $href = '#',
        $text = 'Button',
        $variant = 'primary',
        $size = 'md',
        $icon = null,
    ) {
        $prefix = strtolower(session('role.branch.code') ?? session('role.company.code'));

        if (!str_starts_with($href, "/$prefix")) {
            $href = "/$prefix/" . ltrim($href, '/');
        }

        $this->href = $href;
        $this->text = $text;
        $this->variant = $variant;
        $this->size = $size;
        $this->icon = $icon;
    }

    public function render()
    {
        return view('components.add-button');
    }
}
