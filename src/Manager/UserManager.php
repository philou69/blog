<?php


namespace App\Manager;


use App\Entity\Chapter;
use App\Entity\PDO;
use App\Entity\User;

class UserManager
{
    private $db;

    function __construct()
    {
        $this->db = PDO::get();
    }

    public function create(User $user)
    {
        // Enregistrement de l'user
        $q = $this->db->prepare(
            "INSERT INTO User(pseudo, username, firstname, mail, password, roles) VALUES(:pseudo, :username,:firstname, :mail, :password, :roles)"
        );
        $q->bindValue(':pseudo', $user->getPseudo(), \PDO::PARAM_STR);
        $q->bindValue(":username", $user->getUsername(), \PDO::PARAM_STR);
        $q->bindValue(":firstname", $user->getFirstname(), \PDO::PARAM_STR);
        $q->bindValue(":mail", $user->getMail(), \PDO::PARAM_STR);
        $q->bindValue(":password", $user->getPassword(), \PDO::PARAM_STR);
        $q->bindValue(":roles", $user->serializeRoles());
        $q->execute();
        // recuperation du dernier id
        $user->setId($this->db->lastInsertId());
        return $user;
    }

    public function update(User $user)
    {
        $q = $this->db->prepare(
            "UPDATE User SET pseudo = :pseudo username = :username,firstname = :firstname, mail = :mail, password = :password, roles = :roles, banish = :banish WHERE id = :id"
        );
        $q->bindValue(":pseudo", $user->getPseudo(), \PDO::PARAM_STR);
        $q->bindValue(":username", $user->getUsername(), \PDO::PARAM_STR);
        $q->bindValue(":firstname", $user->getFirstname(), \PDO::PARAM_STR);
        $q->bindValue(":mail", $user->getMail(), \PDO::PARAM_STR);
        $q->bindValue(":password", $user->getPassword(), \PDO::PARAM_STR);
        $q->bindValue(":roles", $user->serializeRoles());
        $q->bindValue(":banish", $user->isBanish(), \PDO::PARAM_BOOL);
        $q->bindValue(":id", $user->getId(), \PDO::PARAM_INT);
        $q->execute();
    }

    public function findOneByPseudoAndPassword($pseudo, $password)
    {
        $q = $this->db->prepare(
            "SELECT id, pseudo, firstname, mail, roles, banish FROM User WHERE pseudo = :pseudo AND  password = :password"
        );
        $q->bindValue(':pseudo', $pseudo, \PDO::PARAM_STR);
        $q->bindValue(':password', $password, \PDO::PARAM_STR);
        $q->execute();

        return $q->fetchObject(User::class);
    }

    public function findOneById($id)
    {
        $q = $this->db->prepare("SELECT id, pseudo,  username,firstname, mail, password, roles, banish FROM User WHERE id = :id");
        $q->bindValue(':id', $id, \PDO::PARAM_INT);
        $q->execute(array(":id" => $id));

        return $q->fetchObject(User::class);
    }

    public function findByPseudoOrMail($pseudo, $mail)
    {
        $q = $this->db->prepare("SELECT id, pseudo FROM User WHERE pseudo = :pseudo OR mail = :mail");
        $q->bindValue(':pseudo', $pseudo, \PDO::PARAM_STR);
        $q->bindValue(':mail', $mail, \PDO::PARAM_STR);
        $q->execute();

        $data = $q->fetch(\PDO::FETCH_ASSOC);
        if (is_bool($data)) {
            return true;
        }

        return false;
    }

    public function findByPseudo($pseudo)
    {
        $q = $this->db->prepare("SELECT id, pseudo FROM User WHERE pseudo = :pseudo");
        $q->bindValue(':pseudo', $pseudo, \PDO::PARAM_STR);
        $q->execute();
        $data = $q->fetch(\PDO::FETCH_ASSOC);
        if (is_bool($data)) {
            return true;
        }
        return false;
    }
    public function findByMail($mail)
    {
        $q = $this->db->prepare("SELECT id, pseudo FROM User WHERE mail = :mail");
        $q->bindValue(':mail', $mail, \PDO::PARAM_STR);
        $q->execute();
        $data = $q->fetch(\PDO::FETCH_ASSOC);
        if (is_bool($data)) {
            return true;
        }
        return false;
    }

    public function findOneByMail($mail){
        $q = $this->db->prepare("SELECT id, pseudo, username, firstname, mail, password, roles, banish FROM User WHERE mail = :mail");
        $q->bindValue(':mail', $mail, \PDO::PARAM_STR);
        $q->execute();
        if($q->rowCount() == 0){
            return false;
        }
        return $q->fetchObject(User::class);
}
    public function findAll(){
        $users = [];
        $q = $this->db->query("SELECT id,pseudo, username, firstname, mail, roles, banish FROM User ");
        if($q->rowCount() == 0){
            return false;
        }
        while ($user = $q->fetchObject(User::class)){
            $users[] = $user;
        }
        return $users;
    }

    public function findAllBanish(){
        $users = [];
        $q = $this->db->query("SELECT id,pseudo, username, firstname, mail, roles, banish FROM User WHERE banish = true");
        if($q->rowCount() == 0){
            return false;
        }
        while ($user = $q->fetchObject(User::class)){
            $users[] = $user;
        }
        return $users;
    }
}