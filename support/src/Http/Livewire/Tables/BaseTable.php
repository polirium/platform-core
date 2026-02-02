<?php

namespace Polirium\Core\Support\Http\Livewire\Tables;

use Polirium\Datatable\Concerns\FilterBuilder;
use Polirium\Datatable\PowerGridComponent;
use Polirium\Datatable\Traits\WithExport;

class BaseTable extends PowerGridComponent
{
    use WithExport;
    use FilterBuilder;

    public string $bulkDeletePermission = '';

    public function bulkDelete(): void
    {
        if ($this->bulkDeletePermission && ! auth()->user()->can($this->bulkDeletePermission)) {
            $this->dispatch('error', trans('Bạn không có quyền thực hiện hành động này.'));
            return;
        }

        if (empty($this->checkboxValues)) {
            $this->dispatch('error', trans('Vui lòng chọn ít nhất một bản ghi.'));
            return;
        }

        $count = 0;
        foreach ($this->checkboxValues as $id) {
            $this->onDelete($id);
            $count++;
        }

        $this->checkboxValues = [];
        $this->checkboxAll = false;

        $this->dispatch('success', trans('Đã xóa :count bản ghi thành công.', ['count' => $count]));

        // Refresh table
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
    }

    public function onDelete(string|int $id): void
    {
        if (method_exists($this, 'delete')) {
            $this->delete($id);
            return;
        }

        // Try to identify model from datasource
        try {
            $query = $this->datasource();
            if (method_exists($query, 'find')) {
                 $model = $query->find($id);
                 $model?->delete();
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
