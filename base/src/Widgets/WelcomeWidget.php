<?php

namespace Polirium\Core\Base\Widgets;

/**
 * Welcome Widget
 *
 * Shows welcome message and quick actions.
 */
class WelcomeWidget extends AbstractWidget
{
    public static function getWidgetId(): string
    {
        return 'core.welcome';
    }

    public static function getWidgetName(): string
    {
        return 'Chào mừng';
    }

    public static function getIcon(): string
    {
        return 'home';
    }

    public static function getDescription(): string
    {
        return 'Lời chào và các hành động nhanh';
    }

    public static function getDefaultWidth(): int
    {
        return 6;
    }

    public static function getDefaultHeight(): int
    {
        return 2;
    }

    public static function getPermissions(): array
    {
        return [
            'core/base::widgets.welcome'
        ];
    }

    protected static function getComponentName(): string
    {
        return 'core/base::widgets.welcome';
    }

    public function render()
    {
        return view('core/base::widgets.welcome', [
            'user' => auth()->user(),
            'quickActions' => [
                [
                    'label' => 'Thêm người dùng',
                    'route' => 'core.users.index',
                    'icon' => 'user-plus',
                    'color' => 'primary',
                ],
                [
                    'label' => 'Quản lý vai trò',
                    'route' => 'core.roles.index',
                    'icon' => 'shield',
                    'color' => 'info',
                ],
            ],
        ]);
    }
}
