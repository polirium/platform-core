<?php

namespace Polirium\Core\Base\Http\Controllers;

use Polirium\Core\UI\Facades\Assets;

class DashboadController extends BaseController
{
    public function index()
    {
        // Load dashboard assets
        Assets::loadCss('dashboard');
        Assets::loadJs(['sortable', 'dashboard']);

        return view('core/base::pages.dashboard');
    }
}
