<?php

namespace Polirium\Core\Base\Http\Livewire\Users\Modal;

use Livewire\Attributes\On;
use Livewire\Component;
use Polirium\Core\Base\Http\Models\User;

class ModalRestoreUserComponent extends Component
{
    public $id = 1;

    protected function rules()
    {
        return [

        ];
    }

    public function render()
    {
        $user = User::onlyTrashed()->find($this->id);

        return view('core/base::users.modal.modal-restore', compact('user'));
    }

    #[On('show-modal-restore-user')]
    public function showModal($id)
    {
        $this->authorize('users.edit');

        $this->id = $id;
        $this->dispatch('poli.modal', ['modal-restore-user', 'show']);
    }

    public function save()
    {
        $this->authorize('users.edit');

        $user = User::onlyTrashed()->find($this->id);

        if ($user) {
            $user->restore();
        }

        $this->dispatch('pg:eventRefresh-usersTable');
        $this->dispatch('poli.modal', ['modal-restore-user', 'hide']);
    }
}
