<?php

namespace Infrared;

use Infrared\MiddleWare\SetupMiddleWare;
use Infrared\MiddleWare\ApiKeyMiddleWare;
use Infrared\MiddleWare\CrossDomainMiddleWare;
use ORM;

class App
{
    protected $config;

    protected $slim;

    protected function __construct($config)
    {
        $this->config = $config;
        $this->slim = new \Slim\Slim(array(
            'debug' => true,
            'config' => $config
        ));

        $this->registerMiddleWare();
        $this->addRoutes();
    }

    public function run()
    {
        $this->slim->run();
    }

    public static function init($config)
    {
        return new static($config);
    }

    protected function addRoutes()
    {
        $this->slim->get('/', array($this, 'front'));
        $this->slim->post('/come-in', array($this, 'entrance'));

        $this->slim->get('/api/domains/:domain/clicks', array($this, 'getClicks'))->name('get_clicks');
        $this->slim->post('/api/domains/:domain/clicks', array($this, 'registerClicks'))->name('register_clicks');
    }

    protected function registerMiddleWare()
    {
        $this->slim->add(new SetupMiddleWare);
        $this->slim->add(new ApiKeyMiddleWare);
        $this->slim->add(new CrossDomainMiddleWare);
    }

    public function front() {
        echo $this->slim->twig->render('front.html.twig', array());
    }

    public function entrance() {
        $email = $this->slim->request->post('email');

        $user = ORM::for_table('users')->where('email', $email)->find_one();
        $sessionKey = md5(sprintf('%s%s%s', $email, microtime(), uniqid()));

        if(!$user) {
            // signup
            $user = ORM::for_table('users')->create();
            $user->email = $email;
        }
        $user->session_key = $sessionKey;
        $user->save();

        setcookie('session_key', $sessionKey);
    }

    public function getClicks($domain)
    {
        // validate required parameters
        if(!$page = $this->slim->request->get('page')) {
            $this->slim->response->setStatus(400);
            return;
        }
        $clicks = $this->slim->storage->retrieve($page, $domain);
        echo json_encode($clicks);
    }

    public function registerClicks()
    {   
        $domainName = $this->slim->domain;
        $domain = $this->config->domains->$domainName;

        $toStore = array();
        $patterns = (array) $domain->url_patterns;

        foreach($this->slim->request->post('clicks') as $click) {

            // apply patterns
            $click['page'] = preg_replace(
                array_keys($patterns),
                array_values($patterns),
                $click['page']
            );
            $toStore[] = $click;
        }
        $this->slim->storage->store($toStore, $domainName);
    }

}