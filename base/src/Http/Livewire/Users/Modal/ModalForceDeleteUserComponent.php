<?php

namespace Polirium\Core\Base\Http\Livewire\Users\Modal;

use Livewire\Attributes\On;
use Livewire\Component;
use Polirium\Core\Base\Http\Models\User;

class ModalForceDeleteUserComponent extends Component
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

        return view('core/base::users.modal.modal-force-delete', compact('user'));
    }

    #[On('show-modal-force-delete-user')]
    public function showModal($id)
    {
        $this->authorize('users.delete');

        $this->id = $id;
        $this->dispatch('poli.modal', ['modal-force-delete-user', 'show']);
    }

    public function save()
    {
        $this->authorize('users.delete');

        $user = User::onlyTrashed()->find($this->id);

        if ($user) {
            $user->forceDelete();
        }

        $this->dispatch('pg:eventRefresh-usersTable');
        $this->dispatch('poli.modal', ['modal-force-delete-user', 'hide']);
    }
}
