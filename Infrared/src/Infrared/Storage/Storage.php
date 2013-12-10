<?php

namespace Infrared\Storage;


abstract class Storage
{
    abstract public function store(array $data, $site);

    abstract public function retrieve($url, $site);

    protected function getPath(ParameterBag $request)
    {

    }

    protected function getSite(ParameterBag $request)
    {

    }
}
