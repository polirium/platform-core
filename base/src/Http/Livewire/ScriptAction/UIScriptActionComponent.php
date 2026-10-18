<?php

namespace Polirium\Core\Base\Http\Livewire\ScriptAction;

use Livewire\Component;

/**
 * Holds the page-wide "call-modal" browser listener.
 *
 * It used to listen to the "modal" event as well and send it back as
 * "call-modal": a second server round trip per modal, and a second
 * Bootstrap instance on top of the one app.js had already opened (two
 * backdrops, one left behind on close). app.js handles "modal" directly.
 */
class UIScriptActionComponent extends Component
{
    public function render()
    {
        return view('core/ui::script-action-ui.script');
    }
}
