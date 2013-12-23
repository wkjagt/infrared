<?php

class ApiController extends \Phalcon\Mvc\Controller
{
    protected $domain;

    public function initialize()
    {        
        if($origin = $this->request->getHeader('ORIGIN')) {
            $domainName = parse_url($origin, PHP_URL_HOST);

            // if($routes[0]->getParam('domain') !== $domainName) {
            //     $app->halt(400);
            // }
            // return;

            // if($cache = $app->cache->hgetall('domains:'.$domainName)) {
                // $domain = ORM::for_table('domains')->hydrate($cache);
            // } else {
                // $domain = Domain::->where('domain_name', $domainName)->find_one();
                $domain = Domain::query()->where("domain_name = :domain_name:")
                            ->bind(array("domain_name" => $domainName))
                            ->execute()->getFirst();

            //     $app->cache->pipeline(function($pipe) use($domain) {
            //         $pipe->hmset('domains:'.$domain->domain_name, $domain->as_array());
            //         $pipe->expire('domains:'.$domain->domain_name, 300);
            //     });
            // }

            if(!$domain) {
                $this->response->setStatusCode(403);
            }

            $this->response->setHeader('Access-Control-Allow-Origin', $origin);
            $this->response->setHeader('Access-Control-Allow-Headers', 'Content-type');
            $this->domain = $domain;
        }
    }

    public function preflightAction(){}

    public function recordClicksAction()
    {
        $postedClicks = $this->request->getJsonRawBody();

        foreach($postedClicks as $click) {

            $click->page = $this->domain->doReplacements($click->page);
            $this->clickStorage->storeClick($click, $this->domain->domain_name);
        }
    }
}