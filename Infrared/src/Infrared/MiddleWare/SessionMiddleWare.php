<?php
namespace Infrared\MiddleWare;

use Slim\Middleware;
use ORM;

/**
 * Api key middleware is needed when the Chrome extension tries to get the
 * clicks from the storage
 */
class SessionMiddleWare extends Middleware
{
    public function call()
    {
        if($sessionKey = $this->app->getCookie('session')) {
            $user = ORM::for_table('users')->where('session_key', $sessionKey)->find_one();
            if($user) {
                $this->app->user = $user;
                $this->app->view->setData('user', $user);
            }
        }
        $this->next->call();
    }
}