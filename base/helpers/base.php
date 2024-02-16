<?php

use Carbon\Carbon;
use Illuminate\Support\Str;
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
    function admin_prefix(string $path = null): string
    {
        return BaseHelper::getAdminPrefix();
    }
}

if (! function_exists('core_roman_numerals')) {
    function core_roman_numerals($decimalInteger)
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

if (! function_exists('core_iteration_loop')) {
    function core_iteration_loop($loop)
    {
        $string = $loop->iteration;
        $get_loop = $loop;
        for ($i = 2; $i <= $loop->depth; $i++) {
            $get_loop = $get_loop->parent;
            $string = $get_loop->iteration . '.' . $string;
        }

        return $string;
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

if (! function_exists('core_number_format')) {
    function core_number_format($number, $type = 5, $double = 0)
    {
        return match ($type) {
            1 => number_format($number, $double, ',', ' '),
            2 => number_format($number, $double, ' ', ','),
            3 => number_format($number, $double, '.', ' '),
            4 => number_format($number, $double, ' ', '.'),
            5 => number_format($number, $double, '.', ','),
            6 => number_format($number, $double, ',', '.'),
            default => number_format($number, $double, ',', ' '),
        };
    }
}

if (! function_exists('core_format_date')) {
    function core_format_date($time, $type = 'date')
    {
        if ($time == null) {
            return 'Rỗng';
        }

        return match ($type) {
            'date' => Carbon::parse($time)->format('d/m/Y'),
            'time' => Carbon::parse($time)->format('H:i:s'),
            'datetime' => Carbon::parse($time)->format('d/m/Y H:i:s'),
            'h' => Carbon::parse($time)->format('H'),
            'i' => Carbon::parse($time)->format('i'),
            's' => Carbon::parse($time)->format('s'),
            'd' => Carbon::parse($time)->format('d'),
            'm' => Carbon::parse($time)->format('m'),
            'y' => Carbon::parse($time)->format('Y'),
            'dow' => Str::title(Carbon::parse($time)->dayName),
            default => 'Rỗng',
        };
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
