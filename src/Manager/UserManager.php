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
                ":roles" => $user->serializeRoles()
            )
        );
    }

    public function update(User $user)
    {
        $q = $this->db->prepare(
            "UPDATE User SET username = :username, mail = :mail, password = :password, roles = :roles WHERE id = :id"
        );
        $q->execute(
            array(
                ":username" => $user->getUsername(),
                ":mail" => $user->getMail(),
                ":passwprd" => $user->getPassword(),
                ":roles" => $user->serializeRoles(),
                ":id" => $user->getId()
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

    public function findOneByUserName($username, $password){
        $q = $this->db->prepare("SELECT id, username, mail, roles FROM User WHERE username = :username AND  password = :password");
        $q->execute(array(":username" => $username,
            ":password" => $password));
        $donnees = $q->fetch(\PDO::FETCH_ASSOC);

        return new User($donnees);
    }

    public function findOneById($id){
        $q = $this->db->prepare("SELECT id, username FROM User WHERE id = :id");
        $q->execute(array(":id" => $id));
        $donnees = $q->fetch(\PDO::FETCH_ASSOC);
        return new User($donnees);
    }
}