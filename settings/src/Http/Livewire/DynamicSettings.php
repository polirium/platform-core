<?php

namespace Polirium\Core\Settings\Http\Livewire;

use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;
use Polirium\Core\Settings\Facades\SettingRegistry;
use Polirium\Core\Settings\Facades\Settings;
use Polirium\Core\UI\Facades\Assets;

/**
 * Dynamic Settings Component
 *
 * Generic Livewire component to render and handle settings for any group.
 * Modules can register settings via SettingRegistry without creating custom components.
 */
class DynamicSettings extends Component
{
    use WithFileUploads;

    /**
     * Current group key
     */
    public string $groupKey;

    /**
     * Group configuration
     */
    public array $group;

    /**
     * Settings values (key => value)
     */
    public array $settings = [];

    /**
     * Validation errors
     */
    protected $validationErrors = [];

    /**
     * Success message
     */
    public string $successMessage = '';

    /**
     * Loading state
     */
    public bool $loading = false;

    /**
     * Mount component
     */
    public function mount(string $groupKey, array $group): void
    {
        Assets::loadCss('settings');
        $this->groupKey = $groupKey;
        $this->group = $group;
        $this->loadSettings();
    }

    /**
     * Load settings values from database
     */
    protected function loadSettings(): void
    {
        $settingDefs = SettingRegistry::getGroupSettings($this->groupKey);

        foreach ($settingDefs as $key => $config) {
            $this->settings[$key] = Settings::get($key, $config['default'] ?? null);
        }
    }

    /**
     * Save all settings
     */
    public function save(): void
    {
        $this->authorize('settings.' . $this->groupKey);

        $this->loading = true;

        $settingDefs = SettingRegistry::getGroupSettings($this->groupKey);
        $rules = $this->buildValidationRules($settingDefs);

        // Validate using Laravel's Validator
        $validator = Validator::make($this->settings, $rules);

        if ($validator->fails()) {
            $this->loading = false;
            $this->setErrorBag($validator->errors());

            return;
        }

        $validated = $validator->validated();

        // Save to database
        foreach ($validated as $key => $value) {
            Settings::set($key, $this->processValue($value, $settingDefs[$key] ?? []));
        }

        // Also save settings that don't have validation rules
        foreach ($this->settings as $key => $value) {
            if (! isset($validated[$key]) && isset($settingDefs[$key])) {
                Settings::set($key, $this->processValue($value, $settingDefs[$key]));
            }
        }

        $this->loading = false;
        $this->successMessage = __('core/base::general.save_settings_success');

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $this->successMessage,
        ]);
    }

    /**
     * Build validation rules from setting definitions
     */
    protected function buildValidationRules(array $settingDefs): array
    {
        $rules = [];

        foreach ($settingDefs as $key => $config) {
            $rule = null;

            if (isset($config['validation']) && is_array($config['validation'])) {
                $rule = $config['validation'];
            } elseif ($config['required'] ?? false) {
                $rule = ['required'];
            }

            if ($rule) {
                // If type is file, and the current setting value is an existing string URL (not empty, not an uploaded object)
                if (($config['type'] ?? '') === 'file') {
                    $currentVal = $this->settings[$key] ?? null;

                    if (is_string($currentVal) && ! empty($currentVal) && ! str_starts_with($currentVal, 'livewire-tmp:')) {
                        // This is an existing image URL. Strip constraints that expect an UploadedFile
                        $rule = array_filter($rule, function ($r) {
                            if (is_string($r)) {
                                $rName = explode(':', $r)[0];

                                return ! in_array($rName, ['image', 'file', 'mimes', 'max', 'min', 'size']);
                            }

                            return true;
                        });
                        $rule[] = 'string';
                    }
                }

                $rules[$key] = array_values($rule);
            }
        }

        return $rules;
    }

    /**
     * Process value before saving (handle file uploads, etc.)
     */
    protected function processValue($value, array $config)
    {
        $type = $config['type'] ?? 'text';

        // Handle file uploads
        if ($type === 'file') {
            if ($value instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                $value = $this->storeUploadedFile($value, $config);
            } elseif (is_string($value) && str_starts_with($value, 'livewire-tmp:')) {
                $value = $this->storeUploadedFile($value, $config);
            }
        }

        // Handle checkbox
        if ($type === 'checkbox') {
            return (bool) $value;
        }

        return $value;
    }

    /**
     * Store uploaded file
     */
    protected function storeUploadedFile($fileOrPath, array $config): string
    {
        if ($fileOrPath instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $extension = $fileOrPath->getClientOriginalExtension();
            $filename = $this->groupKey . '_' . uniqid() . '.' . $extension;
            $destination = public_path('uploads/settings/' . $filename);

            $directory = dirname($destination);
            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            rename($fileOrPath->getRealPath(), $destination);

            return 'uploads/settings/' . $filename;
        }

        $tempPath = $fileOrPath;
        // Get file extension
        $extension = pathinfo($tempPath, PATHINFO_EXTENSION);

        // Generate filename
        $filename = $this->groupKey . '_' . uniqid() . '.' . $extension;

        // Store in public/uploads/settings
        $destination = public_path('uploads/settings/' . $filename);

        // Ensure directory exists
        $directory = dirname($destination);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Move file
        rename(storage_path('app/' . str_replace('livewire-tmp:', '', $tempPath)), $destination);

        return 'uploads/settings/' . $filename;
    }

    /**
     * Get setting value for display
     */
    public function getSettingValue(string $key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Get all setting definitions for current group
     */
    public function getSettingDefsProperty(): array
    {
        return SettingRegistry::getGroupSettings($this->groupKey);
    }

    /**
     * Render component
     */
    public function render(): \Illuminate\View\View
    {
        return view('core/settings::livewire.dynamic-settings', [
            'settingDefs' => $this->settingDefs,
        ]);
    }
}
