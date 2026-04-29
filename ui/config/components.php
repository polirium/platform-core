<?php

use Polirium\Core\UI\View\Components\Button;

// Class-based components are auto-registered via componentNamespace in UIServiceProvider
// componentNamespace('Polirium\\Core\\UI\\View\\Components', 'ui') auto-registers:
// - Button class as x-ui::button
//
// This file registers aliases for dot notation (x-ui.button)

return [
    'button' => [
        'class' => Button::class,
        'alias' => 'ui.button',
    ],
];
