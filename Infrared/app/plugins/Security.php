<?php

use Phalcon\Events\Event,
        Phalcon\Mvc\User\Plugin,
        Phalcon\Mvc\Dispatcher,
        Phalcon\Acl;

class Security extends Plugin
{
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
        $auth = $this->session->get('auth');
        if($auth && $user = User::findFirst($auth['user_id'])) {
            $di = $this->getDI()->set('user', $user);
        }
    }
}