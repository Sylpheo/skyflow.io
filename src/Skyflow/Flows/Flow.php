<?php
namespace Skyflow\Flows;

use Silex\Application;

interface Flow {
    public function event($requestJson);
}
