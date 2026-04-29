<?php

namespace Polirium\Core\Base\Http\Livewire\Table;

use Illuminate\Database\Eloquent\Builder;
use Polirium\Core\Support\Http\Livewire\Tables\BaseTable;
use Polirium\Datatable\Column;
use Polirium\Datatable\Facades\Filter;
use Polirium\Datatable\Facades\PowerGrid;
use Polirium\Datatable\PowerGridFields;
use Spatie\Activitylog\Models\Activity;

final class ActivityLogTable extends BaseTable
{
    public string $tableName = 'activity-log-table';

    public string $currentEvent = 'all';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput()
                ->showToggleColumns()
                ->includeViewOnTop('core/base::activity-log.table.header'),
            PowerGrid::footer()->showPerPage()->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        $query = Activity::query()->orderByDesc('id');

        if ($this->currentEvent !== 'all') {
            $query->where('event', $this->currentEvent);
        }

        return $query;
    }

    public function setEvent(string $event): void
    {
        $this->currentEvent = $event;
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('description')
            ->add('subject_type', function (Activity $model) {
                return class_basename($model->subject_type);
            })
            ->add('causer_id', function (Activity $model) {
                return $model->causer ? $model->causer->name : 'System';
            })
            ->add('created_at_formatted', fn (Activity $model) => core_format_date($model->created_at))
            ->add('changes_html', function (Activity $model) {
                return view('core/base::activity-log.table.changes', ['row' => $model])->render();
            });
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->sortable()->searchable(),
            Column::make(trans('core/base::general.description'), 'description')->searchable()->sortable(),
            Column::make(trans('core/base::general.subject'), 'subject_type')->searchable()->sortable(),
            Column::make(trans('core/base::general.performed_by'), 'causer_id'),
            Column::make(trans('core/base::general.created_at'), 'created_at_formatted', 'created_at')->sortable(),
            Column::make(trans('core/base::general.change_details'), 'changes_html'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('description', 'description')
                ->placeholder('Tìm theo mô tả')
                ->operators(['contains']),

            Filter::inputText('subject_type', 'subject_type')
                ->placeholder('Tìm theo đối tượng')
                ->operators(['contains']),

            Filter::datetimepicker('created_at'),
        ];
    }
}
