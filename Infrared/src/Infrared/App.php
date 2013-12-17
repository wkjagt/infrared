<?php

namespace Infrared;

use Infrared\MiddleWare\SetupMiddleWare;
use Infrared\MiddleWare\ApiAccessMiddleWare;
use Infrared\MiddleWare\CrossDomainMiddleWare;
use Infrared\MiddleWare\SessionMiddleWare;
use Slim\Middleware\SessionCookie;
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
            'config' => $config,
            'templates.path' => __DIR__.'/../../templates',
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
        // landing
        $this->slim->get('/', array($this, 'front'))->name('front');

        // login
        $this->slim->post( '/login', array($this, 'login'));
        $this->slim->get(  '/logout', array($this, 'logout'));
        $this->slim->get(  '/come-in/:session_key', array($this, 'validateSession'))->name('validate_session');

        $this->slim->get(  '/apikey', array($this, 'apikey'))->name('apikey');

        // domains
        $this->slim->get(  '/domains', array($this, 'domains'))->name('domains');
        $this->slim->get(  '/domains/new', array($this, 'newDomain'))->name('new_domain');
        $this->slim->post( '/domains/new', array($this, 'saveDomain'))->name('register_domain');

        $this->slim->get(  '/api/domains/:domain/clicks', array($this, 'getClicks'))->name('get_clicks');
        $this->slim->post( '/api/domains/:domain/clicks', array($this, 'registerClicks'))->name('register_clicks');
    }

    protected function registerMiddleWare()
    {
        $this->slim->add(new SessionMiddleWare);
        $this->slim->add(new ApiAccessMiddleWare);
        $this->slim->add(new CrossDomainMiddleWare);
        $this->slim->add(new SetupMiddleWare);
        $this->slim->add(new SessionCookie);
    }

    public function front() {
        if($this->slim->user) {
            $this->slim->flash('info', 'Welcome back!');
            $this->slim->redirect($this->slim->urlFor('domains'));
        }

        $flash = $this->slim->view->getData('flash');

        if($flash['info']) {
            $this->slim->render('landing/front-confirm.html.twig', array());                    
        } else {
            $this->slim->render('landing/front.html.twig', array());

        }
    }

    public function login() {
        $email = $this->slim->request->post('email');

        $user = ORM::for_table('users')->where('email', $email)->find_one();
        $sessionKey = bin2hex(openssl_random_pseudo_bytes(40));

        $scheme = $this->slim->request->getScheme();
        $host = $this->slim->request->getHost();
        $path = $this->slim->urlFor('validate_session',
            array('session_key'=> urlencode($sessionKey)));

        if(!$user) {
            // signup
            $user = ORM::for_table('users')->create();
            $user->email = $email;
            $user->api_key = bin2hex(openssl_random_pseudo_bytes(40));
            $this->slim->flash('info', 'We\'ve emailed you a link to your new account!');
        } else {
            $this->slim->flash('info', 'We\'ve emailed you a link to login to your account!');
        }
        $user->session_key = $sessionKey;
        $user->save();

        $mail = new Data\LoginEmail($email, sprintf('%s://%s%s', $scheme, $host, $path));
        $this->slim->mailsender->send($mail);
        $this->slim->redirect($this->slim->urlFor('front'));
    }

    public function logout()
    {
        $this->slim->deleteCookie('session');
        $this->slim->redirect($this->slim->urlFor('front'));
    }

    public function validateSession($sessionKey)
    {
        $user = ORM::for_table('users')->where('session_key', $sessionKey)->find_one();
        if($user) {
            $this->slim->setCookie('session', $sessionKey, '1 year');

            // redirect to domains
            $this->slim->redirect($this->slim->urlFor('domains'));
        }
        $this->slim->flash('error', 'There seems to be something wrong with that link');
        $this->slim->redirect($this->slim->urlFor('front'));
    }

    public function apikey()
    {
        $this->slim->render('admin/apikey.html.twig', array(
                                        'api_key' => $this->slim->user->api_key));
    }

    public function domains()
    {
        if(!$this->slim->user) {
            $this->slim->redirect($this->slim->urlFor('front'));
        }
        $domains = ORM::for_table('domains')
                            ->where('user_id', $this->slim->user->id)->find_many();

        $this->slim->render('admin/domains.html.twig', array('domains' => $domains));
    }

    public function newDomain()
    {
        $this->slim->render('admin/new_domain.html.twig');
    }

    public function saveDomain()
    {
        $domainName = $this->slim->request->post('domain_name');
        
        // http://stackoverflow.com/a/4694816/520819
        $valid = preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domainName) //valid chars check
                 && preg_match("/^.{1,253}$/", $domainName) //overall length check
                 && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domainName); //length of each label
        
        $exist = (bool) ORM::for_table('domains')
            ->where('domain_name', $domainName)->where('user_id', $this->slim->user->id)->count();




    }

    public function getClicks($domainName)
    {
        $response = $this->slim->response;

        if(!$page = $this->slim->request->get('page')) {
            $response->setStatus(400);
            return;
        }

        $domain = ORM::for_table('domains')->where('domain_name', $domainName)->find_one();
        $patterns = $domain->replacements
            ? json_decode($domain->replacements, true)
            : array();

        $page = preg_replace(array_keys($patterns), array_values($patterns), $page);
        $clicks = $this->slim->storage->retrieve($page, $domainName);
        $response->headers->set('Content-Type', 'application/json');

        $response->setBody(json_encode($clicks));
    }

    public function registerClicks()
    {   
        $domain = $this->slim->domain;

        $toStore = array();
        $patterns = $domain->replacements
            ? json_decode($domain->replacements, true)
            : array();

        foreach($this->slim->request->post('clicks') as $click) {
            // apply patterns
            $click['page'] = preg_replace(
                array_keys($patterns),
                array_values($patterns),
                $click['page']
            );
            $toStore[] = $click;
        }
        $this->slim->storage->store($toStore, $domain->domain_name);
    }

}