<?php

class InstructionsController extends \Phalcon\Mvc\Controller
{
    public function initialize()
    {
        if(!$this->session->get('auth')) {
            return $this->response->redirect(array('for' => 'front'));
        }
        $this->view->setVar("active_menu", 'instructions');
    }

    public function indexAction() {}
}