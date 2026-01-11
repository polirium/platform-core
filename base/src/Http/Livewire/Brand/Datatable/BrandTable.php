<?php

namespace Polirium\Core\Base\Http\Livewire\Brand\Datatable;

use Illuminate\Database\Eloquent\Builder;
use Polirium\Core\Base\Http\Models\Brand\Brand;
use Polirium\Core\Support\Http\Livewire\Tables\BaseTable;
use Polirium\Datatable\Button;
use Polirium\Datatable\Column;
use Polirium\Datatable\Components\SetUp\Detail;
use Polirium\Datatable\Components\SetUp\Exportable;
use Polirium\Datatable\Facades\Filter;
use Polirium\Datatable\Components\SetUp\Footer;
use Polirium\Datatable\Components\SetUp\Header;
use Polirium\Datatable\PowerGridFields;

final class BrandTable extends BaseTable
{
    public string $tableName = 'brand-table';
    public $tab = 1;

    protected function getListeners(): array
    {
        return array_merge(
            parent::getListeners(),
            [
                'refresh-datatable-brands' => '$refresh',
            ]
        );
    }

    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            (new Exportable('export'))->striped()->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            (new Header())->includeViewOnTop('core/base::brand.datatable.header')->showSearchInput(),
            (new Footer())->showPerPage()->showRecordCount(),

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
        return new PowerGridFields();
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
