<?php

namespace Polirium\Core\Base\Facade;

use Illuminate\Support\Facades\Facade;
use Polirium\Core\Base\Service\LocationService;

/**
 * @method static getProvinces(?int $include_id = null)
 * @method static getDistricts(int $province_id, ?int $include_id = null)
 * @method static getWards(int $district_id, ?int $include_id = null)
 * @method static getWardsByProvince(int $province_id, ?int $include_id = null)
 *
 * @see Polirium\Core\Base\Service\LocationService
 */
class LocationHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return LocationService::class;
    }
}
