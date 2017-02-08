<?php


namespace App\Entity;


class Commentaire
{
    private $id;
    private $user;
    private $commentaire;
    private $chapitre;
    private $commentaireParent = null;
    private $parent = false;
    private $signaled = false;
    private $banished = false;
    private $created_at;
    private $commentaires = null;

    public function __construct(array $donnees = null){
        foreach ($donnees as $key => $value){
            $method = 'set'.ucfirst($key);
            if(method_exists($this, $method)){
                $this->$method($value);
            }
        }
    }
    public function hydrate(array $donnees){
        foreach ($donnees as $key => $value){
            $method = 'set'.ucfirst($key);
            if(method_exists($this, $method)){
                $this->$method($value);
            }
        }
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * @param mixed $commentaire
     */
    public function setCommentaire($commentaire)
    {
        if(is_string($commentaire)){
            $this->commentaire = $commentaire;
        }
    }

    /**
     * @return mixed
     */
    public function getChapitre()
    {
        return $this->chapitre;
    }

    /**
     * @param mixed $chapitre
     */
    public function setChapitre(Chapitre $chapitre)
    {
        $this->chapitre = $chapitre;
    }

    /**
     * @return null
     */
    public function getCommentaireParent()
    {
        return $this->commentaireParent;
    }

    /**
     * @param null $commentireParent
     */
    public function setCommentaireParent(Commentaire $commentaireParent)
    {
        $this->commentaireParent = $commentaireParent;
    }

    /**
     * @return bool
     */
    public function isSignaled()
    {
        return $this->signaled;
    }

    /**
     * @param bool $signaled
     */
    public function setSignaled($signaled)
    {
        if(is_bool($signaled)){
            $this->signaled = $signaled;
        }
    }

    /**
     * @return bool
     */
    public function isBanished()
    {
        return $this->banished;
    }

    /**
     * @param bool $banished
     */
    public function setBanished($banished)
    {
        if(is_bool($banished)){
            $this->banished = $banished;
        }
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt(\DateTime $created_at)
    {
        $this->created_at = $created_at;
    }

        /**
     * @return mixed
     */
    public function getCommentaires()
    {
        return $this->commentaires;
    }/**
     * @param array $commentaires
     */
    public function setCommentaires( $commentaires)
    {
        $this->commentaires = $commentaires;
    }

    public function addCommentaire(Commentaire $commentaire){
        $this->commentaires[] = $commentaire;
    }

    /**
     * @return bool
     */
    public function isParent()
    {
        return $this->parent;
    }/**
     * @param bool $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }


}