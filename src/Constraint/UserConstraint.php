<?php


namespace App\Constraint;


use App\Entity\User;
use App\Manager\UserManager;

class UserConstraint
{
    private $user;
    private $userManager;

    function __construct(User $user = null)
    {
        $this->user = $user;
        $this->userManager = new UserManager();
    }

    public function isNotOtherUser()
    {

        // On vérifie s'il existe un user avec le même nom ou le même mail
        if ($this->userManager->findByFirstnameOrMail($this->user->getFirstname(), $this->user->getMail())) {
            return true;
        }

        return false;
    }

    public function isNotOtherFirstName($username)
    {
        // On regard si la réponse est un boolean
        if($this->userManager->findByFirstname($username)){
             return true;
         }
         return false;
    }

    public function isNotOtherMail($mail)
    {
        if ($this->userManager->findByMail($mail)) {
            return true;
        }

        return false;
    }
}