<?php

$settings = array(
    'view' => new \Slim\Views\Twig(),
    'templates.path' => 'Views',
    'model' => (Object)array(
        "message" => "Lipsum",
        "append" => "?twig",
        "siteName" => "TPS Broadcast"
    )
);

return $settings;
