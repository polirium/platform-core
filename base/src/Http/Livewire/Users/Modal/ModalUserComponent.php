<?php

namespace Polirium\Core\Base\Http\Livewire\Users\Modal;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Polirium\Core\Base\Http\Models\Branch\Branch;
use Polirium\Core\Base\Http\Models\Role;
use Polirium\Core\Base\Http\Models\User;

class ModalUserComponent extends Component
{
    use WithFileUploads;

    public array $list = [];
    public array $role_ids = [];
    public array $branch_ids = [];
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

    public function mount(): void
    {
        $this->list['roles'] = Role::select(['id', 'name'])->pluck('name', 'id')->all();
        $this->list['branches'] = Branch::select(['id', 'name'])->pluck('name', 'id')->all();
        $this->list['statuses'] = [
            'active' => 'Active',
            'inactive' => 'Inactive',
        ];
    }

    public function render()
    {
        // Debug current data state
        if ($this->isEdit) {
            \Log::info('Render Edit Modal:', [
                'isEdit' => $this->isEdit,
                'user_id' => $this->user_id,
                'user_data' => $this->user,
                'role_ids' => $this->role_ids,
                'branch_ids' => $this->branch_ids,
            ]);
        }

        return view('core/base::users.modal.modal-user');
    }

    public function updatedUser($value, $key)
    {
        // This ensures Livewire properly tracks user data changes
        \Log::info('User data updated:', ['key' => $key, 'value' => $value]);
    }

    #[On('show-modal-create-user')]
    public function showCreateModal()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->modalTitle = __('Create User');
        $this->dispatch('poli.modal', ['modal-user', 'show']);
    }

    #[On('show-modal-edit-user')]
    public function showEditModal($id = null)
    {
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
            $this->user['avatar'] = $user->avatar ?? '';
            $this->user['email_verified_at'] = $user->email_verified_at;
            $this->user['password'] = '';
            $this->user['password_confirmation'] = '';

            // Set roles and branches
            $this->role_ids = $user->roles->pluck('id')->toArray();
            $this->branch_ids = $user->branches->pluck('id')->toArray();

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

        // Sync roles
        $roles = Role::whereIn('id', $this->role_ids)->pluck('name')->all();
        $user->syncRoles($roles);

        // Sync branches
        if (count($this->branch_ids) > 0) {
            $user->branches()->sync($this->branch_ids);
        } else {
            $user->branches()->detach();
        }

        $this->dispatch('pg:eventRefresh-usersTable');
        $this->dispatch('poli.modal', ['modal-user', 'hide']);
        $this->resetForm();

        session()->flash('message', $this->isEdit ? 'User updated successfully!' : 'User created successfully!');
    }

    private function resetForm()
    {
        $this->reset('user', 'role_ids', 'branch_ids', 'user_id', 'avatar_file', 'isEdit');
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
    }
}
