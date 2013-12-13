<?php
namespace Infrared\MiddleWare;

use Slim\Middleware;

/**
 * Api key middleware is needed when the Chrome extension tries to get the
 * clicks from the storage
 */
class ApiKeyMiddleWare extends Middleware
{
    public function call()
    {
        $url = $this->app->request->getPath();
        $method = $this->app->request->getMethod();
        $isChrome = $this->app->router()->getNamedRoute('get_clicks')->matches($url)
            && $method == 'GET';

        if(!$isChrome) {
            $this->next->call();            
        } else {
            $apiKey = $this->app->request->get('apikey');
            if($apiKey == '12345678') {
                // todo set user context
                $this->next->call();            
            } else {
                $this->next->call();            
                $this->app->response()->status(403);
                $this->app->response()->body(json_encode(array('error' => 'INVALID_APIKEY')));
            }
        }
    }
}