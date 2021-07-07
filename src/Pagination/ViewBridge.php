<?php

namespace Fluent\Orm\Pagination;

use CodeIgniter\Config\Services;

class ViewBridge
{
    public function make($view, $data = [])
    {
        return Services::renderer()->setData($data)->render($view);
    }
}
