<?php

namespace Polirium\Core\Base\Service;

use Polirium\Core\Base\Http\Models\District;
use Polirium\Core\Base\Http\Models\Province;
use Polirium\Core\Base\Http\Models\Ward;

class LocationService
{
    public function getProvinces(?int $include_id = null)
    {
        $provinces = cache()->remember('provinces', 2592000, function () {
            return Province::where('published', true)->select('id', 'name')->pluck('name', 'id')->toArray();
        });

        return $this->includeSelectedLocation($provinces, new Province(), $include_id);
    }

    public function getDistricts(int $province_id, ?int $include_id = null)
    {
        $districts = cache()->remember('districts_' . $province_id, 2592000, function () use ($province_id) {
            return District::where('province_id', $province_id)
                ->where('published', true)
                ->select('id', 'name')
                ->pluck('name', 'id')
                ->toArray();
        });

        return $this->includeSelectedLocation($districts, new District(), $include_id);
    }

    public function getWards(int $district_id, ?int $include_id = null)
    {
        $wards = cache()->remember('wards_' . $district_id, 2592000, function () use ($district_id) {
            return Ward::where('district_id', $district_id)
                ->where('published', true)
                ->select('id', 'name')
                ->pluck('name', 'id')
                ->toArray();
        });

        return $this->includeSelectedLocation($wards, new Ward(), $include_id);
    }

    public function getWardsByProvince(int $province_id, ?int $include_id = null)
    {
        $wards = cache()->remember('wards_by_province_' . $province_id, 2592000, function () use ($province_id) {
            return Ward::where('province_id', $province_id)
                ->where('published', true)
                ->select('id', 'name')
                ->pluck('name', 'id')
                ->toArray();
        });

        return $this->includeSelectedLocation($wards, new Ward(), $include_id);
    }

    private function includeSelectedLocation(array $locations, Province|District|Ward $model, ?int $include_id): array
    {
        if ($include_id && ! array_key_exists($include_id, $locations)) {
            $selected = $model->newQuery()->find($include_id, ['id', 'name']);

            if ($selected) {
                $locations[$selected->id] = $selected->name;
            }
        }

        return $locations;
    }
}
