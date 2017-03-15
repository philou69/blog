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
            "INSERT INTO User(username, firstname, mail, password, roles) VALUES(:username,:firstname, :mail, :password, :roles)"
        );
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
            "UPDATE User SET username = :username,firstname = :firstname, mail = :mail, password = :password, roles = :roles, banish = :banish WHERE id = :id"
        );
        $q->bindValue(":username", $user->getUsername(), \PDO::PARAM_STR);
        $q->bindValue(":firstname", $user->getFirstname(), \PDO::PARAM_STR);
        $q->bindValue(":mail", $user->getMail(), \PDO::PARAM_STR);
        $q->bindValue(":password", $user->getPassword(), \PDO::PARAM_STR);
        $q->bindValue(":roles", $user->serializeRoles());
        $q->bindValue(":banish", $user->isBanish(), \PDO::PARAM_BOOL);
        $q->bindValue(":id", $user->getId(), \PDO::PARAM_INT);
        $q->execute();
    }

    public function findOneByFirstNameAndPassword($firstname, $password)
    {
        $q = $this->db->prepare(
            "SELECT id, firstname, mail, roles, banish FROM User WHERE firstname = :firstname AND  password = :password"
        );
        $q->bindValue(':firstname', $firstname, \PDO::PARAM_STR);
        $q->bindValue(':password', $password, \PDO::PARAM_STR);
        $q->execute();

        return $q->fetchObject(User::class);
    }

    public function findOneById($id)
    {
        $q = $this->db->prepare("SELECT id, username,firstname, mail, password, roles, banish FROM User WHERE id = :id");
        $q->bindValue(':id', $id, \PDO::PARAM_INT);
        $q->execute(array(":id" => $id));

        return $q->fetchObject(User::class);
    }

    public function findByFirstnameOrMail($firstname, $mail)
    {
        $q = $this->db->prepare("SELECT id, firstname FROM User WHERE firstname = :firstname OR mail = :mail");
        $q->bindValue(':firstname', $firstname, \PDO::PARAM_STR);
        $q->bindValue(':mail', $mail, \PDO::PARAM_STR);
        $q->execute();

        $data = $q->fetch(\PDO::FETCH_ASSOC);
        if (is_bool($data)) {
            return true;
        }

        return false;
    }

    public function findByFirstname($firstname)
    {
        $q = $this->db->prepare("SELECT id, firstname FROM User WHERE firstname = :firstname");
        $q->bindValue(':firstname', $firstname, \PDO::PARAM_STR);
        $q->execute();
        $data = $q->fetch(\PDO::FETCH_ASSOC);
        if (is_bool($data)) {
            return true;
        }
        return false;
    }
    public function findByMail($mail)
    {
        $q = $this->db->prepare("SELECT id, firstname FROM User WHERE mail = :mail");
        $q->bindValue(':mail', $mail, \PDO::PARAM_STR);
        $q->execute(array(":mail" => $mail));
        $data = $q->fetch(\PDO::FETCH_ASSOC);
        if (is_bool($data)) {
            return true;
        }
        return false;
    }

    public function findOneByMail($mail){
        $q = $this->db->prepare("SELECT id, username, firstname, mail, password, roles, banish FROM User WHERE mail = :mail");
        $q->bindValue(':mail', $mail, \PDO::PARAM_STR);
        $q->execute();
        if($q->rowCount() == 0){
            return false;
        }
        return $q->fetchObject(User::class);
}
    public function findAll(){
        $users = [];
        $q = $this->db->query("SELECT id, username, firstname, mail, roles, banish FROM User ");
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
        $q = $this->db->query("SELECT id, username, firstname, mail, roles, banish FROM User WHERE banish = true");
        if($q->rowCount() == 0){
            return false;
        }
        while ($user = $q->fetchObject(User::class)){
            $users[] = $user;
        }
        return $users;
    }
}