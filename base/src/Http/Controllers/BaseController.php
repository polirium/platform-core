<?php

namespace Polirium\Core\Base\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as IlluminateController;

class BaseController extends IlluminateController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth');
    }
}
