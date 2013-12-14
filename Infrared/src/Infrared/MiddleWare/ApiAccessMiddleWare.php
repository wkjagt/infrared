<?php
namespace Infrared\MiddleWare;

use Slim\Middleware;
use ORM;

/**
 * Api key middleware is needed when the Chrome extension tries to get the
 * clicks from the storage
 */
class ApiAccessMiddleWare extends Middleware
{
    public function call()
    {
        $request = $this->app->request;
        $router = $this->app->router;

        $routes = $router->getMatchedRoutes($request->getMethod(), $request->getPath());
        $isChrome = $routes && $routes[0]->getName() === 'get_clicks';

        if(!$isChrome) {
            $this->next->call();            
        } else {
            $apiKey = $this->app->request->get('apikey');
            $domain = $routes[0]->getParam('domain');

            $user = ORM::for_table('users')
                    ->join('domains', array('users.id', '=', 'domains.user_id'))
                    ->where('users.api_key', $apiKey)
                    ->where('domains.domain_name', $domain)
                    ->find_one();

            if($user) {
                // todo set user context
                $this->app->user = $user;
                $this->next->call();            
            } else {
                $this->app->response()->status(403);
                $this->app->response()->body(json_encode(array('error' => 'INVALID_APIKEY')));
            }
        }
    }
}