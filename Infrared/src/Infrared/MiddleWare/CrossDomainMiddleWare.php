<?php
namespace Infrared\MiddleWare;

use Slim\Middleware;
use ORM;

class CrossDomainMiddleWare extends Middleware
{
    public function call()
    {
        $app = $this->app;
        $request = $app->request;
        $router = $app->router;

        $routes = $router->getMatchedRoutes($request->getMethod(), $request->getPath());
        $isClickRegister = $routes &&
            in_array($routes[0]->getName(), array('preflight', 'register_clicks'));

        if(!$isClickRegister) {
            $this->next->call();
        } elseif($request->headers->has('origin')) {

            $origin = $request->headers->get('origin');
            $domainName = parse_url($origin, PHP_URL_HOST);

            if($routes[0]->getParam('domain') !== $domainName) {
                $app->halt(400);
            }

            if($cache = $app->cache->hgetall('domains:'.$domainName)) {
                $domain = ORM::for_table('domains')->hydrate($cache);
            } else {
                $domain = ORM::for_table('domains')->where('domain_name', $domainName)->find_one();
                $app->cache->pipeline(function($pipe) use($domain) {
                    $pipe->hmset('domains:'.$domain->domain_name, $domain->as_array());
                    $pipe->expire('domains:'.$domain->domain_name, 300);
                });
            }

            if(!$domain) {
                $app->halt(403);
            }

            $app->response->headers->set('Access-Control-Allow-Origin', $origin);
            $app->response->headers->set('Access-Control-Allow-Headers', 'Content-type');
            $app->domain = $domain;

            $this->next->call();
        }
    }
}