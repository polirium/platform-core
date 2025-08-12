<?php

namespace Polirium\Core\Base\Http\Livewire\Users\Modal;

use Livewire\Attributes\On;
use Livewire\Component;
use Polirium\Core\Base\Http\Models\User;

class ModalEditUserComponent extends Component
{
    public $user = [
        'id' => null,
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
        return [
            'user.username' => 'required|unique:users,username,' . $this->user['id'],
            'user.email' => 'required|email|unique:users,email,' . $this->user['id'],
            'user.phone' => 'required',
            'user.first_name' => 'required',
            'user.last_name' => 'required',
            'user.password' => 'nullable|min:6|confirmed',
            'user.password_confirmation' => 'nullable|min:6',
        ];
    }

    public function render()
    {
        return view('core/base::users.modal.modal-edit');
    }

    #[On('show-modal-edit-user')]
    public function showModal($userId)
    {
        $user = User::findOrFail($userId);

        $this->user = [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'password' => '',
            'password_confirmation' => '',
        ];

        $this->dispatch('poli.modal', ['modal-edit-user']);
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
        ];

        if (! empty($this->user['password'])) {
            $userData['password'] = $this->user['password'];
        }

        User::find($this->user['id'])->update($userData);

        $this->dispatch('pg:eventRefresh-usersTable');
        $this->dispatch('poli.modal', ['modal-edit-user', 'hide']);
        $this->reset('user');
    }
}
