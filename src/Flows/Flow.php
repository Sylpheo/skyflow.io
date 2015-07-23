<?php
namespace skyflow\Flows;

use Silex\Application;

interface Flow {
    public function event($user,$request,Application $app);
}
