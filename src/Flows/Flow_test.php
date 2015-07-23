<?php

namespace skyflow\Flows;

use skyflow\Flows\Flow;

class Flow_test implements Flow{

    public function event($jsonData){
        return $jsonData;
    }

}

