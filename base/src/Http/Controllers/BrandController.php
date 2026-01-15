<?php

namespace Polirium\Core\Base\Http\Controllers;

use Polirium\Core\Base\Http\Controllers\BaseController;
use Polirium\Core\UI\Facades\Assets;
use Illuminate\Http\Request;

class BrandController extends BaseController
{
    public function index()
    {
        Assets::loadCss('brand-modal');
        return view('core/base::brand.index');
    }
}
