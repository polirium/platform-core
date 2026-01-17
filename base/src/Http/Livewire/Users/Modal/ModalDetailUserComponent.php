<?php

namespace Polirium\Core\Base\Http\Livewire\Users\Modal;

use Livewire\Attributes\On;
use Livewire\Component;
use Polirium\Core\Base\Http\Models\User;

class ModalDetailUserComponent extends Component
{
    public ?int $user_id = null;
    public array $user = [];

    public $roles = null;
    public $branches = null;

    public function render()
    {
        return view('core/base::users.modal.modal-detail');
    }

    #[On('show-modal-detail-user')]
    public function showDetailModal($id = null)
    {
        $this->authorize('users.view');

        // Handle both array and direct parameter formats
        if (is_array($id) && isset($id['id'])) {
            $userId = $id['id'];
        } else {
            $userId = $id;
        }

        // Clear any existing data first
        $this->reset(['user', 'roles', 'branches']);

        // Load user data
        $user = User::with(['roles', 'branches'])->find($userId);
        if ($user) {
            $this->user = [
                'id' => $user->id,
                'username' => $user->username ?? '',
                'email' => $user->email ?? '',
                'phone' => $user->phone ?? '',
                'first_name' => $user->first_name ?? '',
                'last_name' => $user->last_name ?? '',
                'status' => $user->status ?? 'active',
                'super_admin' => (bool) $user->super_admin,
                'avatar' => $user->avatar_path ?? '',
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at?->toDateTimeString(),
                'updated_at' => $user->updated_at?->toDateTimeString(),
            ];

            $this->roles = $user->roles;
            $this->branches = $user->branches;

            \Log::info('Detail User Data Loaded:', [
                'user_id' => $userId,
                'username' => $this->user['username'],
                'email' => $this->user['email'],
            ]);
        } else {
            \Log::error('User not found for detail:', ['user_id' => $userId]);
        }

        $this->dispatch('poli.modal', ['modal-detail-user', 'show']);
    }

    #[On('close-modal-detail-user')]
    public function closeDetailModal()
    {
        $this->dispatch('poli.modal', ['modal-detail-user', 'hide']);
    }
}
