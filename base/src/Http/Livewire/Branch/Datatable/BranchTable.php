<?php

namespace Polirium\Core\Base\Http\Livewire\Branch\Datatable;

use Illuminate\Database\Eloquent\Builder;
use Polirium\Core\Base\Http\Models\Branch\Branch;
use Polirium\Core\Support\Http\Livewire\Tables\BaseTable;
use Polirium\Core\UI\Facades\Assets;
use Polirium\Datatable\ActionButtons;
use Polirium\Datatable\Column;
use Polirium\Datatable\Components\SetUp\Exportable;
use Polirium\Datatable\Facades\Filter;
use Polirium\Datatable\Facades\PowerGrid;
use Polirium\Datatable\PowerGridFields;

final class BranchTable extends BaseTable
{
    public string $tableName = 'table-branches';

    public $tab = 1;

    public function mount(): void
    {
        Assets::loadCss(['professional-table', 'professional-detail']);
        parent::mount();
    }

    protected function getListeners(): array
    {
        return array_merge(
            parent::getListeners(),
            [
                'refresh-datatable-branches' => '$refresh',
            ]
        );
    }

    public function setUp(): array
    {
        return [
            PowerGrid::exportable('export')->striped()->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            PowerGrid::header()
                ->includeViewOnTop('core/base::branch.datatable.header')
                ->showSearchInput()
                ->showToggleColumns(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
            PowerGrid::detail()
                ->view('core/base::branch.datatable.detail')
                ->showCollapseIcon()
                ->collapseOthers(),
        ];
    }

    public function datasource(): Builder
    {
        return Branch::query()
        ->with('users:id,name', 'takingAddresses', 'province', 'district', 'ward')
        ->withCount(['users']);
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
            })
            ->add('status_name', function ($row) {
                if ($row->status) {
                    return '<span class="business-status-badge active">
                        <span class="business-status-badge-dot"></span>
                        ' . trans('core/base::general.active') . '
                    </span>';
                }

                return '<span class="business-status-badge inactive">
                    <span class="business-status-badge-dot"></span>
                    ' . trans('core/base::general.inactive') . '
                </span>';
            });
    }

    public function columns(): array
    {
        return [
            Column::make(trans('core/base::general.branch_name'), 'name')->sortable()->searchable(),
            Column::make(trans('core/base::general.address'), 'address')->sortable()->searchable(),
            Column::make(trans('core/base::general.phone'), 'phone')->sortable()->searchable(),
            Column::make(trans('core/base::general.user_count'), 'amount')->sortable()->searchable(),
            Column::make(trans('core/base::general.status'), 'status_name')->sortable()->searchable(),
            Column::action(trans('core/base::general.action')),
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
        // Toggle status: the icon shows the current state, the label the action to take.
        $toggleLabel = $row->status ? trans('core/base::general.deactivate') : trans('core/base::general.activate');

        return ActionButtons::make(
            ActionButtons::edit('show-modal-create-branch', $row->id),
            ActionButtons::custom('toggle', $row->status ? 'toggle-right' : 'toggle-left', $toggleLabel)
                ->dispatch('toggleActive', ['id' => $row->id, 'status' => $row->status ? 0 : 1]),
        );
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

    #[\Livewire\Attributes\On('toggleActive')]
    public function toggleActive($id, $status): void
    {
        Branch::findOrFail($id)->update([
            'status' => $status,
        ]);

        $this->dispatch('refresh-datatable-branches');
    }
}
