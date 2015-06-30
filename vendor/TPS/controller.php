<?php

namespace TPS;

Class Controller extends \Slim\Slim
{
    protected $data;

    public function __construct()
    {
        $settings = require("controller_settings.php");
        if (isset($settings['model'])) {
            $this->data = $settings['model'];
        }
        parent::__construct($settings);
    }

    public function render($name, $data = array(), $status = null)
    {
        if (strpos($name, ".html.twig") === false) {
            $name = $name . ".html.twig";
        }
        parent::render($name, $data, $status);
    }
}
