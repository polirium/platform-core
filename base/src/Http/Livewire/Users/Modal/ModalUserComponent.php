<?php

namespace Polirium\Core\Base\Http\Livewire\Users\Modal;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Polirium\Core\Base\Http\Models\Branch\Branch;
use Polirium\Core\Base\Http\Models\Role;
use Polirium\Core\Base\Http\Models\User;
use Polirium\Core\Base\Traits\GetPermission;

class ModalUserComponent extends Component
{
    use WithFileUploads;
    use GetPermission;

    public array $list = [];
    public $role_ids = [];  // No type hint to allow normalization before type check
    public $branch_ids = [];  // No type hint to allow normalization before type check
    public $permission_ids = [];  // No type hint to allow normalization before type check
    public ?int $user_id = null;
    public bool $isEdit = false;
    public string $modalTitle = '';
    public $avatar_file = null;

    public $user = [
        'username' => '',
        'email' => '',
        'password' => '',
        'password_confirmation' => '',
        'phone' => '',
        'first_name' => '',
        'last_name' => '',
        'status' => 'active',
        'super_admin' => false,
        'avatar' => '',
        'email_verified_at' => null,
    ];

    protected function rules()
    {
        $rules = [
            'user.username' => "required|unique:users,username,{$this->user_id},id",
            'user.email' => "required|email|unique:users,email,{$this->user_id},id",
            'user.phone' => 'required',
            'user.first_name' => 'required',
            'user.last_name' => 'required',
            'user.status' => 'required|in:active,inactive',
            'role_ids' => 'array',
            'branch_ids' => 'array',
            'avatar_file' => 'nullable|image|max:2048', // 2MB max
        ];

        if (! $this->isEdit) {
            $rules['user.password'] = 'required|min:6|confirmed';
            $rules['user.password_confirmation'] = 'required|min:6';
        } else {
            $rules['user.password'] = 'nullable|min:6|confirmed';
            $rules['user.password_confirmation'] = 'nullable|min:6';
        }

        return $rules;
    }

    /**
     * Normalize array properties on every request
     * This runs before Livewire processes properties
     */
    public function boot(): void
    {
        $this->normalizeArrays();
    }

    public function mount(): void
    {
        $this->list['roles'] = Role::select(['id', 'name'])->pluck('name', 'id')->all();
        $this->list['branches'] = Branch::select(['id', 'name'])->pluck('name', 'id')->all();
        $this->list['statuses'] = [
            'active' => __('Hoạt động'),
            'inactive' => __('Không hoạt động'),
        ];

        // Load available permissions for direct assignment
        $permissions = $this->getAvailablePermissions();
        $this->list['permissions'] = collect($permissions)->mapWithKeys(function ($perm) {
            return [$perm['flag'] => trans($perm['name'])];
        })->all();
        $this->list['permission_tree'] = $this->getPermissionTree($permissions);
        $this->list['permission_flags'] = $permissions;

        // Ensure arrays are initialized
        $this->normalizeArrays();
    }

    /**
     * Normalize all array properties to ensure they are arrays
     * Called on every request to handle string values from frontend
     */
    protected function normalizeArrays(): void
    {
        // Normalize role_ids
        if (!is_array($this->role_ids)) {
            if (is_string($this->role_ids) && !empty($this->role_ids)) {
                $this->role_ids = array_map('intval', array_filter(explode(',', $this->role_ids)));
            } else {
                $this->role_ids = [];
            }
        } else {
            $this->role_ids = array_values(array_filter(array_map('intval', $this->role_ids)));
        }

        // Normalize branch_ids
        if (!is_array($this->branch_ids)) {
            if (is_string($this->branch_ids) && !empty($this->branch_ids)) {
                $this->branch_ids = array_map('intval', array_filter(explode(',', $this->branch_ids)));
            } else {
                $this->branch_ids = [];
            }
        } else {
            $this->branch_ids = array_values(array_filter(array_map('intval', $this->branch_ids)));
        }

        // Normalize permission_ids
        if (!is_array($this->permission_ids)) {
            if (is_string($this->permission_ids) && !empty($this->permission_ids)) {
                $this->permission_ids = array_filter(explode(',', $this->permission_ids));
            } else {
                $this->permission_ids = [];
            }
        } else {
            $this->permission_ids = array_values(array_filter($this->permission_ids));
        }
    }

    public function render()
    {
        // Get permissions from selected roles
        $rolePermissions = [];
        if (!empty($this->role_ids)) {
            $roles = Role::whereIn('id', $this->role_ids)->with('permissions')->get();
            foreach ($roles as $role) {
                foreach ($role->permissions as $permission) {
                    $rolePermissions[] = $permission->name;
                }
            }
            $rolePermissions = array_unique($rolePermissions);
        }

        return view('core/base::users.modal.modal-user', [
            'rolePermissions' => $rolePermissions,
        ]);
    }

    public function updatedUser($value, $key)
    {
        // This ensures Livewire properly tracks user data changes
        // Never log sensitive information like passwords
        if (in_array($key, ['password', 'password_confirmation'])) {
            \Log::info('User data updated:', ['key' => $key, 'value' => '***REDACTED***']);
        } else {
            \Log::info('User data updated:', ['key' => $key, 'value' => $value]);
        }
    }

    /**
     * Normalize role_ids to always be an array
     * Component may send JSON string, comma-separated string, or array from frontend
     */
    public function updatedRoleIds($value)
    {
        $this->role_ids = $this->normalizeArrayValue($value, true);
    }

    /**
     * Normalize branch_ids to always be an array
     * Component may send JSON string, comma-separated string, or array from frontend
     */
    public function updatedBranchIds($value)
    {
        $this->branch_ids = $this->normalizeArrayValue($value, true);
    }

    /**
     * Normalize permission_ids to always be an array
     * Component may send JSON string, comma-separated string, or array from frontend
     */
    public function updatedPermissionIds($value)
    {
        $this->permission_ids = $this->normalizeArrayValue($value, false);
    }

    /**
     * Normalize a value to array
     * @param mixed $value The value to normalize
     * @param bool $convertToInt Whether to convert values to integers
     * @return array
     */
    protected function normalizeArrayValue($value, bool $convertToInt = false): array
    {
        if (is_array($value)) {
            $result = array_values(array_filter($value));
            return $convertToInt ? array_map('intval', $result) : $result;
        }
        
        if (is_string($value)) {
            // Try to parse as JSON first (from updated component)
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $result = array_values(array_filter($decoded));
                return $convertToInt ? array_map('intval', $result) : $result;
            }
            
            // Fallback to comma-separated string
            if (!empty($value)) {
                $result = array_filter(explode(',', $value));
                return $convertToInt ? array_map('intval', $result) : $result;
            }
        }
        
        return [];
    }

    #[On('show-modal-create-user')]
    public function showCreateModal()
    {
        $this->authorize('users.create');

        $this->reset(['user', 'role_ids', 'branch_ids', 'avatar_file']);
        $this->isEdit = false;
        $this->user_id = null;
        $this->modalTitle = __('Create User');
        $this->dispatch('poli.modal', ['modal-user', 'show']);
    }

    #[On('show-modal-edit-user')]
    public function showEditModal($id = null)
    {
        $this->authorize('users.edit');

        // Handle both array and direct parameter formats
        if (is_array($id) && isset($id['id'])) {
            $userId = $id['id'];
        } else {
            $userId = $id;
        }

        // Clear any existing data first
        $this->reset(['user', 'role_ids', 'branch_ids', 'avatar_file']);

        // Set edit mode
        $this->isEdit = true;
        $this->user_id = $userId;
        $this->modalTitle = __('Edit User');

        // Load user data
        $user = User::with(['roles', 'branches'])->find($userId);
        if ($user) {
            // Set user data step by step to ensure proper binding
            $this->user['username'] = $user->username ?? '';
            $this->user['email'] = $user->email ?? '';
            $this->user['phone'] = $user->phone ?? '';
            $this->user['first_name'] = $user->first_name ?? '';
            $this->user['last_name'] = $user->last_name ?? '';
            $this->user['status'] = $user->status ?? 'active';
            $this->user['super_admin'] = (bool) $user->super_admin;
            $this->user['avatar'] = $user->avatar_path ?? '';  // Use raw path, not accessor
            $this->user['email_verified_at'] = $user->email_verified_at;
            $this->user['password'] = '';
            $this->user['password_confirmation'] = '';

            // Set roles and branches
            $this->role_ids = $user->roles->pluck('id')->toArray();
            $this->branch_ids = $user->branches->pluck('id')->toArray();

            // Load direct permissions (not from roles)
            $this->permission_ids = $user->getDirectPermissions()->pluck('name')->toArray();

            // Debug log - check if data is actually set
            \Log::info('Edit User Data Set:', [
                'user_id' => $userId,
                'user_array' => $this->user,
                'username' => $this->user['username'] ?? 'NOT SET',
                'email' => $this->user['email'] ?? 'NOT SET',
                'first_name' => $this->user['first_name'] ?? 'NOT SET',
                'last_name' => $this->user['last_name'] ?? 'NOT SET',
                'role_ids' => $this->role_ids,
                'branch_ids' => $this->branch_ids,
            ]);

            // Force Livewire to recognize the changes
            $this->dispatch('user-data-loaded');
        } else {
            \Log::error('User not found for edit:', ['user_id' => $userId]);
        }

        $this->dispatch('poli.modal', ['modal-user', 'show']);
    }

    public function save()
    {
        $this->validate();

        $userData = [
            'username' => $this->user['username'],
            'email' => $this->user['email'],
            'phone' => $this->user['phone'],
            'first_name' => $this->user['first_name'],
            'last_name' => $this->user['last_name'],
            'status' => $this->user['status'],
            'super_admin' => $this->user['super_admin'] ?? false,
        ];

        // Handle avatar upload
        if ($this->avatar_file) {
            $avatarPath = $this->avatar_file->store('avatars', 'public');
            $userData['avatar'] = $avatarPath;
        }

        if ($this->isEdit) {
            $user = User::find($this->user_id);
            if (! empty($this->user['password'])) {
                $userData['password'] = $this->user['password'];
            }
            $user->update($userData);
        } else {
            $userData['password'] = $this->user['password'];
            $user = User::create($userData);
        }

        $user->refresh();

        // Ensure arrays are properly formatted before syncing
        $roleIds = is_array($this->role_ids) ? array_filter(array_map('intval', $this->role_ids)) : [];
        $branchIds = is_array($this->branch_ids) ? array_filter(array_map('intval', $this->branch_ids)) : [];
        $permissionIds = is_array($this->permission_ids) ? array_filter($this->permission_ids) : [];

        // Sync roles
        if (!empty($roleIds)) {
            $roles = Role::whereIn('id', $roleIds)->pluck('name')->all();
            $user->syncRoles($roles);
        } else {
            $user->syncRoles([]);
        }

        // Sync branches
        if (!empty($branchIds)) {
            $user->branches()->sync($branchIds);
        } else {
            $user->branches()->detach();
        }

        // Sync direct permissions (additional to role permissions)
        if (!empty($permissionIds)) {
            $user->syncPermissions($permissionIds);
        } else {
            $user->syncPermissions([]);
        }

        $this->dispatch('pg:eventRefresh-usersTable');
        $this->dispatch('poli.modal', ['modal-user', 'hide']);
        $this->resetForm();

        session()->flash('message', $this->isEdit ? 'User updated successfully!' : 'User created successfully!');
    }

    private function resetForm()
    {
        $this->reset('user', 'role_ids', 'branch_ids', 'permission_ids', 'user_id', 'avatar_file', 'isEdit');
        $this->user = [
            'username' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
            'phone' => '',
            'first_name' => '',
            'last_name' => '',
            'status' => 'active',
            'super_admin' => false,
        ];
        $this->role_ids = [];
        $this->branch_ids = [];
        $this->permission_ids = [];
    }
}
