<?php

namespace Polirium\Core\UI\Theme;

use Polirium\Datatable\Themes\Theme;

class PoliPowerGrid extends Theme
{
    public string $name = 'tabler';

    public function layout(): array
    {
        return [
            'table' => $this->root().'.table-base',
            'table-content' => $this->root().'.table',
            'header' => $this->root().'.header',
            'pagination' => $this->root().'.pagination',
            'footer' => $this->root().'.footer',
        ];
    }

    public function table(): array
    {
        return [
            'layout' => [
                'base' => 'card',
                'div' => 'table-responsive',
                'table' => 'table table-vcenter text-nowrap datatable',
                'container' => 'card-table',
                'actions' => 'd-flex gap-2',
            ],

            'header' => [
                'thead' => '',
                'tr' => '',
                'th' => 'text-nowrap',
                'thAction' => '',
            ],

            'body' => [
                'tbody' => '',
                'tbodyEmpty' => '',
                'tr' => '',
                'td' => '',
                'tdEmpty' => 'p-2 text-nowrap',
                'tdSummarize' => 'text-dark-emphasis small px-3 py-2 lh-sm',
                'trSummarize' => '',
                'tdFilters' => '',
                'trFilters' => '',
                'tdActionsContainer' => 'text-end',
            ],
        ];
    }

    public function cols(): array
    {
        return [
            'div' => 'd-flex align-items-center gap-1',
        ];
    }

    public function footer(): array
    {
        return [
            'view' => $this->root().'.footer',
            'select' => 'form-select form-select-sm w-auto',
            'footer' => 'card-footer',
            'footer_with_pagination' => '',
        ];
    }

    public function toggleable(): array
    {
        return [
            'view' => $this->root().'.toggleable',
            'base' => 'form-check form-switch',
            'label' => 'form-check-label',
            'input' => 'form-check-input',
            'role' => 'switch',
        ];
    }

    public function editable(): array
    {
        return [
            'view' => $this->root().'.editable',
            'input' => 'form-control form-control-sm',
        ];
    }

    public function checkbox(): array
    {
        return [
            'th' => 'w-1',
            'base' => 'form-check',
            'label' => 'form-check-label',
            'input' => 'form-check-input m-0 align-middle',
        ];
    }

    public function radio(): array
    {
        return [
            'th' => '',
            'base' => 'form-check',
            'label' => 'form-check-label',
            'input' => 'form-check-input',
        ];
    }

    public function filterBoolean(): array
    {
        return [
            'view' => $this->root().'.filters.boolean',
            'base' => '',
            'select' => 'form-select form-select-sm',
        ];
    }

    public function filterDatePicker(): array
    {
        return [
            'base' => '',
            'view' => $this->root().'.filters.date-picker',
            'input' => 'form-control form-control-sm flatpickr-input',
        ];
    }

    public function filterMultiSelect(): array
    {
        return [
            'view' => $this->root().'.filters.multi-select',
            'base' => '',
            'select' => 'form-select form-select-sm',
        ];
    }

    public function filterNumber(): array
    {
        return [
            'view' => $this->root().'.filters.number',
            'input' => 'form-control form-control-sm',
        ];
    }

    public function filterSelect(): array
    {
        return [
            'view' => $this->root().'.filters.select',
            'base' => '',
            'select' => 'form-select form-select-sm',
        ];
    }

    public function filterInputText(): array
    {
        return [
            'view' => $this->root().'.filters.input-text',
            'base' => '',
            'select' => 'form-select form-select-sm mb-1',
            'input' => 'form-control form-control-sm',
        ];
    }

    public function searchBox(): array
    {
        return [
            'input' => 'form-control form-control-sm',
            'iconSearch' => 'icon',
            'iconClose' => 'icon',
        ];
    }
}
