<?php

namespace Infrared;

use Infrared\MiddleWare\SetupMiddleWare;
use Infrared\MiddleWare\ApiAccessMiddleWare;
use Infrared\MiddleWare\CrossDomainMiddleWare;
use Infrared\MiddleWare\SessionMiddleWare;
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
        $this->slim->get('/', array($this, 'front'))->name('front');
        $this->slim->post('/login', array($this, 'login'));
        $this->slim->get('/come-in/:session_key', array($this, 'validateSession'))->name('validate_session');
        $this->slim->get('/profile', array($this, 'profile'))->name('profile');

        $this->slim->get('/api/domains/:domain/clicks', array($this, 'getClicks'))->name('get_clicks');
        $this->slim->post('/api/domains/:domain/clicks', array($this, 'registerClicks'))->name('register_clicks');
    }

    protected function registerMiddleWare()
    {
        $this->slim->add(new SessionMiddleWare);
        $this->slim->add(new ApiAccessMiddleWare);
        $this->slim->add(new CrossDomainMiddleWare);
        $this->slim->add(new SetupMiddleWare);
    }

    public function front() {
        // $msg = 
        echo $this->slim->twig->render('front.html.twig', array());
    }

    public function login() {
        $email = $this->slim->request->post('email');

        $user = ORM::for_table('users')->where('email', $email)->find_one();
        $sessionKey = md5(sprintf('%s%s%s', $email, microtime(), uniqid()));

        $scheme = $this->slim->request->getScheme();
        $host = $this->slim->request->getHost();
        $path = $this->slim->urlFor('validate_session', array('session_key'=> $sessionKey));

        if(!$user) {
            // signup
            $user = ORM::for_table('users')->create();
            $user->email = $email;
            $user->api_key = md5(sprintf('%s%s%s', $email, microtime(), uniqid()));
        }
        $user->session_key = $sessionKey;
        $user->save();

        $mail = new Data\LoginEmail($email, sprintf('%s://%s%s', $scheme, $host, $path));
        $this->slim->mailsender->send($mail);
    }

    public function validateSession($sessionKey)
    {
        $user = ORM::for_table('users')->where('session_key', $sessionKey)->find_one();
        if($user) {
            $this->slim->setCookie('session', $sessionKey, '1 year');

            // redirect to profile
            $this->slim->redirect($this->slim->urlFor('profile'));
        }
        $this->slim->flash('error', 'There seems to be somemthing wrong with that link');
        $this->slim->redirect($this->slim->urlFor('front'));
    }

    public function profile()
    {
        echo 'woot!';
    }

    public function getClicks($domain)
    {
        $response = $this->slim->response;

        if(!$page = $this->slim->request->get('page')) {
            $response->setStatus(400);
            return;
        }
        $clicks = $this->slim->storage->retrieve($page, $domain);
        $response->headers->set('Content-Type', 'application/json');
        $response->setBody(json_encode($clicks));
    }

    public function registerClicks()
    {   
        $domainName = $this->slim->domain->domain_name;

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