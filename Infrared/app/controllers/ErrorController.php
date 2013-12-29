<?php

class ErrorController extends \Phalcon\Mvc\Controller
{
    public function show404Action()
    {
        $this->response->setStatusCode(404, '')->setContent('Not Found')->send();
    }
}