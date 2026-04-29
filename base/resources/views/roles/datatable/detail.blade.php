@php
    use Polirium\Core\Base\Helpers\BaseHelper;
    use Polirium\Core\Base\Http\Models\Role;
    use Illuminate\Support\Arr;

    // Always fetch fresh role data from database to ensure we have latest permissions
    $freshRole = Role::with('permissions')->find($row->id);
    if (!$freshRole) {
        $freshRole = $row; // Fallback to cached row if not found
    }

    // Load permissions directly if not passed
    $flags = $permissionFlags ?? [];
    $tree = $permissionTree ?? [];

    if (empty($flags) || empty($tree)) {
        // Load permissions config
        $flags = [];
        $configuration = config(strtolower('core-permissions'));
        if (!empty($configuration)) {
            foreach ($configuration as $config) {
                $flags[$config['flag']] = $config;
            }
        }

        $types = ['core', 'packages', 'modules'];
        foreach ($types as $type) {
            foreach (BaseHelper::scanFolder(platform_path($type)) as $module) {
                $key = strtolower($type . '.' . $module . '.permissions');
                $config = config($key);

                if (empty($config)) {
                    $configFile = platform_path($type . '/' . $module . '/config/permissions.php');
                    if (file_exists($configFile)) {
                        $config = require $configFile;
                    }
                }

                if (!empty($config)) {
                    foreach ($config as $c) {
                        $flags[$c['flag']] = $c;
                    }
                }
            }
        }

        // Build tree
        $sortedFlag = $flags;
        sort($sortedFlag);
        $tree['root'] = [];
        foreach ($flags as $flagDetails) {
            if (Arr::get($flagDetails, 'parent_flag', 'root') == 'root') {
                $tree['root'][] = $flagDetails['flag'];
            }
        }
        foreach (array_keys($flags) as $key) {
            $childrenReturned = [];
            foreach ($flags as $fd) {
                if (Arr::get($fd, 'parent_flag', 'root') == $key) {
                    $childrenReturned[] = $fd['flag'];
                }
            }
            if (count($childrenReturned) > 0) {
                $tree[$key] = $childrenReturned;
            }
        }
    }

    // Use fresh role permissions from database
    $rolePermissions = $freshRole->permissions->pluck('name')->toArray();
    $rootGroups = $tree['root'] ?? [];

    // Build grouped permissions for this role
    $groupedPermissions = [];
    foreach ($rootGroups as $groupKey) {
        $groupName = trans($flags[$groupKey]['name'] ?? $groupKey);
        $children = $tree[$groupKey] ?? [];
        $hasPermissions = [];

        foreach ($children as $childKey) {
            if (in_array($childKey, $rolePermissions)) {
                $hasPermissions[] = [
                    'key' => $childKey,
                    'name' => trans($flags[$childKey]['name'] ?? $childKey),
                ];
            }
        }

        if (count($hasPermissions) > 0) {
            $groupedPermissions[] = [
                'key' => $groupKey,
                'name' => $groupName,
                'permissions' => $hasPermissions,
                'count' => count($hasPermissions),
                'total' => count($children),
            ];
        }
    }
@endphp

<div class="role-detail-container">
    {{-- Header --}}
    <div class="role-detail-header">
        <div class="role-detail-title">
            <div class="role-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <circle cx="12" cy="12" r="4"/>
                    <line x1="4.93" y1="4.93" x2="9.17" y2="9.17"/>
                    <line x1="14.83" y1="14.83" x2="19.07" y2="19.07"/>
                    <line x1="14.83" y1="9.17" x2="19.07" y2="4.93"/>
                    <line x1="4.93" y1="14.83" x2="9.17" y2="19.07"/>
                </svg>
            </div>
            <div>
                <h4 class="mb-0 fw-semibold">{{ $freshRole->name }}</h4>
                <small class="text-muted">
                    {{ count($rolePermissions) }} {{ trans('core/base::general.permissions_assigned') }}
                </small>
            </div>
        </div>

        <div class="role-detail-actions">
            @can('roles.edit')
                <x-ui::button color="primary" size="sm" icon="edit"
                        wire:click="$dispatch('show-modal-create-role', { id: {{ $row->id }} })">
                    {{ trans('core/base::general.edit') }}
                </x-ui::button>
            @endcan
            @can('roles.delete')
                <x-ui::button color="danger" size="sm" icon="trash"
                        wire:click="$dispatch('show-modal-delete-role', { id: {{ $row->id }} })"
                        wire:confirm="{{ trans('core/base::general.delete_role_confirm') }}">
                    {{ trans('core/base::general.delete') }}
                </x-ui::button>
            @endcan
        </div>
    </div>

    {{-- Permission Tree --}}
    @if(count($groupedPermissions) > 0)
        <div class="permission-tree-container" x-data="{ allExpanded: false }">
            <div class="permission-tree-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <span>{{ trans('core/base::general.permissions_list') }}</span>
                <button type="button"
                        class="permission-tree-expand-all"
                        @click="allExpanded = !allExpanded; $dispatch('toggle-all-groups', { expanded: allExpanded })">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 3 21 3 21 9"/>
                        <polyline points="9 21 3 21 3 15"/>
                        <line x1="21" y1="3" x2="14" y2="10"/>
                        <line x1="3" y1="21" x2="10" y2="14"/>
                    </svg>
                    <span x-show="!allExpanded">{{ trans('core/base::general.expand_all') }}</span>
                    <span x-show="allExpanded" x-cloak>{{ trans('core/base::general.collapse_all') }}</span>
                </button>
            </div>

            <div class="permission-tree-grid">
                @foreach($groupedPermissions as $index => $group)
                    <div class="permission-group"
                         data-group="{{ $group['key'] }}"
                         x-data="{ expanded: false }"
                         @toggle-all-groups.window="expanded = $event.detail.expanded"
                         :class="{ 'expanded': expanded }">
                        <button type="button" class="permission-group-toggle" @click="expanded = !expanded">
                            <svg class="toggle-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                            <span class="permission-group-name">{{ $group['name'] }}</span>
                            <span class="permission-group-count">{{ $group['count'] }}/{{ $group['total'] }}</span>
                        </button>

                        <div class="permission-group-items" x-show="expanded" x-collapse x-cloak>
                            @foreach($group['permissions'] as $permission)
                                <div class="permission-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                    <span>{{ $permission['name'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="permission-tree-empty">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                <line x1="12" y1="15" x2="12" y2="19"/>
                <line x1="12" y1="23" x2="12.01" y2="23"/>
            </svg>
            <p>{{ trans('core/base::general.no_permissions_assigned') }}</p>
        </div>
    @endif
</div>
