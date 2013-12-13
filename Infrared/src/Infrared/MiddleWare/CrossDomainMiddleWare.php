<?php
namespace Infrared\MiddleWare;

use Slim\Middleware;

class CrossDomainMiddleWare extends Middleware
{
    public function call()
    {
        $url = $this->app->request->getPath();
        $method = $this->app->request->getMethod();
        $isClickRegister = $this->app->router()->getNamedRoute('register_clicks')->matches($url)
            && $method == 'POST';

        if(!$isClickRegister) {
            $this->next->call();
        } else {
            $origin = $this->app->request->headers->get('origin');
            $this->app->response->headers->set('Access-Control-Allow-Origin', $origin);

            $this->app->domain = parse_url($origin, PHP_URL_HOST);

            $this->next->call();

            // get user from context and check if he own the domain
            // set proper header
        }
    }
}