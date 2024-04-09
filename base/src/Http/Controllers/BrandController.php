<?php

namespace Polirium\Core\Base\Http\Controllers;

use Polirium\Core\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class BrandController extends BaseController
{
    public function index()
    {
        return view('core/base::brand.index');
    }
}
