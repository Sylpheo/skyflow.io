<?php

namespace Salesforce;

use skyflow\Facade;

class SalesforceFacade extends Facade
{
    public function getData()
    {
        return $this->getService('data');
    }

    public function getOAuth()
    {
        return $this->getService('oauth');
    }
}
