<?php

namespace Polirium\Core\Base\Http\Livewire\Roles\Datatable;

use CoreSupport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use Polirium\Core\Base\Http\Models\Role;
use Polirium\Core\Base\Http\Models\User;
use Polirium\Core\Support\Http\Livewire\Tables\BaseTable;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class RoleTable extends BaseTable
{
    use WithExport;

    public string $tableName = 'roles-table';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::exportable(fileName: 'roles')
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            PowerGrid::header()->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount()
                ->includeViewOnBottom('core/base::roles.datatable.footer')
                ,
        ];
    }

    public function header(): array
    {
        return [];
    }

    public function datasource(): Builder
    {
        return Role::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id');
    }

    public function columns(): array
    {
        return [
            Column::make(trans('core/base::general.id'), 'id'),
            Column::make(trans('core/base::role.name'), 'name'),
            Column::action(trans('core/base::general.action')),
        ];
    }

    public function filters(): array
    {
        return [
            // Filter::inputText('username')->operators(['contains']),
            // Filter::boolean('super_admin'),
            // Filter::datetimepicker('created_at'),
        ];
    }

    public function actions(Role $row): array
    {
        $actions = [];

        if (auth()->user()->can('roles.edit')) {
            $actions[] = Button::add('edit')
                ->slot('<i class="ti ti-edit me-1"></i>' . __('Sửa'))
                ->class('btn btn-sm btn-primary')
                ->dispatch('show-modal-create-role', ['id' => $row->id]);
        }

        if (auth()->user()->can('roles.delete')) {
            $actions[] = Button::add('delete')
                ->slot('<i class="ti ti-trash me-1"></i>' . __('Xóa'))
                ->class('btn btn-sm btn-outline-danger')
                ->dispatch('show-modal-delete-role', ['id' => $row->id]);
        }

        return $actions;
    }

    public function actionRules($row): array
    {
        return [

         ];
    }
}
