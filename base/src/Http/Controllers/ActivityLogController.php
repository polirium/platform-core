<?php

namespace Polirium\Core\Base\Http\Controllers;

class ActivityLogController extends BaseController
{
    public function index()
    {
        page_title()->setTitle(trans('core/base::general.activity_log'));

        return view('core/base::activity-log.index');
    }
}
