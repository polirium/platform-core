<?php

namespace Polirium\Core\Base\Http\Controllers;

class DashboadController extends BaseController
{
    public function index()
    {
        return view('core/base::pages.dashboard');
    }
}
