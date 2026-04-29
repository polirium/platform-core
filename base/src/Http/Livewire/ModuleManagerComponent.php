<?php

namespace Polirium\Core\Base\Http\Livewire;

use Livewire\Component;
use Polirium\Core\Base\Http\Models\Module;
use Polirium\Core\Base\Service\ModuleManager;

class ModuleManagerComponent extends Component
{
    public $search = '';
    public $selected = [];
    public $viewMode = 'list';
    public $moduleFile; // For upload
    public $modules;

    protected $listeners = [
        'refresh' => '$refresh',
        'moduleStatusChanged' => 'loadModules',
        'open-upload-modal' => 'openUploadModal',
    ];

    public function mount()
    {
        $this->viewMode = session()->get('module_view_mode', 'list');
        $this->loadModules();
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
        session()->put('module_view_mode', $mode);
    }

    public function openUploadModal()
    {
        $this->dispatch('show-modal', id: 'upload-module-modal');
    }

    public function uploadModule()
    {
        $this->authorize('modules.manage');

        $this->validate([
            'moduleFile' => 'required|file|mimes:zip|max:51200', // 50MB
        ]);

        try {
            $zipPath = $this->moduleFile->getRealPath();
            $zip = new \ZipArchive();

            if ($zip->open($zipPath) === true) {
                // Extract to modules directory
                $extractPath = platform_path('modules');

                // Get the root folder name from the zip to check for existence/validity
                // Simple validation: usually the first entry is the folder

                $zip->extractTo($extractPath);
                $zip->close();

                // Trigger discovery to register the new module
                $this->discover();

                session()->flash('success', trans('core/base::general.module_installed_success'));
                $this->dispatch('hide-modal', id: 'upload-module-modal');
                $this->moduleFile = null; // Reset
            } else {
                session()->flash('error', trans('core/base::general.failed_open_zip'));
            }
        } catch (\Exception $e) {
            session()->flash('error', trans('core/base::general.error_uploading_module') . $e->getMessage());
        }
    }

    public function updatedSearch()
    {
        $this->loadModules();
    }

    public function loadModules()
    {
        $query = Module::orderBy('display_name');

        if (! empty($this->search)) {
            $search = strtolower($this->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(display_name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
            });
        }

        $this->modules = $query->get();
    }

    public function discover()
    {
        $this->authorize('modules.manage');

        $manager = app(ModuleManager::class);
        $discovered = $manager->discover();

        $this->loadModules();

        $count = count($discovered);
        session()->flash('success', __("Đã phát hiện {$count} module."));
    }

    public function toggleStatus($name)
    {
        $module = Module::where('name', $name)->first();
        if (! $module) {
            return;
        }

        if ($module->isActive()) {
            $this->disable($name);
        } else {
            $this->enable($name);
        }
    }

    public function bulkActivate()
    {
        $this->authorize('modules.manage');

        $count = 0;
        foreach ($this->selected as $name) {
            if ($this->enable($name)) {
                $count++;
            }
        }
        session()->flash('success', __("Đã kích hoạt {$count} module."));
        $this->selected = [];
        $this->loadModules();
    }

    public function bulkDeactivate()
    {
        $this->authorize('modules.manage');

        $count = 0;
        foreach ($this->selected as $name) {
            if ($this->disable($name)) {
                $count++;
            }
        }
        session()->flash('success', __("Đã vô hiệu hóa {$count} module."));
        $this->selected = [];
        $this->loadModules();
    }

    public function delete($name)
    {
        $this->authorize('modules.manage');

        $manager = app(ModuleManager::class);

        if ($manager->delete($name)) {
            session()->flash('success', __("Đã xóa module '{$name}' (được chuyển vào thùng rác)."));
        } else {
            session()->flash('error', __("Không thể xóa module '{$name}'."));
        }

        $this->loadModules();
    }

    public function download($name)
    {
        $this->authorize('modules.manage');

        $manager = app(ModuleManager::class);
        $path = $manager->download($name);

        if ($path && file_exists($path)) {
            return response()->download($path)->deleteFileAfterSend(true);
        }

        session()->flash('error', trans('core/base::general.cannot_download_module'));
    }

    public function install($name)
    {
        $this->authorize('modules.manage');

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
        $this->authorize('modules.manage');

        $manager = app(ModuleManager::class);

        if ($manager->enable($name)) {
            $this->loadModules();

            return true;
        }

        return false;
    }

    public function disable($name)
    {
        $this->authorize('modules.manage');

        $manager = app(ModuleManager::class);

        if ($manager->disable($name)) {
            $this->loadModules();

            return true;
        }

        return false;
    }

    public function uninstall($name, $rollback = true)
    {
        $this->authorize('modules.manage');

        $manager = app(ModuleManager::class);

        if ($manager->uninstall($name, $rollback)) {
            session()->flash('success', __("Đã gỡ cài đặt module '{$name}'."));
        } else {
            session()->flash('error', __("Không thể gỡ cài đặt module '{$name}'."));
        }

        $this->loadModules();
    }

    public function render()
    {
        return view('core/base::modules.module-manager');
    }
}
