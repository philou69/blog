<?php


namespace App\Entity;


class User
{
    private $id;
    private $username;
    private $mail;
    private $password;
    private $roles = [];

    function __construct(array $data = null)
    {
        if (isset($data)) {
            foreach ($data as $key => $value) {
                $method = 'set'.ucfirst($key);
                if (method_exists($this, $method)) {
                    $this->$method($value);
                }
            }
        }
    }

    public function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set'.ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {

        $this->id = intval($id);
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        if (is_string($username)) {
            $this->username = $username;
        }
    }

    /**
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param mixed $mail
     */
    public function setMail($mail)
    {
        if (is_string($mail)) {
            $this->mail = $mail;
        }
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        if (is_string($password)) {
            $this->password = $password;
        }
    }


    /**
     * @return array
     */
    public function getRoles()
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    /**
     * @param $role
     */
    public function addRoles($role)
    {
        if (is_string($role)) {
            $role = strtoupper($role);
            if (!in_array($role, $this->roles, true)) {
                $this->roles[] = $role;
            }
        }
    }

    /**
     * @param array $roles
     */
    public function setRoles($roles)
    {
        if (is_array($roles)) {
            $this->roles = $roles;
        } else {
            if (is_string($roles)) {
                $this->roles = unserialize($roles);
            }
        }
    }

    public function serializeRoles()
    {
        return serialize($this->roles);
    }

}