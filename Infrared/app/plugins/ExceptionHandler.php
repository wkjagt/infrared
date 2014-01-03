<?php

use Phalcon\Events\Event,
        Phalcon\Mvc\User\Plugin,
        Phalcon\Mvc\Dispatcher,
        Phalcon\Mvc\Dispatcher\Exception;

class ExceptionHandler extends Plugin
{
    public function beforeException(Event $event, Dispatcher $dispatcher, Exception $exception)
    {
        switch ($exception->getCode()) {
            case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
            case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                $dispatcher->forward(array('controller' => 'error', 'action' => 'show404'));
                return false;
        }
    }
}