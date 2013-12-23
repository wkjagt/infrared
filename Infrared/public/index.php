<?php

require __DIR__.'/../vendor/autoload.php';

try {
    //Register an autoloader
    $loader = new \Phalcon\Loader();
    $loader->registerDirs(array(
        '../app/controllers/',
        '../app/models/',
        '../app/plugins/',
        '../app/emails/',
    ))->register();

    //Create a DI
    $di = new Phalcon\DI\FactoryDefault();

    $di->set('cache', function(){
        return phpiredis_connect('127.0.0.1', 6379);
    });

    $di->set('clickStorage', function() use ($di) {
        return new ClickStorage($di['cache']);
    });

    $di->set('session', function(){
        $session = new Phalcon\Session\Adapter\Files();
        $session->start();
        return $session;
    }, true);

    //Set up the flash service
    $di->set('flash', function() {
        return new \Phalcon\Flash\Session();
    });

    $di->set('mail_sender', function(){
        return new MailSender('-TwREiIz1HQrMkeY2cvwcA'  );
    });

    $di->set('db', function(){
        return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
            "host" => "localhost",
            "username" => "root",
            "password" => "",
            "dbname" => "infrared"
        ));
    });

    //Register Volt as a service
    $di->set('voltService', function($view, $di) {

        $volt = new Phalcon\Mvc\View\Engine\Volt($view, $di);

        $volt->setOptions(array(
            "compiledPath" => "../app/compiled-templates/",
            "compiledExtension" => ".compiled",
            "compileAlways" => true
        ));
        return $volt;
    });

    //Setup the view component
    $di->set('view', function(){
        $view = new \Phalcon\Mvc\View();
        $view->setViewsDir('../app/views/');

        $view->registerEngines(array(
            ".volt" => 'voltService'
        ));

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

        //Instantiate the Security plugin
        $security = new Security($di);

        //Listen for events produced in the dispatcher using the Security plugin
        $eventsManager->attach('dispatch', $security);

        $dispatcher = new Phalcon\Mvc\Dispatcher();

        //Bind the EventsManager to the Dispatcher
        $dispatcher->setEventsManager($eventsManager);

        return $dispatcher;
    });

    $application = new \Phalcon\Mvc\Application($di);
    echo $application->handle()->getContent();

} catch(\Phalcon\Exception $e) {
     error_log($e->getMessage());
     echo "PhalconException: ", $e->getMessage();
}