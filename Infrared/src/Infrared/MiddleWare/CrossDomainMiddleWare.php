<?php
namespace Infrared\MiddleWare;

use Slim\Middleware;
use ORM;

class CrossDomainMiddleWare extends Middleware
{
    public function call()
    {
        $request = $this->app->request;
        $router = $this->app->router;

        $routes = $router->getMatchedRoutes($request->getMethod(), $request->getPath());
        $isClickRegister = $routes && $routes[0]->getName() === 'register_clicks';

        if(!$isClickRegister) {
            $this->next->call();
        } elseif($request->headers->has('origin')) {

            $origin = $request->headers->get('origin');
            $domainName = parse_url($origin, PHP_URL_HOST);

            if($routes[0]->getParam('domain') !== $domainName) {
                $this->app->halt(400);
            }

            // check if registered domain
            // todo: cache this, so I don't hit the db for each click
            $domain = ORM::for_table('domains')->where('domain_name', $domainName)->find_one();

            if(!$result) {
                $this->app->halt(403);
            }

            $this->app->response->headers->set('Access-Control-Allow-Origin', $origin);
            $this->app->domain = $domain;

            $this->next->call();
        }
    }
}