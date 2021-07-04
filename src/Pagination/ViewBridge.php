<?php

namespace Fluent\Orm\Pagination;

use CodeIgniter\Config\Services;
use CodeIgniter\View\RendererInterface;

class ViewBridge
{
    /** @var string */
    protected $view;

    /** @var RendererInterface */
    protected $viewBridge;

    /** @var mixed */
    protected $data;

    public function __construct()
    {
        $this->viewBridge = Services::renderer();
    }

    public function make($view, $data = [])
    {
        $this->view = $view;
        $this->data = $data;

        return $this;
    }

    public function render()
    {
        return $this->viewBridge->setData($this->data)->render($this->view);
    }

    public function __toString()
    {
        return $this->render();
    }
}
