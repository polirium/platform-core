<?php

namespace Polirium\Core\Base\Service;

use Polirium\Core\Base\Http\Models\District;
use Polirium\Core\Base\Http\Models\Province;
use Polirium\Core\Base\Http\Models\Ward;

class LocationService
{
    public function getProvinces()
    {
        $provinces = cache()->remember('provinces', 2592000, function () {
            return Province::select('id', 'name')->pluck('name', 'id')->toArray();
        });

        return $provinces;
    }

    public function getDistricts(int $province_id)
    {
        $districts = cache()->remember('districts_' . $province_id, 2592000, function () use ($province_id) {
            return District::where('province_id', $province_id)->select('id', 'name')->pluck('name', 'id')->toArray();
        });

        return $districts;
    }

    public function getWards(int $district_id)
    {
        $wards = cache()->remember('wards_' . $district_id, 2592000, function () use ($district_id) {
            return Ward::where('district_id', $district_id)->select('id', 'name')->pluck('name', 'id')->toArray();
        });

        return $wards;
    }
}
