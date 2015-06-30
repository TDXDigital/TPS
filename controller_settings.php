<?php

$settings = array(
    'view' => new \Slim\Views\Twig(),
    'templates.path' => 'Views',
    'model' => (Object)array(
        "message" => "Hello World"
    )
);

return $settings;
