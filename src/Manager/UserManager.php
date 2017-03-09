<?php


namespace App\Manager;


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
        $q = $this->db->prepare(
            "INSERT INTO User(username, mail, password, roles) VALUES(:username, :mail, :password, :roles)"
        );
        $q->execute(
            array(
                ":username" => $user->getUsername(),
                ":mail" => $user->getMail(),
                ":password" => $user->getPassword(),
                ":roles" => $user->serializeRoles(),
            )
        );

        $q = $this->db->query("SELECT LAST_INSERT_ID() FROM User");
        $donnees =$q->fetch();
        $user->setId($donnees[0]);
        return $user;
    }

    public function update(User $user)
    {
        $q = $this->db->prepare(
            "UPDATE User SET username = :username, mail = :mail, password = :password, roles = :roles, banish = :banish WHERE id = :id"
        );
        $q->execute(
            array(
                ":username" => $user->getUsername(),
                ":mail" => $user->getMail(),
                ":password" => $user->getPassword(),
                ":roles" => $user->serializeRoles(),
                ":banish" =>$user->getBanish(),
                ":id" => $user->getId(),
            )
        );
    }

    public function getOne($id)
    {
        $q = $this->db->prepare("SELECT id, username, mail, roles FROM User WHERE id = :id");
        $q->execute(array(":id" => $id));
        $donnees = $q->fetch(\PDO::FETCH_ASSOC);

        return new User($donnees);
    }

    public function getAll()
    {
        $users = [];
        $q = $this->db->query("SELECT id, username, mail, roles FROM User ORDER BY username");
        while ($donnees = $q->fetch(\PDO::FETCH_ASSOC)) {
            $users[] = new User($donnees);
        }

        return $users;
    }

    public function findOneByUserName($username)
    {
        $q = $this->db->prepare(
            "SELECT id, username, mail, password, roles FROM User WHERE username = :username"
        );
        $q->execute(
            array(
                ":username" => $username
            )
        );
        $donnees = $q->fetch(\PDO::FETCH_ASSOC);

        return new User($donnees);
    }
    public function findOneByUserNameAndPassword($username, $password)
    {
        $q = $this->db->prepare(
            "SELECT id, username, mail, roles FROM User WHERE username = :username AND  password = :password"
        );
        $q->execute(
            array(
                ":username" => $username,
                ":password" => $password,
            )
        );
        $donnees = $q->fetch(\PDO::FETCH_ASSOC);

        return new User($donnees);
    }

    public function findOneById($id)
    {
        $q = $this->db->prepare("SELECT id, username, mail, password, roles, banish FROM User WHERE id = :id");
        $q->execute(array(":id" => $id));
        $donnees = $q->fetch(\PDO::FETCH_ASSOC);

        return new User($donnees);
    }

    public function findByUsernameOrMail($username, $mail)
    {
        $q = $this->db->prepare("SELECT id, username FROM User WHERE username = :username OR mail = :mail");
        $q->execute(
            array(
                ":username" => $username,
                ":mail" => $mail,
            )
        );
        $donnees = $q->fetch(\PDO::FETCH_ASSOC);
        if (is_bool($donnees)) {
            return true;
        }

        return false;
    }

    public function findByUsername($username)
    {
        $q = $this->db->prepare("SELECT id, username FROM User WHERE username = :username");
        $q->execute(array(":username" => $username));
        $donnees = $q->fetch(\PDO::FETCH_ASSOC);
        if (is_bool($donnees)) {
            return true;
        }
        return false;
    }
    public function findByMail($mail)
    {
        $q = $this->db->prepare("SELECT id, username FROM User WHERE mail = :mail");
        $q->execute(array(":mail" => $mail));
        $donnees = $q->fetch(\PDO::FETCH_ASSOC);
        if (is_bool($donnees)) {
            return true;
        }
        return false;
    }

    public function getByMail($mail){
        $q = $this->db->prepare("SELECT id, username, firstname, mail, password, roles, banish FROM User WHERE mail = :mail");
        $q->execute(array(":mail" => $mail));
        $data = $q->fetch(\PDO::FETCH_ASSOC);
        if (is_bool($data)) {
            return false;
        }
        return new User($data);
}
    public function findAll(){
        $users = [];
        $q = $this->db->query("SELECT id, username, firstname, mail, roles, banish FROM User ");
        if($q->rowCount() == 0){
            return false;
        }
        while ($data = $q->fetch(\PDO::FETCH_ASSOC)){
            $users[] = new User($data);
        }
        return $users;
    }

    public function findAllBanish(){
        $users = [];
        $q = $this->db->query("SELECT id, username, firstname, mail, roles, banish FROM User WHERE banish = true");
        if($q->rowCount() == 0){
            return false;
        }
        while ($data = $q->fetch(\PDO::FETCH_ASSOC)){
            $users[] = new User($data);
        }
        return $users;
    }
}