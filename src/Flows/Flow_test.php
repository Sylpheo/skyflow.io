<?php

namespace skyflow\Flows;

use skyflow\Flows\Flow;
use Symfony\Component\HttpFoundation\Request;

class Flow_test implements Flow{

    public function event($user,$request){
        if ($request->has('email')) {
            $email = $request->get('email');

            return $email;
        }
       else{
           return "error";
       }
    }

}

