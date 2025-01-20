<?php

namespace Polirium\Core\Base\Facade;

use Illuminate\Support\Facades\Facade;
use Polirium\Core\Base\Service\LocationService;

/**
 * @method static getProvinces()
 * @method static getDistricts(int $province_id)
 * @method static getWards(int $district_id)
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
