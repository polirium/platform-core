<?php

namespace Polirium\Core\Base\Http\Livewire\Users\Datatable;

use CoreSupport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
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

final class UserTable extends BaseTable
{
    use WithExport;

    public string $tableName = 'usersTable';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::exportable(fileName: 'users')
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            PowerGrid::header()->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return User::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('username')
            ->add('name', function (User $model) {
                return  Blade::render(<<<HTML
                    <x-ui.table::column.user name="$model->name" avatar="$model->avatar" email="$model->email" />
                HTML);
            })
            ->add('email')
            ->add('super_admin')
            ->add('created_at_formatted', function (User $model) {
                return CoreSupport::datetime($model->created_at ?? now());
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Username', 'username')
                ->sortable()
                ->searchable(),
            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),
            Column::make('Super admin', 'super_admin')
                ->toggleable(),
            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('username')->operators(['contains']),
            Filter::inputText('first_name')->operators(['contains']),
            Filter::inputText('last_name')->operators(['contains']),
            Filter::inputText('name')->operators(['contains']),
            Filter::inputText('email')->operators(['contains']),
            Filter::boolean('super_admin'),
            Filter::datetimepicker('created_at'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(User $row): array
    {
        return [
            Button::add('edit')
                ->slot(trans('Edit'))
                ->id()
                ->class('btn btn-success')
                ->dispatch('show-modal-edit-user', ['id' => $row->id]),
            Button::add('delete')
                ->slot(trans('Delete'))
                ->id()
                ->class('btn btn-danger')
                ->attributes([
                    'onclick' => "Livewire.dispatch('show-modal-delete-user', {id: $row->id});",
                ]),
        ];
    }

    public function actionRules($row): array
    {
        return [
            Rule::button('delete')
                 ->when(fn ($row) => $row->super_admin)
                 ->hide(),
            Rule::toggleable('super_admin')
                 ->when(fn ($row) => $row->id === 1)
                 ->hide(),
         ];
    }

    public function onUpdatedToggleable(string|int $id, string $field, string $value): void
    {
        User::find($id)->update([$field => $value]);
        $this->dispatch('pg:eventRefresh-usersTable');
    }

}
