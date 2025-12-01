<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AutoCode extends Component
{
    public string $name;
    public string $label;
    public ?string $value;
    public string $source;
    public string $checkUrl;
    public $currentId;
    public string $placeholder;
    public ?string $inputId;

    public function __construct(
        string $name = 'code',
        string $label = 'Mã sản phẩm',
        string $value = null,
        string $source = '#product_name',
        string $checkUrl = '',
        $currentId = null,
        string $placeholder = 'VD: SP-ABC-001',
        string $id = null
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->value = $value;
        $this->source = $source;
        $this->checkUrl = $checkUrl;
        $this->currentId = $currentId;
        $this->placeholder = $placeholder;
        $this->inputId = $id ?: $this->name;
    }

    public function render()
    {
        return view('components.auto-code');
    }
}
