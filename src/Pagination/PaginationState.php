<?php

namespace Fluent\Orm\Pagination;

use CodeIgniter\Config\Services;

class PaginationState
{
    /**
     * Bind the pagination state resolvers using the given application container as a base.
     *
     * @return void
     */
    public static function resolveUsing()
    {
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
    }
}
