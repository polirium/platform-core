<?php

namespace Polirium\Core\Base\Http\Livewire\Users\Datatable;

use CoreSupport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use Livewire\Attributes\On;
use Polirium\Core\Base\Http\Models\Role;
use Polirium\Core\Base\Http\Models\User;
use Polirium\Core\Support\Http\Livewire\Tables\BaseTable;
use Polirium\Datatable\ActionButtons;
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

    public string $tab = 'active'; // 'active' hoặc 'deleted'

    #[On('switch-user-tab')]
    public function switchTab(string $tab): void
    {
        $this->tab = $tab;
        // Reset pagination and refresh the data grid
        $this->dispatch('pg:eventRefresh-usersTable');
    }

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
        $query = User::query()
            ->with(['roles']);

        if ($this->tab === 'deleted') {
            return $query->onlyTrashed();
        }

        // Active users (default behavior includes SoftDeletes scope)
        return $query;
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

            Column::make(trans('core/base::general.users'), 'name')
                ->sortable()
                ->searchable(),

            Column::make(trans('core/base::general.username'), 'username')
                ->sortable()
                ->searchable()
                ->hidden(),

            Column::make(trans('core/base::general.email'), 'email')
                ->sortable()
                ->searchable()
                ->hidden(),

            Column::make(trans('core/base::general.phone'), 'phone')
                ->sortable()
                ->searchable(),

            Column::make(trans('core/base::general.role'), 'roles_list')
                ->contentClasses('text-nowrap'),

            Column::make(trans('core/base::general.status'), 'status_badge'),

            Column::make(trans('core/base::general.super_admin'), 'super_admin_badge'),

            Column::make(trans('core/base::general.created_at'), 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::action(trans('core/base::general.action')),
        ];
    }

    public function filters(): array
    {
        $roles = Role::pluck('name', 'id')->toArray();

        return [
            Filter::inputText('name')
                ->placeholder(trans('core/base::general.search_by_name_placeholder'))
                ->operators(['contains']),

            Filter::inputText('email')
                ->placeholder(trans('core/base::general.search_by_email_placeholder'))
                ->operators(['contains']),

            Filter::inputText('phone')
                ->placeholder(trans('core/base::general.search_by_phone_placeholder'))
                ->operators(['contains']),

            Filter::select('role_filter', 'id')
                ->dataSource(Role::all())
                ->optionLabel('name')
                ->optionValue('id')
                ->builder(function (Builder $query, mixed $value) {
                    return $query->whereHas('roles', fn ($q) => $q->where('roles.id', $value));
                }),

            Filter::boolean('super_admin')
                ->label(trans('core/base::general.super_admin'), trans('core/base::general.no')),

            Filter::select('status', 'status')
                ->dataSource([
                    ['id' => 'active', 'name' => trans('core/base::general.active')],
                    ['id' => 'inactive', 'name' => trans('core/base::general.inactive')],
                ])
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::datetimepicker('created_at'),
        ];
    }

    public function actions(User $row): array
    {
        // Every action opens its own modal (delete/restore/force delete confirm there).
        $modal = fn (string $event): array => ['onclick' => "Livewire.dispatch('{$event}', {id: {$row->id}});"];

        if ($this->tab === 'active') {
            // For active users: view, edit, impersonate and delete
            $canImpersonate = auth()->user()->can('users.impersonate') && $row->id !== auth()->id() && $row->canBeImpersonated();

            return ActionButtons::make(
                ActionButtons::custom('view', 'eye', trans('core/base::general.details'))
                    ->attributes($modal('show-modal-detail-user')),
                ActionButtons::custom('edit', 'pencil', trans('core/base::general.edit'), false, 'users.edit')
                    ?->attributes($modal('show-modal-edit-user')),
                $canImpersonate
                    ? ActionButtons::custom('impersonate', 'user-share', trans('core/base::general.login'))
                        ->route('impersonate', ['id' => $row->id])
                    : null,
                ActionButtons::custom('delete', 'trash', trans('core/base::general.delete'), true, 'users.delete')
                    ?->attributes($modal('show-modal-delete-user')),
            );
        }

        // For deleted users: view, restore and permanently delete
        return ActionButtons::make(
            ActionButtons::custom('view', 'eye', trans('core/base::general.details'))
                ->attributes($modal('show-modal-detail-user')),
            ActionButtons::custom('restore', 'restore', trans('core/base::general.restore'), false, 'users.edit')
                ?->attributes($modal('show-modal-restore-user')),
            ActionButtons::custom('force-delete', 'trash-x', trans('core/base::general.permanent_delete'), true, 'users.delete')
                ?->attributes($modal('show-modal-force-delete-user')),
        );
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
