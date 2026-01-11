<?php

namespace Polirium\Core\Base\Widgets;

use Polirium\Core\Base\Http\Models\User;

/**
 * Stats Widget
 *
 * Shows quick statistics for the dashboard.
 */
class StatsWidget extends AbstractWidget
{
    public static function getWidgetId(): string
    {
        return 'core.stats';
    }

    public static function getWidgetName(): string
    {
        return 'Thống kê nhanh';
    }

    public static function getIcon(): string
    {
        return 'chart-bar';
    }

    public static function getDescription(): string
    {
        return 'Hiển thị các thống kê quan trọng';
    }

    public static function getDefaultWidth(): int
    {
        return 12;
    }

    public static function getDefaultHeight(): int
    {
        return 1;
    }

    public static function getPermissions(): array
    {
        return [
            'core/base::widgets.stats'
        ];
    }

    protected static function getComponentName(): string
    {
        return 'core/base::widgets.stats';
    }

    public function render()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $totalRoles = \Polirium\Core\Base\Http\Models\Role::count();

        return view('core/base::widgets.stats', [
            'stats' => [
                [
                    'label' => 'Tổng người dùng',
                    'value' => $totalUsers,
                    'icon' => 'users',
                    'color' => 'primary',
                ],
                [
                    'label' => 'Đang hoạt động',
                    'value' => $activeUsers,
                    'icon' => 'user-check',
                    'color' => 'success',
                ],
                [
                    'label' => 'Vai trò',
                    'value' => $totalRoles,
                    'icon' => 'shield',
                    'color' => 'info',
                ],
            ],
        ]);
    }
}
