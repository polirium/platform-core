<?php

namespace Polirium\Core\Base\Http\Livewire\ScriptAction;

use Livewire\Attributes\On;
use Livewire\Component;

class UIScriptActionComponent extends Component
{
    public function render()
    {
        return view('core/ui::script-action-ui.script');
    }

    #[On('modal')]
    public function modal($id, $show = 'show')
    {
        $this->dispatch('call-modal', $id, $show);
    }
}
