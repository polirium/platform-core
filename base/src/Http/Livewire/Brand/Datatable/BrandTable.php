<?php

namespace Polirium\Core\Base\Http\Livewire\Brand\Datatable;

use Illuminate\Database\Eloquent\Builder;
use Polirium\Core\Base\Http\Models\Brand\Brand;
use Polirium\Core\Support\Http\Livewire\Tables\BaseTable;
use Polirium\Core\UI\Facades\Assets;
use Polirium\Datatable\ActionButtons;
use Polirium\Datatable\Column;
use Polirium\Datatable\Components\SetUp\Detail;
use Polirium\Datatable\Components\SetUp\Exportable;
use Polirium\Datatable\Components\SetUp\Footer;
use Polirium\Datatable\Components\SetUp\Header;
use Polirium\Datatable\Facades\Filter;
use Polirium\Datatable\Facades\Rule;
use Polirium\Datatable\PowerGridFields;

final class BrandTable extends BaseTable
{
    public string $tableName = 'brand-table';
    public $tab = 1;

    public function mount(): void
    {
        parent::mount();
        Assets::loadCss('professional-table');
    }

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
            ->leftJoin('users', 'brands.user_id', '=', 'users.id')
            ->select([
                'brands.*',
                'users.name as user_name',
            ]);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return (new PowerGridFields())
            ->add('name')
            ->add('user_name')
            ->add('note');
    }

    public function columns(): array
    {
        return [
            Column::make(trans('core/base::general.brand_name'), 'name')->sortable()->searchable(),
            Column::make(trans('core/base::general.creator'), 'user_name')->sortable()->searchable(),
            Column::make(trans('core/base::general.note'), 'note')->sortable()->searchable(),
            // Column::make(trans('core/base::general.status'), 'status_name')->sortable()->searchable(),
            Column::action(trans('core/base::general.action')),
        ];
    }

    public function filters(): array
    {
        return [
            // Filter::inputText('username')->operators(['contains']),
        ];
    }

    #[\Livewire\Attributes\On('deleteBrand')]
    public function deleteBrand($id): void
    {
        $brand = Brand::find($id);
        if ($brand) {
            $brand->delete();
            $this->dispatch('refresh-datatable-brands');
        }
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Brand $row): array
    {
        return ActionButtons::make(
            ActionButtons::edit('show-modal-create-brand', $row->id),
            ActionButtons::delete('deleteBrand', $row->id, null, trans('core/base::general.delete_confirm')),
        );
    }

    public function actionRules($row): array
    {
        return [
            Rule::button('edit')
                ->when(fn ($row) => ! auth()->user()?->can('brands.edit'))
                ->hide(),

            Rule::button('delete')
                ->when(fn ($row) => ! auth()->user()?->can('brands.delete'))
                ->hide(),
        ];
    }
}
