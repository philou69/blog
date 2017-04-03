<?php

namespace App\Validator;

class UserValidator
{
    private $regexUsername;
    private $regexPassword;
    private $regexMail;

    function __construct()
    {

        $this->regexUsername = "#^[a-zA-Zéèàùêâûîôç-]{2,25}$#";
        $this->regexPassword = "#^[\wéèêùàîôûç@+*&-]{3,25}$#";
        $this->regexMail = "/^[\w\.-]+@[a-zA-Z]{2,}\.[a-zA-Z]{2,4}$/";
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