<?php

$router = new \Phalcon\Mvc\Router();

$router->addGet('/', 'index::index')->setName('front');;
$router->addPost('/login', 'login::login');
$router->addGet('/comein/{key:\w+}', 'login::validate')->setName('validate_login');
$router->addGet('/logout', 'login::logout');

$router->addGet('/domains', 'domains::index')->setName('domains');
$router->addGet('/domains/new', 'domains::create')->setName('create_domain');
$router->addPost('/domains/new', 'domains::save')->setName('save_domain');
$router->addDelete('/domains/{key:\d+}', 'domains::delete')->setName('delete_domain');

$router->addGet('/instructions', 'instructions::index')->setName('instructions');

$router->addOptions('/api/clicks', 'api::preflight')->setName('preflight');
$router->addPost('/api/clicks', 'api::recordClicks')->setName('record_clicks');
$router->addGet('/api/domains/{domain}/clicks', 'api::getClicks')->setName('get_clicks');

return $router;