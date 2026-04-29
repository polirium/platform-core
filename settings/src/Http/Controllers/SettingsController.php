<?php

namespace Polirium\Core\Settings\Http\Controllers;

use Polirium\Core\Base\Http\Controllers\BaseController;
use Polirium\Core\Settings\Facades\SettingRegistry;

class SettingsController extends BaseController
{
    public function index()
    {
        // Load dropzone assets for file uploads
        load_css('dropzone');
        load_js('dropzone');

        $groups = SettingRegistry::getGroups();

        return view('core/settings::index', compact('groups'));
    }
}
