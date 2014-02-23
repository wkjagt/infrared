<?php

require __DIR__.'/../vendor/autoload.php';

try {
    $config = require '../app/config/config.php';

    //Register an autoloader
    $loader = new \Phalcon\Loader();
    $loader->registerDirs([
        '../app/controllers/',
        '../app/models/',
        '../app/plugins/',
        '../app/emails/',
    ])->register();

    //Create a DI
    $di = new Phalcon\DI\FactoryDefault();

    $di->set('cache', function() use ($config) {
        return phpiredis_connect($config['redis']['host'], $config['redis']['port']);
    });

    $di->set('clickStorage', function() use ($di) {
        return new ClickStorage($di['cache']);
    });

    $di->set('session', function() use ($config) {
        $session = new Phalcon\Session\Adapter\Files($config['session']);
        $session->start();
        return $session;
    }, true);

    //Set up the flash service
    $di->set('flash', function() {
        return new \Phalcon\Flash\Session();
    });

    $di->set('mail_sender', function() use ($config) {
        return new MailSender($config['mandrill']['api_key']);
    });

    $di->set('db', function() use ($config){
        return new \Phalcon\Db\Adapter\Pdo\Mysql([
            "host" => $config['db']['host'],
            "username" => $config['db']['user'],
            "password" => $config['db']['pass'],
            "dbname" => $config['db']['db_name']
        ]);
    });

    //Register Volt as a service
    $di->set('voltService', function($view, $di) {

        $volt = new Phalcon\Mvc\View\Engine\Volt($view, $di);

        $volt->setOptions([
            "compiledPath" => "../app/compiled-templates/",
            "compiledExtension" => ".compiled",
            // "compileAlways" => true
        ]);
        return $volt;
    });

    //Setup the view component
    $di->set('view', function(){
        $view = new \Phalcon\Mvc\View();
        $view->setViewsDir('../app/views/');

        $view->registerEngines([
            ".volt" => 'voltService'
        ]);

        return $view;
    });

    //Setup a base URI so that all generated URIs include the "tutorial" folder
    $di->set('url', function(){
        $url = new \Phalcon\Mvc\Url();
        return $url;
    });

    $di->set('router', function(){
        require __DIR__.'/../app/config/routes.php';
        return $router;
    });
    
    $di->set('dispatcher', function() use ($di) {

        //Obtain the standard eventsManager from the DI
        $eventsManager = $di->getShared('eventsManager');

        $eventsManager->attach('dispatch', new Security($di));
        $eventsManager->attach('dispatch', new ExceptionHandler($di));

        $dispatcher = new Phalcon\Mvc\Dispatcher();

        //Bind the EventsManager to the Dispatcher
        $dispatcher->setEventsManager($eventsManager);

        return $dispatcher;
    });

    $di->set('url_parser', function(){
        $pslManager = new Pdp\PublicSuffixListManager();
        return new Pdp\Parser($pslManager->getList());
    });

    $application = new \Phalcon\Mvc\Application($di);
    echo $application->handle()->getContent();

} catch(\Phalcon\Exception $e) {
     error_log($e->getMessage());
     echo "PhalconException: ", $e->getMessage();
}