<?php

namespace Polirium\Core\UI\Http\Livewire;

use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class InlineTextComponent extends Component
{
    public string $type = "text";

    public string $col;

    public ?string $text = null;

    public mixed $value;

    public Model|string $model;

    public int $inline_id;

    public bool $open_inline = false;

    public array|string $dispatches = [];

    public function render()
    {
        return view('core/ui::inline.text');
    }

    public function toggleInline() : void
    {
        $this->open_inline = ! $this->open_inline;

        if ($this->open_inline) {
            $this->value = $this->model::select(['id', $this->col])
            ->findByUuidOrId($this->inline_id)?->{$this->col};
        } else {
            $this->value = null;
        }
    }

    public function save() : void
    {
        $data = $this->model::findByUuidOrId($this->inline_id)?->update([
            $this->col => $this->value
        ]);

        $this->toggleInline();

        if (count((array)$this->dispatches) > 0) {
            foreach ((array)$this->dispatches as $key => $value) {
                $this->dispatch($value, data: $data);
            }
        }

        // $this->js('$wire.$parent.$refresh()');
        $this->js('window.location.reload()');
    }
}
