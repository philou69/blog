<?php


namespace App\Validator;


class UsernameValidator
{
    private $regex;

    function __construct()
    {
        $this->regex = "#^[a-zA-Z0-9éèàùêâûîô_\-]{2,25}$#";
    }

    public function isUsername($username){
        if(preg_match($this->regex, $username)){
            return true;
        }
        return false;
    }

}