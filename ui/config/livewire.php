<?php

use Polirium\Core\UI\Http\Livewire\CurrencyComponent;
use Polirium\Core\UI\Http\Livewire\InlineTextComponent;
use Polirium\Core\UI\Http\Livewire\SelectTextComponent;

return [
    'inline-text' => [
        'class' => InlineTextComponent::class,
        'alias' => 'core/ui::inline.text',
        'description' => 'Inline edit text',
    ],
    'inline-currency' => [
        'class' => CurrencyComponent::class,
        'alias' => 'core/ui::inline.currency',
        'description' => 'Inline edit currency',
    ],
    'inline-select' => [
        'class' => SelectTextComponent::class,
        'alias' => 'core/ui::inline.select',
        'description' => 'Inline edit select',
    ],
];
