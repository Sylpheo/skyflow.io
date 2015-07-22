<?php
namespace skyflow\Service;

interface Flow {

    /**
     * @param $jsonData
     * @return mixed
     */
    public function event($jsonData);
}