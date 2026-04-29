<?php

namespace Polirium\Core\Base\Http\Controllers;

use Polirium\Core\UI\Facades\Assets;

class BranchController extends BaseController
{
    public function index()
    {
        Assets::loadCss('professional-detail-view');

        return view('core/base::branch.index');
    }
}
