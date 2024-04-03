<?php

return [
    'user-table' => [
        'class' => \Polirium\Core\Base\Http\Livewire\Tables\UserTable::class,
        'alias' => 'core/base::user-table',
        'description' => 'User Table',
    ],
    'script-action-ui' => [
        'class' => \Polirium\Core\Base\Http\Livewire\ScriptAction\UIScriptActionComponent::class,
        'alias' => 'core/ui::script-action-ui.script',
        'description' => 'Scripts Action UI',
    ],
];
