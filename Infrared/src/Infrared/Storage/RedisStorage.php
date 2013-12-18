<?php

namespace Infrared\Storage;

use Predis\Client as RedisClient;

class RedisStorage extends Storage
{
    protected $client;

    public function __construct(RedisClient $client)
    {
        $this->client = $client;
    }

    public function store(array $data, $site)
    {
        foreach($data as $click) {
            $this->storeClick($click, $site);
        }
    }

    public function storeClick($data, $domain)
    {
        // #  create unique id for new click
        $id = sprintf('click:%d', $this->client->incr('click:id'));

        $clickInfo = array( 'x' => $data->click->x,
                            'y' => $data->click->y,
                            'elapsed' => $data->elapsed,
                            'centered' =>  $data->centered);

        // open pipe line
        $pipe = $this->client->pipeline();

        // save the click itself
        $pipe->hmset($id, $clickInfo);

        $pipe->expire($id, 7 * 24 * 60 * 60);

        // add id id to page set so we can get clicks for one page
        $pipe->sadd("clicks_by_page:$domain:".$data->page, $id);

        // add id to page set so we can get clicks for one page
        $pipe->sadd("clicks_by_site:$domain", $id);

        // keep a list of pages
        $pipe->sadd("$domain:pages", $data->page);

        $pipe->execute();
    }

    public function retrieve($page, $site)
    {        
        $intersectKey = md5(microtime());
        $sortKey = md5(microtime());
        $site = urldecode($site);

        // store a temporary sorted set, which is a copy of clicks_by_date
        // intersected with the appropriate clicks_by_page:x sets.
        $this->client->zinterstore($intersectKey, 2,
            "clicks_by_page:$site:$page",
            "clicks_by_site:$site"
        );

        $ids = $this->client->sort($intersectKey, array('by' => '*->elapsed'));

        // apparently this is fine http://blog.jmoz.co.uk/python-redis-py-pipeline
        $clicks = array();
        foreach($ids as $id) {
            if($click = $this->client->hgetall($id)) {
                $clicks[] = $this->client->hgetall($id);
            }
        }
        return $clicks;
    }
}
