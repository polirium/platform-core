<?php

namespace Polirium\Core\Base\Http\Livewire\Brand\Datatable;

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
use Polirium\Core\Base\Http\Models\Brand\Brand;

final class BrandTable extends BaseTable
{
    public $tab = 1;

    protected function getListeners(): array
    {
        return array_merge(
            parent::getListeners(), 
            [
                'refresh-datatable-brands' => '$refresh'
            ]);
    }

    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            Exportable::make('export')->striped()->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->includeViewOnTop('core/base::brand.datatable.header')->showSearchInput(),
            Footer::make()->showPerPage()->showRecordCount(),

            // Detail::make()
            // ->view('core/base::brand.datatable.detail')
            // ->showCollapseIcon()
            // ->collapseOthers(),
        ];
    }

    public function datasource(): Builder
    {
        return Brand::query()
        ->with('user:id,name');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields();
    }

    public function columns(): array
    {
        return [
            Column::make(trans('Tên thương hiệu'), 'name')->sortable()->searchable(),
            Column::make(trans('Người tạo'), 'user.name')->sortable()->searchable(),
            Column::make(trans('Note'), 'note')->sortable()->searchable(),
            // Column::make(trans('Trạng thái'), 'status_name')->sortable()->searchable(),
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
            // Filter::inputText('username')->operators(['contains']),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Brand $row): array
    {
        return [
            Button::add('edit')
            ->slot(trans('Sửa'))
            ->id()
            ->class('btn btn-warning btn-sm')
            ->dispatch('show-modal-create-brand', ['id' => $row->id]),
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
}
