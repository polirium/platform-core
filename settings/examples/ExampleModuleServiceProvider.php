<?php

namespace ExampleModule\Providers;

use Polirium\Core\Settings\Facades\SettingRegistry;
use Polirium\Core\Support\Providers\PoliriumBaseServiceProvider;

/**
 * Example ServiceProvider showing how to register settings for a module
 */
class ExampleModuleServiceProvider extends PoliriumBaseServiceProvider
{
    public function boot()
    {
        $this->setNamespace('modules/example-module')
            ->loadConfigurations(['config'])
            ->loadViews()
            ->loadRoutes(['web']);

        $this->registerSettings();
    }

    protected function registerSettings()
    {
        // Register Email Settings Group
        SettingRegistry::group('email', [
                'title' => 'Email Settings',
                'icon' => 'mail',
                'description' => 'Configure email settings for your application',
            ])
            ->add('smtp_host', [
                'type' => 'text',
                'label' => 'SMTP Host',
                'description' => 'Your SMTP server hostname',
                'default' => 'smtp.gmail.com',
                'required' => true,
                'validation' => ['required', 'string', 'max:255'],
            ])
            ->add('smtp_port', [
                'type' => 'number',
                'label' => 'SMTP Port',
                'description' => 'SMTP server port (usually 587 or 465)',
                'default' => 587,
                'required' => true,
                'validation' => ['required', 'integer', 'min:1', 'max:65535'],
            ])
            ->add('smtp_encryption', [
                'type' => 'select',
                'label' => 'Encryption',
                'description' => 'Email encryption method',
                'options' => [
                    'tls' => 'TLS',
                    'ssl' => 'SSL',
                    'none' => 'None',
                ],
                'default' => 'tls',
                'validation' => ['required', 'in:tls,ssl,none'],
            ])
            ->add('from_email', [
                'type' => 'email',
                'label' => 'From Email',
                'description' => 'Default sender email address',
                'required' => true,
                'validation' => ['required', 'email'],
            ])
            ->add('email_signature', [
                'type' => 'textarea',
                'label' => 'Email Signature',
                'description' => 'Default email signature (HTML allowed)',
                'validation' => ['nullable', 'string'],
            ])
            ->add('email_logo', [
                'type' => 'file',
                'label' => 'Email Logo',
                'description' => 'Logo to include in email templates',
                'validation' => ['nullable', 'image', 'max:1024'],
                'attributes' => ['accept' => 'image/*'],
            ]);

        // Register Social Media Settings Group
        SettingRegistry::group('social', [
                'title' => 'Social Media',
                'icon' => 'brand-facebook',
                'description' => 'Configure social media integration',
            ])
            ->add('facebook_url', [
                'type' => 'url',
                'label' => 'Facebook URL',
                'description' => 'Your Facebook page URL',
                'validation' => ['nullable', 'url'],
            ])
            ->add('twitter_url', [
                'type' => 'url',
                'label' => 'Twitter URL',
                'description' => 'Your Twitter profile URL',
                'validation' => ['nullable', 'url'],
            ])
            ->add('linkedin_url', [
                'type' => 'url',
                'label' => 'LinkedIn URL',
                'description' => 'Your LinkedIn profile URL',
                'validation' => ['nullable', 'url'],
            ])
            ->add('enable_social_login', [
                'type' => 'checkbox',
                'label' => 'Enable Social Login',
                'description' => 'Allow users to login with social media accounts',
                'default' => false,
            ]);

        // Register Advanced Settings Group
        SettingRegistry::group('advanced', [
                'title' => 'Advanced Settings',
                'icon' => 'settings-2',
                'description' => 'Advanced configuration options',
            ])
            ->add('maintenance_mode', [
                'type' => 'checkbox',
                'label' => 'Maintenance Mode',
                'description' => 'Enable maintenance mode to prevent user access',
                'default' => false,
            ])
            ->add('debug_mode', [
                'type' => 'checkbox',
                'label' => 'Debug Mode',
                'description' => 'Enable debug mode for development',
                'default' => false,
            ])
            ->add('cache_duration', [
                'type' => 'number',
                'label' => 'Cache Duration (minutes)',
                'description' => 'How long to cache data',
                'default' => 60,
                'validation' => ['required', 'integer', 'min:1'],
            ])
            ->add('api_rate_limit', [
                'type' => 'number',
                'label' => 'API Rate Limit',
                'description' => 'Maximum API requests per minute',
                'default' => 100,
                'validation' => ['required', 'integer', 'min:1'],
            ])
            ->add('backup_frequency', [
                'type' => 'select',
                'label' => 'Backup Frequency',
                'description' => 'How often to create backups',
                'options' => [
                    'daily' => 'Daily',
                    'weekly' => 'Weekly',
                    'monthly' => 'Monthly',
                    'never' => 'Never',
                ],
                'default' => 'weekly',
                'validation' => ['required', 'in:daily,weekly,monthly,never'],
            ]);
    }
}
