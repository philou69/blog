<?php


namespace App\Entity;


class User
{
    private $id;
    private $username;
    private $firstname;
    private $mail;
    private $password;
    private $banish = false;
    private $roles = [];

    function __construct()
    {
        if(is_string($this->roles)){
            $this->setRoles(unserialize($this->roles));
        }
        if($this->banish == 0){
            $this->banish = false;
        }else{
            $this->banish = true;
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
     *
     * @return $this
     */
    public function setUsername($username)
    {
        if (is_string($username)) {
            $this->username = $username;
        }

        return $this;
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
     *
     * @return $this
     */
    public function setMail($mail)
    {
        if (is_string($mail)) {
            $this->mail = $mail;
        }

        return $this;
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
     *
     * @return $this
     */
    public function setPassword($password)
    {
        if (is_string($password)) {
            $this->password = $password;
        }

        return $this;
    }


    /**
     * @return array
     */
    public function getRoles()
    {
        if(!is_array($this->roles)){
            $roles = unserialize($this->roles);
        }else{
            $roles = $this->roles;
        }
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
     *
     * @return $this
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
        return $this;
    }

    public function serializeRoles()
    {
        if(is_array($this->roles)){
            return serialize($this->roles);
        }else{
            return $this->roles;
        }
    }

    /**
     * Get firstname
     *
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set firstname
     *
     * @param mixed $firstname
     *
     * @return $this
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get banish
     *
     * @return mixed
     */
    public function isBanish()
    {
        return $this->banish;
    }

    /**
     * Set banish
     *
     * @param mixed $banish
     *
     * @return $this
     */
    public function setBanish($banish)
    {
        $this->banish = $banish;

        return $this;
    }


}