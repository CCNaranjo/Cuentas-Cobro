<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Logo extends Component
{
    public $primaryColor;
    public $secondaryColor;
    public $showSymbol;
    public $showText;
    /**
     * Create a new component instance.
     */
    public function __construct($primaryColor = null, $secondaryColor = null, $showSymbol = true, $showText = true)
    {
        $this->primaryColor = $primaryColor; // Color por defecto si no se proporciona
        $this->secondaryColor = $secondaryColor; // Color por defecto si no se proporciona

        $this->showSymbol = filter_var($showSymbol, FILTER_VALIDATE_BOOLEAN);
        $this->showText = filter_var($showText, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.logo');
    }
}
