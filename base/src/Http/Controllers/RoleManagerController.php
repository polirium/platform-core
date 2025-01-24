<?php

namespace Polirium\Core\Base\Http\Controllers;

class RoleManagerController extends BaseController
{
    public function index()
    {
        return view('core/base::roles.index');
    }
}
