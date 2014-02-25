<?php

class DomainsController extends \Phalcon\Mvc\Controller
{
    public function initialize()
    {
        if(!$this->session->get('auth')) {
            return $this->response->redirect(array('for' => 'front'));
        }
        $this->view->setVar("active_menu", 'domains');
    }

    public function indexAction()
    {
        $domains = Domain::query()->where("user_id = :user_id:")
                ->bind(array("user_id" => $this->session->get('auth')['user_id']))
                ->execute();

        $this->view->setVar("domains", $domains);
    }

    public function createAction(){}

    public function saveAction()
    {
        $this->view->disable();
        $domainName = $this->request->getPost('domain_name');

        try {
            $domain = new Domain;
            $domain->user_id = $this->user->id;
            $domain->domain_name = $domainName;
            $domain->replacements = json_encode(array(':/\\d+:' => '/id'));
            $domain->save();

            $this->flash->success(
                sprintf('The domain %s was successfully registered.', $domainName));
            return $this->response->redirect(array('for' => 'confirm_domain', 'id' => $domain->id));
            
        } catch(DomainExistsException $e) {
            $this->flash->error('You\'ve already registered that domain');
            return $this->response->redirect(array('for' => 'create_domain'));            
        } catch(InvalidDomainNameException $e) {            
            $this->flash->error('That doesn\'t seem to be a valid domain. Please try again.');
            return $this->response->redirect(array('for' => 'create_domain'));
        }
    }

    public function confirmAction()
    {
        if(!$domain = $this->user->getDomain($this->dispatcher->getParam("id"))) {
            echo '404';die;
        }
        $this->view->setVar("domain", $domain);
    }

    public function confirmPostAction()
    {
        $this->view->disable();

        if(!$domain = $this->user->getDomain($this->dispatcher->getParam("id"))) {
            echo '404';die;
        }

        try {
            $domain->confirm($this->user->getPublicKey(),
                             $this->request->getPost('subdomain'));            
            $this->flash->error(
                sprintf('The domain %s was successfully confirmed.', $domain->domain_name));
            return $this->response->redirect(array('for' => 'domains'));

        } catch(DomainConfirmationException $e) {
            $this->flash->success(
                sprintf('There was an error confirming the domain %s.', $domain->domain_name));
            return $this->response->redirect(array('for' => 'confirm_domain', 'id' => $domain->id));
        }        
    }


    public function deleteAction()
    {
        $this->view->disable();
        if($domain = $this->user->getDomain($this->dispatcher->getParam("id"))) {
            $domain->delete();
            $this->flash->success(
                sprintf('The domain %s was successfully deleted.', $domain->domain_name));
        }
    }
}