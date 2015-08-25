<?php
namespace skyflow\Service;
/**
 * Created by PhpStorm.
 * User: Elodie
 * Date: 15/07/2015
 * Time: 11:33
 */
class GenerateToken{
    public function generateToken(){

        $skyflowtoken =  md5(uniqid(rand(), true));
        return $skyflowtoken;

    }
}
