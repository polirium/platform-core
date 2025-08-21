<?php

namespace Polirium\Core\Base\Http\Livewire\Users\Modal;

use Livewire\Attributes\On;
use Livewire\Component;
use Polirium\Core\Base\Http\Models\Role;
use Polirium\Core\Base\Http\Models\User;

class ModalCreateUserComponent extends Component
{
    public array $list = [];

    public array $role_ids = [];

    public ?int $user_id = null;

    public $user = [
        'username' => '',
        'email' => '',
        'password' => '',
        'password_confirmation' => '',
        'phone' => '',
        'first_name' => '',
        'last_name' => '',
    ];
    protected function rules()
    {
        if (! $this->user_id) {
            return [
                'user.username' => "required|unique:users,username,{$this->user_id},id",
                'user.email' => "required|email|unique:users,email,{$this->user_id},id",
                'user.password' => 'required|min:6|confirmed',
                'user.password_confirmation' => 'required|min:6',
                'user.phone' => 'required',
                'user.first_name' => 'required',
                'user.last_name' => 'required',
            ];
        }

        return [
            'user.username' => "required|unique:users,username,{$this->user_id},id",
            'user.email' => "required|email|unique:users,email,{$this->user_id},id",
            'user.phone' => 'required',
            'user.first_name' => 'required',
            'user.last_name' => 'required',
        ];
    }

    public function mount(): void
    {
        $this->list['roles'] = Role::select(['id', 'name'])->pluck('name', 'id')->all();
    }

    public function render()
    {
        return view('core/base::users.modal.modal-create');
    }

    #[On('show-modal-create-user')]
    public function showModal(int $id = null)
    {
        // $this->dispatch('modal', ['modal-create-user']);
        $this->reset('user', 'role_ids');
        $this->user_id = $id;
        if ($id) {
            $user = User::find($id);
            $this->user = $user?->toArray();
            $this->role_ids = $user?->roles->pluck('id')->toArray() ?: [];
        }
        $this->dispatch('poli.modal', ['modal-create-user', 'show']);
    }

    public function save()
    {
        $this->validate();

        if ($this->user_id) {
            $user = User::find($this->user_id);
            $user->update([
                'username' => $this->user['username'],
                'email' => $this->user['email'],
                'phone' => $this->user['phone'],
                'first_name' => $this->user['first_name'],
                'last_name' => $this->user['last_name'],
            ]);
        } else {
            $user = new User();
            $user->username = $this->user['username'];
            $user->email = $this->user['email'];
            $user->password = $this->user['password'];
            $user->phone = $this->user['phone'];
            $user->first_name = $this->user['first_name'];
            $user->last_name = $this->user['last_name'];
            $user->super_admin = false;
            $user->save();
        }

        $user->refresh();

        $roles = Role::whereIn('id', $this->role_ids)->pluck('name')->all();
        if (count($roles) > 0) {
            $user->syncRoles($roles);
        } else {
            $user->syncRoles([]);
        }

        $this->dispatch('pg:eventRefresh-usersTable');
        $this->dispatch('poli.modal', ['modal-create-user', 'hide']);
        $this->reset('user');
    }
}
