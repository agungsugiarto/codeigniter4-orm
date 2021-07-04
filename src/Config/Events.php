<?php

namespace Fluent\Orm\Config;

use CodeIgniter\Config\Services;
use CodeIgniter\Events\Events;
use Fluent\Orm\Pagination\Cursor;
use Fluent\Orm\Pagination\CursorPaginator;
use Fluent\Orm\Pagination\Paginator;
use Fluent\Orm\Pagination\ViewBridge;

Events::on('pre_system', function () {
    Paginator::viewFactoryResolver(function () {
        return new ViewBridge();
    });

    Paginator::currentPathResolver(function () {
        return current_url();
    });

    Paginator::currentPageResolver(function ($pageName = 'page') {
        $page = Services::request()->getVar($pageName);

        if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int) $page >= 1) {
            return (int) $page;
        }

        return 1;
    });

    Paginator::queryStringResolver(function () {
        return Services::uri()->getQuery();
    });

    CursorPaginator::currentCursorResolver(function ($cursorName = 'cursor') {
        return Cursor::fromEncoded(Services::request()->getVar($cursorName));
    });
});
