<?php

namespace Polirium\Core\Settings\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Polirium\Core\Settings\Facades\Settings;
use Polirium\Core\Settings\Facades\SettingRegistry;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GeneralSettings extends Component
{
    use WithFileUploads;

    public $title;
    public $logo;
    public $favicon;
    public $logoFile;
    public $faviconFile;

    protected $rules = [
        'title' => 'required|string|max:255',
        'logoFile' => 'nullable|image|max:2048',
        'faviconFile' => 'nullable|image|max:1024',
    ];

    protected $messages = [
        'title.required' => 'Site title is required.',
        'title.max' => 'Site title must not exceed 255 characters.',
        'logoFile.image' => 'Logo must be an image file.',
        'logoFile.max' => 'Logo file size must not exceed 2MB.',
        'faviconFile.image' => 'Favicon must be an image file.',
        'faviconFile.max' => 'Favicon file size must not exceed 1MB.',
    ];

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $settings = SettingRegistry::getGroupSettings('general');
        
        $this->title = Settings::get('title', $settings['title']['default'] ?? '');
        $this->logo = Settings::get('logo', $settings['logo']['default'] ?? '');
        $this->favicon = Settings::get('favicon', $settings['favicon']['default'] ?? '');
    }

    public function save()
    {
        $this->validate();

        try {
            // Save title
            Settings::set('title', $this->title);

            // Handle logo upload
            if ($this->logoFile) {
                $logoPath = $this->uploadFile($this->logoFile, 'logo');
                if ($logoPath) {
                    // Delete old logo if it exists and is not the default
                    $this->deleteOldFile($this->logo);
                    Settings::set('logo', $logoPath);
                    $this->logo = $logoPath;
                }
            }

            // Handle favicon upload
            if ($this->faviconFile) {
                $faviconPath = $this->uploadFile($this->faviconFile, 'favicon');
                if ($faviconPath) {
                    // Delete old favicon if it exists and is not the default
                    $this->deleteOldFile($this->favicon);
                    Settings::set('favicon', $faviconPath);
                    $this->favicon = $faviconPath;
                }
            }

            // Clear file inputs
            $this->logoFile = null;
            $this->faviconFile = null;

            session()->flash('success', 'Settings saved successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save settings: ' . $e->getMessage());
        }
    }

    protected function uploadFile($file, $type)
    {
        try {
            $filename = $type . '_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('settings', $filename, 'public');
            
            return asset('storage/' . $path);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to upload ' . $type . ': ' . $e->getMessage());
            return null;
        }
    }

    protected function deleteOldFile($filePath)
    {
        if (!$filePath || Str::startsWith($filePath, 'http')) {
            return; // Don't delete external URLs or empty paths
        }

        try {
            $relativePath = str_replace(asset('storage/'), '', $filePath);
            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }
        } catch (\Exception $e) {
            // Silently fail - not critical
        }
    }

    public function render()
    {
        return view('core/settings::livewire.general-settings');
    }
}
