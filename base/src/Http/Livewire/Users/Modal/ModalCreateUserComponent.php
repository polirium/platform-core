<?php

namespace Polirium\Core\Base\Http\Livewire\Users\Modal;

use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;
use Livewire\Component;
use Polirium\Core\Base\Http\Models\User;

class ModalCreateUserComponent extends Component
{
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
        return [
            'user.username' => 'required|unique:users,username',
            'user.email' => 'required|email|unique:users,email',
            'user.password' => 'required|min:6',
            'user.password_confirmation' => 'required|min:6',
            'user.phone' => 'required',
            'user.first_name' => 'required',
            'user.last_name' => 'required',
        ];
    }

    public function render()
    {
        return view('core/base::users.modal.modal-create');
    }

    #[On('show-modal-create-user')]
    public function showModal()
    {
        $this->dispatch('poli.modal', ['modal-create-user']);
    }

    public function save()
    {
        $this->validate();
        User::create([
            'username' => $this->user['username'],
            'email' => $this->user['email'],
            'password' => Hash::make($this->user['password']),
            'phone' => $this->user['phone'],
            'first_name' => $this->user['first_name'],
            'last_name' => $this->user['last_name'],
            'super_admin' => false,
        ]);

        $this->dispatch('pg:eventRefresh-usersTable');
        $this->dispatch('poli.modal', ['modal-create-user', 'hide']);
        $this->reset('user');
    }

}
