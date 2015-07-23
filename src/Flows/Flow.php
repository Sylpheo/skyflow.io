<?php
namespace skyflow\Flows;

interface Flow {
    public function event($user,$request);
}
