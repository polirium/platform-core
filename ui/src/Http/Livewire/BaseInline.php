<?php

namespace Polirium\Core\UI\Http\Livewire;

use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;
use Livewire\Component;

class BaseInline extends Component
{
    public string $type = "text";

    public string $col;

    public ?string $obj = "";

    public mixed $value;

    public Model|string $model;

    public int $inline_id;

    public bool $open_inline = false;

    public array|string $validate = [];

    public array|string $dispatches = [];

    public string $text_display = "";

    public function mount() : void
    {
        $this->loadData();
    }

    #[Computed]
    public function entity()
    {
        return $this->model::findByUuidOrId($this->inline_id);
    }

    private function loadData()
    {
        $this->reset('text_display');
        $result = "";

        if (! empty($this->obj)) {
            $first = 0;

            $parts = explode('.', $this->obj);
            foreach ($parts as $part) {
                if (isset($this->entity->{$part})) {
                    if ($first == 0) {
                        $result = $this->entity->{$part};
                    } else {
                        $result = $result->{$part};
                    }
                } else {
                    $result = null;
                    break;
                }
                $first++;
            }
        } else {
            $result = $this->entity->{$this->col};
        }

        $this->text_display = $result ?? '';
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

        $this->loadData();

        if (count((array)$this->dispatches) > 0) {
            foreach ((array)$this->dispatches as $key => $value) {
                $this->dispatch($value, data: $data);
            }
        }
    }
}
