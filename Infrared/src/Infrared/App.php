<?php

namespace Infrared;

use WebGlue;
use Infrared\Storage\RedisStorage;

class App
{
    protected static $config;

    protected static $domain = '';

    protected static $storage;

    /**
     * initialize the Infrared application
     */
    public static function init(WebGlue $webglue, $config)
    {
        // config
        static::$config = $config;

        $redisClient = new \Predis\Client( (array) static::$config->storage->redis->parameters );
        static::$storage = new RedisStorage($redisClient);

        // before & after
        $webglue->before(array(__CLASS__, 'before'));
        $webglue->after(array(__CLASS__, 'after'));

        // get clicks for a domain
        $webglue->get('/domains/{domain:string}/clicks', array(__CLASS__, 'getClicks'), 'api', 'json');
        $webglue->post('/clicks', array(__CLASS__, 'registerClicks'), 'js', 'json');

        return $webglue;
    }

    /**
     * Before all request
     */
    public static function before($webglue, $request, $response)
    {
        $response->headers->set('Cache-Control', 'no-cache');

        if(in_array('api', $webglue['_route']->tags)) {
            $apiKey = $request->query->get('apikey');
            for($i = 0; $i < count(static::$config->domains); $i++) {
                $domain = static::$config->domains[$i];

                if($domain->domain == $request->attributes->get('domain') &&
                    $domain->apikey != $request->query->get('apikey')) {

                    $response->setStatusCode(400)
                             ->setContent(json_encode(array('error' => 'INVALID_API_KEY')))
                             ->send();
                    exit();
                }
            }
        }


        if(in_array('js', $webglue['_route']->tags)) {
            $origin = $request->headers->get('origin');
            for($i = 0; $i < count(static::$config->domains); $i++) {
                $domain = static::$config->domains[$i];

                if($domain->domain == parse_url($origin, PHP_URL_HOST)) {
                    static::$domain = $domain;
                    $response->headers->set('Access-Control-Allow-Origin', $origin);
                    return;
                }
            }
            exit;
        }
    }

    /**
     * After all request
     */
    public static function after($webglue, $request, $response)
    {
        if(in_array('json', $webglue['_route']->tags)) {
            $response->headers->set('Content-Type', 'application/json');
        }
    }

    /**
     * Get get a json of clicks for a page
     */
    public static function getClicks($webglue, $request, $response)
    {
        $domain = $request->attributes->get('domain');

        // validate required parameters
        if(!$page = $request->query->get('page')) {
            $response->setStatusCode(400)->send();
            exit;
        }
        $clicks = static::$storage->retrieve($page, $domain);
        $response->setContent(json_encode($clicks));
    }

    public static function registerClicks($webglue, $request, $response)
    {
        $toStore = array();
        $patterns = (array) static::$domain->url_patterns;

        foreach($request->request->get('clicks') as $click) {

            // apply patterns
            $click['page'] = preg_replace(
                array_keys($patterns),
                array_values($patterns),
                $click['page']
            );
            $toStore[] = $click;
        }
        static::$storage->store($toStore, static::$domain->domain);
    }
}
