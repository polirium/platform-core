<?php

namespace Polirium\Core\Base\Http\Livewire\Roles\Datatable;

use CoreSupport;
use Illuminate\Database\Eloquent\Builder;
use Polirium\Core\Base\Http\Models\Role;
use Polirium\Core\Base\Http\Models\User;
use Polirium\Core\Base\Traits\GetPermission;
use Polirium\Core\Support\Http\Livewire\Tables\BaseTable;
use Polirium\Core\UI\Facades\Assets;
use Polirium\Datatable\Button;
use Polirium\Datatable\Column;
use Polirium\Datatable\Components\SetUp\Exportable;
use Polirium\Datatable\Facades\Filter;
use Polirium\Datatable\Facades\PowerGrid;
use Polirium\Datatable\Facades\Rule;
use Polirium\Datatable\PowerGridFields;
use Polirium\Datatable\Traits\WithExport;

final class RoleTable extends BaseTable
{
    use WithExport, GetPermission;

    public string $tableName = 'roles-table';

    protected array $permissionFlags = [];

    protected array $permissionTree = [];

    public function mount(): void
    {
        parent::mount();
        Assets::loadCss(['professional-table', 'role-table']);

        // Pre-load permissions data
        $this->permissionFlags = $this->getAvailablePermissions();
        $this->permissionTree = $this->getPermissionTree($this->permissionFlags);
    }

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::exportable(fileName: 'roles')
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            PowerGrid::header()
                ->includeViewOnTop('core/base::roles.datatable.header')
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount()
                ->includeViewOnBottom('core/base::roles.datatable.footer'),
            PowerGrid::detail()
                ->showCollapseIcon()
                ->view('core/base::roles.datatable.detail')
                ->params([
                    'permissionFlags' => $this->permissionFlags,
                    'permissionTree' => $this->permissionTree,
                ]),
        ];
    }

    public function header(): array
    {
        return [];
    }

    public function datasource(): Builder
    {
        return Role::with('permissions');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        // Re-load permissions if empty (protected properties are not preserved across Livewire refreshes)
        if (empty($this->permissionFlags)) {
            $this->permissionFlags = $this->getAvailablePermissions();
            $this->permissionTree = $this->getPermissionTree($this->permissionFlags);
        }

        return PowerGrid::fields()
            ->add('id')
            ->add('permissions_count', function ($row) {
                return $row->permissions->count();
            })
            ->add('permissions_preview', function ($row) {
                $permissions = $row->permissions->pluck('name')->take(3);
                $count = $row->permissions->count();
                $preview = $permissions->map(function ($name) {
                    return trans($this->permissionFlags[$name]['name'] ?? $name);
                })->join(', ');

                if ($count > 3) {
                    $preview .= ' +' . ($count - 3) . ' ' . trans('core/base::general.more');
                }

                return $preview ?: trans('core/base::general.no_permissions');
            });
    }

    public function columns(): array
    {
        return [
            Column::make(trans('core/base::general.id'), 'id')
                ->sortable(),

            Column::make(trans('core/base::role.name'), 'name')
                ->sortable()
                ->searchable(),

            Column::add()
                ->title(trans('core/base::general.permissions'))
                ->field('permissions_preview'),

            Column::action(trans('core/base::general.action')),
        ];
    }

    public function filters(): array
    {
        return [];
    }

    public function actions(Role $row): array
    {
        $actions = [];

        if (auth()->user()->can('roles.edit')) {
            $actions[] = Button::add('edit')
                ->slot(tabler_icon('pencil', ['class' => 'icon']))
                ->class('btn btn-primary btn-icon btn-sm me-1')
                ->attributes(['aria-label' => trans('core/base::general.edit')])
                ->tooltip(trans('core/base::general.edit'))
                ->dispatch('show-modal-create-role', ['id' => $row->id]);
        }

        if (auth()->user()->can('roles.delete')) {
            $actions[] = Button::add('delete')
                ->slot(tabler_icon('trash', ['class' => 'icon']))
                ->class('btn btn-outline-danger btn-icon btn-sm')
                ->attributes(['aria-label' => trans('core/base::general.delete')])
                ->tooltip(trans('core/base::general.delete'))
                ->dispatch('show-modal-delete-role', ['id' => $row->id]);
        }

        return $actions;
    }

    public function actionRules($row): array
    {
        return [];
    }
}
