<?php

use Polirium\Core\Base\Facade\LocationHelper;
use Polirium\Core\Base\Helpers\BaseHelper;

/**
 * |--------------------------------------------------------------------------
 * | Platform path helpers
 * |--------------------------------------------------------------------------
 * |
 */
if (! function_exists('platform_path')) {
    function platform_path(?string $path = null): string
    {
        return base_path('platform/' . $path);
    }
}

if (! function_exists('core_path')) {
    function core_path(?string $path = null): string
    {
        return platform_path('core/' . $path);
    }
}

if (! function_exists('modules_path')) {
    function modules_path(?string $path = null): string
    {
        return platform_path('modules/' . $path);
    }
}

if (! function_exists('package_path')) {
    function package_path(?string $path = null): string
    {
        return platform_path('packages/' . $path);
    }
}

if (! function_exists('admin_prefix')) {
    function admin_prefix(?string $path = null): ?string
    {
        return BaseHelper::getAdminPrefix();
    }
}

if (! function_exists('core_can')) {
    function core_can(string $permissions): bool
    {
        if (auth()->user()->isSuperAdmin()) {
            return true;
        }

        return auth()->user()->can($permissions);
    }
}

if (! function_exists('roman_numerals')) {
    function roman_numerals($decimalInteger)
    {
        $n = intval($decimalInteger);
        $res = '';

        $roman_numerals = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1, ];

        foreach ($roman_numerals as $roman => $numeral) {
            $matches = intval($n / $numeral);
            $res .= str_repeat($roman, $matches);
            $n = $n % $numeral;
        }

        return $res;
    }
}

if (! function_exists('number_to_text')) {
    /**
     * Convert number to text representation.
     *
     * @param float|int $number The number to convert
     * @param string|null $locale Locale code (vi, en). Defaults to app locale
     * @param string|null $currency Currency code (VND, USD). Defaults to provider default
     * @return string Text representation
     *
     * @example
     * number_to_text(1500000);              // 'Một triệu năm trăm nghìn đồng' (default vi)
     * number_to_text(1500000, 'en');        // 'One million five hundred thousand dollars'
     * number_to_text(1500000, 'vi', 'USD'); // 'Một triệu năm trăm nghìn đô la Mỹ'
     */
    function number_to_text(float|int $number, ?string $locale = null, ?string $currency = null): string
    {
        $service = new \Polirium\Core\Base\Service\NumberToText\NumberToTextService();

        return $service->convert($number, $locale, $currency);
    }
}

if (! function_exists('get_provinces')) {
    function get_provinces(?int $include_id = null)
    {
        return LocationHelper::getProvinces($include_id);
    }
}

if (! function_exists('get_districts')) {
    function get_districts(int $province_id, ?int $include_id = null)
    {
        return LocationHelper::getDistricts($province_id, $include_id);
    }
}

if (! function_exists('get_wards')) {
    function get_wards(int $district_id, ?int $include_id = null)
    {
        return LocationHelper::getWards($district_id, $include_id);
    }
}

if (! function_exists('get_wards_by_province')) {
    function get_wards_by_province(int $province_id, ?int $include_id = null)
    {
        return LocationHelper::getWardsByProvince($province_id, $include_id);
    }
}

if (! function_exists('user_branch')) {
    /**
     * Get or set the current user's selected branch
     *
     * @param int|string|null $branch_id
     * @return int|null
     */
    function user_branch($branch_id = null): ?int
    {
        if (! auth()->check()) {
            return null;
        }

        $user = auth()->user();
        $sessionKey = 'user_branch_' . $user->id;

        // If setting a branch
        if ($branch_id !== null && $branch_id !== '' && $branch_id !== 0 && $branch_id !== '0') {
            // Cast to int
            $branch_id = (int) $branch_id;

            // Super admin can access any branch
            if ((bool) $user->super_admin === true) {
                // Verify the branch exists
                $branchExists = \Polirium\Core\Base\Http\Models\Branch\Branch::where('id', $branch_id)->exists();
                if ($branchExists) {
                    session()->put($sessionKey, $branch_id);
                    session()->save();

                    return $branch_id;
                }

                return null;
            }

            // Regular users need to be assigned to the branch
            $hasAccess = $user->branches()->where('branch_id', $branch_id)->exists();

            if ($hasAccess) {
                session()->put($sessionKey, $branch_id);
                session()->save();

                return $branch_id;
            }

            return null;
        }

        // If getting the current branch
        $currentBranch = session()->get($sessionKey);

        // If no branch is set in session, get the user's first active branch
        if (! $currentBranch) {
            $firstBranch = $user->branches()
                ->wherePivot('active', 1)
                ->first();

            if ($firstBranch) {
                $currentBranch = $firstBranch->id;
                session()->put($sessionKey, $currentBranch);
                session()->save();
            } else {
                // If no active branch, try to get any branch the user has access to
                $anyBranch = $user->branches()->first();
                if ($anyBranch) {
                    $currentBranch = $anyBranch->id;
                    session()->put($sessionKey, $currentBranch);
                    session()->save();
                } else {
                    // Fallback: get the first branch in the database (for super admin or unassigned users)
                    $fallbackBranch = \Polirium\Core\Base\Http\Models\Branch\Branch::first();
                    if ($fallbackBranch) {
                        $currentBranch = $fallbackBranch->id;
                        session()->put($sessionKey, $currentBranch);
                        session()->save();
                    }
                }
            }
        }

        return $currentBranch;
    }
}

if (! function_exists('user_can_access_branch')) {
    /**
     * Whether the signed-in user may work with records of this branch:
     * super admins with every branch, other users with the ones assigned to them.
     */
    function user_can_access_branch($branch_id): bool
    {
        $user = auth()->user();

        if (! $user || ! $branch_id) {
            return false;
        }

        if ((bool) $user->super_admin === true) {
            return true;
        }

        return $user->branches()->where('branch_id', (int) $branch_id)->exists();
    }
}

if (! function_exists('user_branch_ids')) {
    /**
     * Ids of the branches the signed-in user may work with, or null for all of them (super admin).
     *
     * @return array<int, int>|null
     */
    function user_branch_ids(): ?array
    {
        $user = auth()->user();

        if (! $user) {
            return [];
        }

        if ((bool) $user->super_admin === true) {
            return null;
        }

        return $user->branches()->pluck('branches.id')->map(fn ($id) => (int) $id)->all();
    }
}

if (! function_exists('admin_notification')) {
    /**
     * Register a listener for admin bar notifications.
     * Call this in your ServiceProvider's boot() method.
     *
     * @param callable $callback A callback that receives RenderingAdminBarNotification event
     *                          and can call $event->addNotification([...])
     * @return void
     *
     * Example usage in a ServiceProvider:
     *
     * admin_notification(function ($event) {
     *     $pendingOrders = Order::where('status', 'pending')->count();
     *     if ($pendingOrders > 0) {
     *         $event->addNotification([
     *             'title' => 'Đơn hàng chờ xử lý',
     *             'description' => "Có {$pendingOrders} đơn hàng cần xử lý",
     *             'actionUrl' => route('orders.index'),
     *             'isNew' => true,
     *             'dotColor' => 'red',
     *         ]);
     *     }
     * });
     */
    function admin_notification(callable $callback): void
    {
        app('events')->listen(
            \Polirium\Core\Base\Events\RenderingAdminBarNotification::class,
            $callback
        );
    }
}
