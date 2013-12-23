<?php

class IndexController extends \Phalcon\Mvc\Controller
{
    public function indexAction()
    {
        if($this->session->get('auth')) {
            return $this->response->redirect(array('for' => 'domains'));
        }

        if ($this->session->get('email_sent')) {
            $this->dispatcher->forward(array('action' => 'confirm'));
        }
    }

    public function confirmAction(){
        if ($msg = $this->session->get('email_sent')) {
            $this->session->remove('email_sent');
            $this->view->setVar('message', $msg);
        } else {
            return $this->response->redirect(array('for' => 'front'));
        }
    }
}