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
    function platform_path(string $path = null): string
    {
        return base_path('platform/' . $path);
    }
}

if (! function_exists('core_path')) {
    function core_path(string $path = null): string
    {
        return platform_path('core/' . $path);
    }
}

if (! function_exists('modules_path')) {
    function modules_path(string $path = null): string
    {
        return platform_path('modules/' . $path);
    }
}

if (! function_exists('package_path')) {
    function package_path(string $path = null): string
    {
        return platform_path('packages/' . $path);
    }
}

if (! function_exists('admin_prefix')) {
    function admin_prefix(string $path = null): ?string
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
    function number_to_text($number)
    {
        $amount = round($number);
        if ($amount <= 0) {
            return $textnumber = 'Tiền phải là dạng số lớn hơn 0';
        }
        $Text = ['không', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];
        $TextLuythua = ['', 'nghìn', 'triệu', 'tỷ', 'ngān tỷ', 'triệu tỷ', 'tỷ tỷ'];
        $textnumber = '';
        $negative = 'âm ';
        $decimal = ' phẩy ';
        $length = strlen($amount);

        for ($i = 0; $i < $length; $i++) {
            $unread[$i] = 0;
        }

        for ($i = 0; $i < $length; $i++) {
            $so = substr($amount, $length - $i - 1, 1);

            if (($so == 0) && ($i % 3 == 0) && ($unread[$i] == 0)) {
                for ($j = $i + 1; $j < $length; $j++) {
                    $so1 = substr($amount, $length - $j - 1, 1);
                    if ($so1 != 0) {
                        break;
                    }
                }

                if (intval(($j - $i) / 3) > 0) {
                    for ($k = $i; $k < intval(($j - $i) / 3) * 3 + $i; $k++) {
                        $unread[$k] = 1;
                    }
                }
            }
        }

        for ($i = 0; $i < $length; $i++) {
            $so = substr($amount, $length - $i - 1, 1);
            if ($unread[$i] == 1) {
                continue;
            }

            if (($i % 3 == 0) && ($i > 0)) {
                $textnumber = $TextLuythua[$i / 3] . ' ' . $textnumber;
            }

            if ($i % 3 == 2) {
                $textnumber = 'trăm ' . $textnumber;
            }

            if ($i % 3 == 1) {
                $textnumber = 'mươi ' . $textnumber;
            }

            $textnumber = $Text[$so] . ' ' . $textnumber;
        }

        //Phai de cac ham replace theo dung thu tu nhu the nay
        $textnumber = str_replace('không mươi', 'lẻ', $textnumber);
        $textnumber = str_replace('lẻ không', '', $textnumber);
        $textnumber = str_replace('mươi không', 'mươi', $textnumber);
        $textnumber = str_replace('một mươi', 'mười', $textnumber);
        $textnumber = str_replace('mươi năm', 'mươi lăm', $textnumber);
        $textnumber = str_replace('mươi một', 'mươi mốt', $textnumber);
        $textnumber = str_replace('mười năm', 'mười lăm', $textnumber);

        return ucfirst($textnumber . ' đồng');
    }
}

if (! function_exists('get_provinces')) {
    function get_provinces()
    {
        return LocationHelper::getProvinces();
    }
}

if (! function_exists('get_districts')) {
    function get_districts(int $province_id)
    {
        return LocationHelper::getDistricts($province_id);
    }
}

if (! function_exists('get_wards')) {
    function get_wards(int $district_id)
    {
        return LocationHelper::getWards($district_id);
    }
}

if (! function_exists('user_branch')) {
    /**
     * Get or set the current user's selected branch
     *
     * @param int|null $branch_id
     * @return int|null
     */
    function user_branch(int $branch_id = null): ?int
    {
        if (! auth()->check()) {
            return null;
        }

        $user = auth()->user();
        $sessionKey = 'user_branch_' . $user->id;

        // If setting a branch
        if ($branch_id !== null) {
            // Verify the user has access to this branch
            $hasAccess = $user->branches()->where('branch_id', $branch_id)->exists();

            if ($hasAccess) {
                session([$sessionKey => $branch_id]);

                return $branch_id;
            }

            return null;
        }

        // If getting the current branch
        $currentBranch = session($sessionKey);

        // If no branch is set in session, get the user's first active branch
        if (! $currentBranch) {
            $firstBranch = $user->branches()
                ->wherePivot('active', 1)
                ->first();

            if ($firstBranch) {
                $currentBranch = $firstBranch->id;
                session([$sessionKey => $currentBranch]);
            } else {
                // If no active branch, try to get any branch the user has access to
                $anyBranch = $user->branches()->first();
                if ($anyBranch) {
                    $currentBranch = $anyBranch->id;
                    session([$sessionKey => $currentBranch]);
                }
            }
        }

        return $currentBranch;
    }
}
