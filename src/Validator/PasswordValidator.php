<?php


namespace App\Validator;


class PasswordValidator
{
    private $regex;

    function __construct()
    {
        $this->regex = "#^[a-zA-Z0-9@+\-*/&]{3,25}$#";
    }

    public function isPassword($password){
        if(preg_match($this->regex, $password)){
            return true;
        }
        return false;
    }
}