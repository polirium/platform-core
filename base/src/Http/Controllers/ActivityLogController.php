<?php

namespace Polirium\Core\Base\Http\Controllers;

use Polirium\Core\Base\Http\Controllers\BaseController;

class ActivityLogController extends BaseController
{
    public function index()
    {
        page_title()->setTitle(__('Lịch sử hoạt động'));

        return view('core/base::activity-log.index');
    }
}
