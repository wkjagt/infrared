<?php

class ApiController extends \Phalcon\Mvc\Controller
{
    protected $domain;

    protected $user;

    public function initialize()
    {
        $routeName = $this->router->getMatchedRoute()->getName();

        switch($routeName) {
            case 'preflight':
            case 'record_clicks':
                $this->checkCrossDomain();
                break;
            case 'get_clicks':
                $this->checkApiKey();
        }
    }

    protected function checkApiKey()
    {
        $apiKey = $this->request->get('apikey');

        $query = $this->modelsManager->createQuery(
            "SELECT * FROM User AS u JOIN Domain AS d WHERE u.api_key = :api_key: AND d.domain_name = :domain_name:");

        $this->user = $query->execute(array(
            'api_key' => $apiKey,
            'domain_name' => $this->dispatcher->getParam('domain')
        ))->getFirst();

        if(!$this->user) {
            $this->response->setStatusCode(400, '')
                ->setContent(json_encode(array('error' => 'INVALID_APIKEY')))
                ->send();
            exit();
        }
    }

    protected function checkCrossDomain()
    {
        if(!$origin = $this->request->getHeader('ORIGIN')) {
            $this->response->setStatusCode(400, '')->send();
            exit;
        }

        $domainName = parse_url($origin, PHP_URL_HOST);

        if($cache = phpiredis_command_bs($this->cache, array('GET', "domains:$domainName"))) {
            $domain = new Domain;
            $domain->unserialize($cache);
            echo "from cache";
        } else {
            $domain = Domain::query()->where("domain_name = :domain_name:")
                        ->bind(array("domain_name" => $domainName))
                        ->execute()->getFirst();

            phpiredis_multi_command_bs($this->cache, array(
                array('SET', "domains:$domainName", $domain->serialize()),
                array('EXPIRE', "domains:$domainName", 5 * 60)
            ));            
        }

        if(!$domain) {
            $this->response->setStatusCode(403);
        }

        $this->response->setHeader('Access-Control-Allow-Origin', $origin);
        $this->response->setHeader('Access-Control-Allow-Headers', 'Content-type');
        $this->domain = $domain;
    }

    public function preflightAction(){}

    public function recordClicksAction()
    {
        $this->view->disable();

        $postedClicks = $this->request->getJsonRawBody();

        foreach($postedClicks as $click) {

            $click->page = $this->domain->doReplacements($click->page);
            $this->clickStorage->storeClick($click, $this->domain->domain_name);
        }
    }

    public function getClicksAction()
    {
        if(!$page = $this->request->get('page')) {
            $this->response->setStatusCode(400);
            return;
        }
        $domainName = $this->dispatcher->getParam("domain");

        $domain = Domain::query()
                    ->where("domain_name = :domain_name:")
                    ->andWhere("user_id = :user_id:")
                    ->bind(array("domain_name" => $domainName,
                                 "user_id" => $this->session->get('auth')['user_id']))
                    ->execute()->getFirst();

        $page = $domain->doReplacements($page);
        $clicks = $this->clickStorage->retrieve($page, $domainName);
        $this->response->setHeader('Content-Type', 'application/json');

        echo json_encode($clicks);
    }
}