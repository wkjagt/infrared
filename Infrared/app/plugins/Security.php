<?php

use Phalcon\Events\Event,
        Phalcon\Mvc\User\Plugin,
        Phalcon\Mvc\Dispatcher;

class Security extends Plugin
{
    /**
     * If this gets more granular, use proper Acl classes
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
        $auth = $this->session->get('auth');
        if($auth && $user = User::findFirst($auth['user_id'])) {
            $di = $this->getDI()->set('user', $user);
        }

        $privateControllers = array('domains', 'instructions');

        $controller = $dispatcher->getControllerName();

        if(!$auth && in_array($controller, $privateControllers)) {
            $this->response->redirect(array('for' => 'front'));
            return false;
        }
    }
}