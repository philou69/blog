<?php


namespace App\Validator;


class ChapitreValidator
{
    private $regexTitle;
    private $regexDate;

    function __construct()
    {
        $this->regexTitle = "#^[a-zA-Z0-9éèêà-.,:]{3,}$#";
        $this->regexDate = "#^20[0-9]{2}-[01][0-9]-[0123][0-9]$#";
    }

    public function isTitle($title){
        return true;
        if(preg_match($this->regexTitle, $title)){
            return true;
        }
        return false;
    }
    public function isChapitre($chapitre){
        return true;
    }

    public function isDate($date){
        if(preg_match($this->regexDate, $date)){
            return true;
        }
        return false;
    }

    public function isPublished($published){

        if($published == "true" || $published == "false"){
            return true;
        }
        return false;
    }
}