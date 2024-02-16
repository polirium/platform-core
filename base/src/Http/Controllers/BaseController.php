<?php

namespace Polirium\Core\Base\Http\Controllers;

use Illuminate\Routing\Controller as IlluminateController;

class BaseController extends IlluminateController
{
    public function __construct()
    {
        $this->middleware('auth');
    }
}
