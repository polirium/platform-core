<?php

namespace Polirium\Core\Base\Http\Livewire\Users\Modal;

use Livewire\Attributes\On;
use Livewire\Component;
use Polirium\Core\Base\Http\Models\User;

class ModalDeleteUserComponent extends Component
{
    public $id = 1;

    protected function rules()
    {
        return [

        ];
    }

    public function render()
    {
        $user = User::find($this->id);

        return view('core/base::users.modal.modal-delete', compact('user'));
    }

    #[On('show-modal-delete-user')]
    public function showModal($id)
    {
        $this->authorize('users.delete');

        $this->id = $id;
        $this->dispatch('poli.modal', ['modal-delete-user', 'show']);
    }

    public function save()
    {
        $this->authorize('users.delete');

        $user = User::find($this->id);

        if ($user->super_admin) {
            $this->dispatch('poli.modal', ['modal-delete-user', 'hide']);

            return;
        }
        $user->delete();

        $this->dispatch('pg:eventRefresh-usersTable');
        $this->dispatch('poli.modal', ['modal-delete-user', 'hide']);
    }

}
