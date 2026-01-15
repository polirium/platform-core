<?php

namespace Polirium\Core\Base\Http\Livewire\Users\Datatable;

use CoreSupport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use Polirium\Core\Base\Http\Models\Role;
use Polirium\Core\Base\Http\Models\User;
use Polirium\Core\Support\Http\Livewire\Tables\BaseTable;
use Polirium\Datatable\Button;
use Polirium\Datatable\Column;
use Polirium\Datatable\Components\SetUp\Exportable;
use Polirium\Datatable\Facades\Filter;
use Polirium\Datatable\Facades\PowerGrid;
use Polirium\Datatable\Facades\Rule;
use Polirium\Datatable\PowerGridFields;
use Polirium\Datatable\Traits\WithExport;

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
            PowerGrid::header()
                ->showSearchInput()
                ->showToggleColumns(),
            PowerGrid::footer()
                ->showPerPage(perPage: 25, perPageValues: [10, 25, 50, 100])
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return User::query()
            ->with(['roles']);
    }

    public function relationSearch(): array
    {
        return [
            'roles' => ['name'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('username')
            ->add('name', function (User $model) {
                return Blade::render(<<<HTML
                    <x-ui.table::column.user name="$model->name" avatar="$model->avatar" email="$model->email" />
                HTML);
            })
            ->add('email')
            ->add('phone')
            ->add('roles_list', function (User $model) {
                $badges = $model->roles->map(function ($role) {
                    $isAdmin = strtolower($role->name) === 'admin' || strtolower($role->name) === 'super admin';
                    $class = $isAdmin ? 'crm-role-badge admin' : 'crm-role-badge';
                    return "<span class='{$class} me-1'>{$role->name}</span>";
                })->join('');
                return $badges ?: '<span class="text-muted">—</span>';
            })
            ->add('status_badge', function (User $model) {
                $status = $model->status ?? 'active';
                if ($status === 'active') {
                    return '<span class="crm-status-badge active">Hoạt động</span>';
                }
                return '<span class="crm-status-badge inactive">Không hoạt động</span>';
            })
            ->add('super_admin_badge', function (User $model) {
                if ($model->super_admin) {
                    return '<span class="crm-role-badge admin"><i class="ti ti-shield-check me-1"></i>Super Admin</span>';
                }
                return '<span class="text-muted">—</span>';
            })
            ->add('created_at_formatted', function (User $model) {
                return CoreSupport::datetime($model->created_at ?? now());
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->hidden(),

            Column::make(__('Người dùng'), 'name')
                ->sortable()
                ->searchable(),

            Column::make(__('Username'), 'username')
                ->sortable()
                ->searchable()
                ->hidden(),

            Column::make(__('Email'), 'email')
                ->sortable()
                ->searchable()
                ->hidden(),

            Column::make(__('Điện thoại'), 'phone')
                ->sortable()
                ->searchable(),

            Column::make(__('Vai trò'), 'roles_list')
                ->contentClasses('text-nowrap'),

            Column::make(__('Trạng thái'), 'status_badge'),

            Column::make(__('Quyền đặc biệt'), 'super_admin_badge'),

            Column::make(__('Ngày tạo'), 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::action(__('Thao tác')),
        ];
    }

    public function filters(): array
    {
        $roles = Role::pluck('name', 'id')->toArray();

        return [
            Filter::inputText('name')
                ->placeholder(__('Tìm theo tên...'))
                ->operators(['contains']),

            Filter::inputText('email')
                ->placeholder(__('Tìm theo email...'))
                ->operators(['contains']),

            Filter::inputText('phone')
                ->placeholder(__('Tìm theo SĐT...'))
                ->operators(['contains']),

            Filter::select('role_filter', 'id')
                ->dataSource(Role::all())
                ->optionLabel('name')
                ->optionValue('id')
                ->builder(function (Builder $query, mixed $value) {
                    return $query->whereHas('roles', fn($q) => $q->where('roles.id', $value));
                }),

            Filter::boolean('super_admin')
                ->label(__('Super Admin'), __('Không')),

            Filter::select('status', 'status')
                ->dataSource([
                    ['id' => 'active', 'name' => __('Hoạt động')],
                    ['id' => 'inactive', 'name' => __('Không hoạt động')],
                ])
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::datetimepicker('created_at'),
        ];
    }

    public function actions(User $row): array
    {
        $actions = [];

        // Detail action
        $actions[] = Button::add('detail')
            ->slot('<i class="ti ti-eye"></i>')
            ->id()
            ->class('crm-action-btn')
            ->attributes([
                'onclick' => "Livewire.dispatch('show-modal-detail-user', {id: {$row->id}});",
                'title' => __('Chi tiết'),
            ]);

        if (auth()->user()->can('users.edit')) {
            $actions[] = Button::add('edit')
                ->slot('<i class="ti ti-edit"></i>')
                ->id()
                ->class('crm-action-btn')
                ->attributes([
                    'onclick' => "Livewire.dispatch('show-modal-edit-user', {id: {$row->id}});",
                    'title' => __('Sửa'),
                ]);
        }

        if (auth()->user()->can('users.delete')) {
            $actions[] = Button::add('delete')
                ->slot('<i class="ti ti-trash"></i>')
                ->id()
                ->class('crm-action-btn')
                ->attributes([
                    'onclick' => "Livewire.dispatch('show-modal-delete-user', {id: {$row->id}});",
                    'title' => __('Xóa'),
                ]);
        }

        if (auth()->user()->can('users.impersonate') && $row->id !== auth()->id() && $row->canBeImpersonated()) {
            $actions[] = Button::add('impersonate')
                ->slot('<i class="ti ti-login"></i>')
                ->id()
                ->class('crm-action-btn')
                ->route('impersonate', ['id' => $row->id])
                ->attributes(['title' => __('Đăng nhập')]);
        }

        return $actions;
    }

    public function actionRules($row): array
    {
        return [
            Rule::button('delete')
                ->when(fn ($row) => $row->super_admin || $row->id === auth()->id())
                ->hide(),
        ];
    }

    public function onUpdatedToggleable(string|int $id, string $field, string $value): void
    {
        User::find($id)->update([$field => $value]);
        $this->dispatch('pg:eventRefresh-usersTable');
    }
}
