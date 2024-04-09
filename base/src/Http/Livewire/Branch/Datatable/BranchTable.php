<?php

namespace Polirium\Core\Base\Http\Livewire\Branch\Datatable;

use Illuminate\Database\Eloquent\Builder;
use Polirium\Core\Support\Http\Livewire\Tables\BaseTable;
use Polirium\LivewireDatatable\Button;
use Polirium\LivewireDatatable\Column;
use Polirium\LivewireDatatable\Detail;
use Polirium\LivewireDatatable\Exportable;
use Polirium\LivewireDatatable\Facades\Filter;
use Polirium\LivewireDatatable\Footer;
use Polirium\LivewireDatatable\Header;
use Polirium\LivewireDatatable\PowerGrid;
use Polirium\LivewireDatatable\PowerGridFields;
use Polirium\Core\Base\Http\Models\Branch\Branch;

final class BranchTable extends BaseTable
{
    public $tab = 1;

    protected function getListeners(): array
    {
        return array_merge(
            parent::getListeners(), 
            [
                'refresh-datatable-branches' => '$refresh'
            ]);
    }

    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            Exportable::make('export')->striped()->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->includeViewOnTop('core/base::branch.datatable.header')->showSearchInput(),
            Footer::make()->showPerPage()->showRecordCount(),

            Detail::make()
            ->view('core/base::branch.datatable.detail')
            ->showCollapseIcon()
            ->collapseOthers(),
        ];
    }

    public function datasource(): Builder
    {
        return Branch::query()
        ->with('users:id,name', "takingAddresses")
        ->withCount(["users"]);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('amount', function ($row) {
                return $row->users_count;
            });
    }

    public function columns(): array
    {
        return [
            Column::make(trans('Tên chi nhánh'), 'name')->sortable()->searchable(),
            Column::make(trans('Địa chỉ'), 'address')->sortable()->searchable(),
            Column::make(trans('Điện thoại'), 'phone')->sortable()->searchable(),
            Column::make(trans('SL người dùng'), 'amount')->sortable()->searchable(),
            Column::make(trans('Trạng thái'), 'status_name')->sortable()->searchable(),
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('username')->operators(['contains']),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Branch $row): array
    {
        return [
            Button::add('edit')
            ->slot(trans('Sửa'))
            ->id()
            ->class('btn btn-warning btn-sm')
            ->dispatch('show-modal-create-branch', ['id' => $row->id]),
        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */

    public function toggleActive($id, $status)
    {
        Branch::findOrFail($id)->update([
            "status" => $status
        ]);
    }
}
