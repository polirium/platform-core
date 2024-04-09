<?php

namespace Polirium\Core\Base\Http\Controllers;

use Polirium\Core\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class BranchController extends BaseController
{
    public function index()
    {
        return view('core/base::branch.index');
    }
}
