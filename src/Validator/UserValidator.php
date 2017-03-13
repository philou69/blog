<?php

namespace App\Validator;

class UserValidator
{
    private $regexUsername;
    private $regexPassword;
    private $regexMail;

    function __construct()
    {
        $this->regexUsername = "#^[a-zA-Z0-9éèàùêâûîô_\-]{2,25}$#";
        $this->regexPassword = "#^[a-zA-Z0-9@+\-*/&]{3,25}$#";
        $this->regexMail = "/^[a-z0-9._-]+@[a-z0-9_-]{2,}\.[a-z]{2,4}$/i";
    }

    public function isUsername($username){
        // Fonction verifiant si c'est un username
        // retourne de base faux
        // mais vrai si le preg_match est bon
        if(preg_match($this->regexUsername, $username)){
            return true;
        }
        return false;
    }

    public function isPassword($password){
        // Fonction verifiant si c'est un mail
        // retourne de base faux
        // mais vrai si le preg_match est bon
        if (preg_match($this->regexPassword, $password)){
            return true;
        }
        return false;
    }

    public function isMail($mail){
        // Fonction verifiant si c'est un mail
        // retourne de base faux
        // mais vrai si le preg_match est bon
        if(preg_match($this->regexMail, $mail)){
            return true;
        }
        return false;
    }
}