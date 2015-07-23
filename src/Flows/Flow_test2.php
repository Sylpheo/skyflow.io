<?php

namespace skyflow\Flows;
/**
 * Created by PhpStorm.
 * User: Elodie
 * Date: 23/07/2015
 * Time: 11:29
 */
class Flow_test2 implements Flow{
    public function event($user,$request){
        if($request->has('nom')){
            $nom = $request->get('nom');
        return $nom;
        }else{
            return 'pas de nom';
        }
    }
}