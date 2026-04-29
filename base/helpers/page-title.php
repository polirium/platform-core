<?php

use Polirium\Core\Base\Services\PageTitle;

if (! function_exists('page_title')) {
    function page_title(): PageTitle
    {
        return app(PageTitle::class);
    }
}
