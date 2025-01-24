<?php

use Polirium\Core\Base\Http\Livewire\Branch\Datatable\BranchTable;
use Polirium\Core\Base\Http\Livewire\Branch\Modal\ModalCreateBranchComponent;
use Polirium\Core\Base\Http\Livewire\Branch\Modal\ModalCreateBranchTakingAddressComponent;
use Polirium\Core\Base\Http\Livewire\Brand\Datatable\BrandTable;
use Polirium\Core\Base\Http\Livewire\Brand\Modal\ModalCreateBrandComponent;
use Polirium\Core\Base\Http\Livewire\Roles\Datatable\RoleTable;
use Polirium\Core\Base\Http\Livewire\Roles\Modal\ModalCreateRoleComponent;

/**
 * Livewire User Components
 */
use Polirium\Core\Base\Http\Livewire\Users\Modal\ModalCreateUserComponent;
use Polirium\Core\Base\Http\Livewire\Users\Modal\ModalDeleteUserComponent;

return [
    /**
     * User Manager
     */
    'user-table' => [
        'class' => \Polirium\Core\Base\Http\Livewire\Tables\UserTable::class,
        'alias' => 'core/base::user-table',
        'description' => 'User Table',
    ],
    'user.modal.create' => [
        'class' => ModalCreateUserComponent::class,
        'alias' => 'core/base::user.modal.create',
        'description' => 'User Modal Create',
    ],
    'user.modal.delete' => [
        'class' => ModalDeleteUserComponent::class,
        'alias' => 'core/base::user.modal.delete',
        'description' => 'User Modal Delete',
    ],


    'script-action-ui' => [
        'class' => \Polirium\Core\Base\Http\Livewire\ScriptAction\UIScriptActionComponent::class,
        'alias' => 'core/ui::script-action-ui.script',
        'description' => 'Scripts Action UI',
    ],

    // Chi nhánh
    'branch-table' => [
        'class' => BranchTable::class,
        'alias' => 'core/base::branch-table',
        'description' => 'Branch Table',
    ],
    'branch-modal-create' => [
        'class' => ModalCreateBranchComponent::class,
        'alias' => 'core/base::branch.modal.modal-create-branch',
        'description' => 'Modal create branch',
    ],
    'branch-modal-create-taking-address' => [
        'class' => ModalCreateBranchTakingAddressComponent::class,
        'alias' => 'core/base::branch.modal.modal-create-branch-taking-address',
        'description' => 'Modal create branch taking address',
    ],
    // End Chi nhánh

    // Thương hiệu
    'brand-table' => [
        'class' => BrandTable::class,
        'alias' => 'core/base::brand-table',
        'description' => 'Brand Table',
    ],
    'brand-modal-create' => [
        'class' => ModalCreateBrandComponent::class,
        'alias' => 'core/base::brand.modal.modal-create-brand',
        'description' => 'Modal create brand',
    ],
    // End Thương hiệu

    // Phân quyền
    'role-table' => [
        'class' => RoleTable::class,
        'alias' => 'core/base::role-table',
        'description' => 'Role Table',
    ],
    'modal-create-role' => [
        'class' => ModalCreateRoleComponent::class,
        'alias' => 'core/base::roles.modal.modal-create-role',
        'description' => 'Modal create role',
    ],
    // End Phân quyền
];
