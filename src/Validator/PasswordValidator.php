<?php


namespace App\Validator;


class PasswordValidator
{
    private $regex;

    function __construct()
    {
        $this->regex = "#^[a-z0-9._-]+@[a-z0-9_-]{2,}\.[a-z]{2,4}$#";
    }

    public function isPassword($password){
        if(preg_match($this->regex, $password)){
            return true;
        }
        return false;
    }
}