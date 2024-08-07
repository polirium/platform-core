<?php

namespace Polirium\Core\Base\Http\Controllers;

class UsersManagerController extends BaseController
{
    public function index()
    {
        return view('core/base::users.index');
    }
}
