<?php

namespace Polirium\Core\Base\Http\Livewire;

use Livewire\Component;
use Polirium\Core\Base\Http\Models\Module;
use Polirium\Core\Base\Service\ModuleManager;

class ModuleManagerComponent extends Component
{
    public $modules = [];
    public $selectedModule = null;
    public $showInfoModal = false;

    protected $listeners = [
        'refresh' => '$refresh',
    ];

    public function mount()
    {
        $this->loadModules();
    }

    public function loadModules()
    {
        $this->modules = Module::orderBy('display_name')->get();
    }

    public function discover()
    {
        $manager = app(ModuleManager::class);
        $discovered = $manager->discover();

        $this->loadModules();

        $count = count($discovered);
        session()->flash('success', __("Đã phát hiện {$count} module."));
    }

    public function install($name)
    {
        $manager = app(ModuleManager::class);

        if ($manager->install($name)) {
            session()->flash('success', __("Đã cài đặt module '{$name}' thành công."));
        } else {
            session()->flash('error', __("Không thể cài đặt module '{$name}'."));
        }

        $this->loadModules();
    }

    public function enable($name)
    {
        $manager = app(ModuleManager::class);

        if ($manager->enable($name)) {
            session()->flash('success', __("Đã kích hoạt module '{$name}'."));
        } else {
            session()->flash('error', __("Không thể kích hoạt module '{$name}'."));
        }

        $this->loadModules();
    }

    public function disable($name)
    {
        $manager = app(ModuleManager::class);

        if ($manager->disable($name)) {
            session()->flash('success', __("Đã vô hiệu hóa module '{$name}'."));
        } else {
            session()->flash('error', __("Không thể vô hiệu hóa module '{$name}'."));
        }

        $this->loadModules();
    }

    public function uninstall($name)
    {
        $manager = app(ModuleManager::class);

        if ($manager->uninstall($name, true)) {
            session()->flash('success', __("Đã gỡ cài đặt module '{$name}'."));
        } else {
            session()->flash('error', __("Không thể gỡ cài đặt module '{$name}'."));
        }

        $this->loadModules();
    }

    public function showInfo($id)
    {
        $this->selectedModule = Module::find($id);
        $this->showInfoModal = true;
        $this->dispatch('show-modal', id: 'module-info-modal');
    }

    public function render()
    {
        return view('core/base::modules.module-manager');
    }
}
