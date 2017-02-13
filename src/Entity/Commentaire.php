<?php


namespace App\Entity;


class Commentaire
{
    private $id;
    private $user;
    private $commentaire;
    private $chapitre;
    private $commentaireParent = null;
    private $signaled = false;
    private $banished = false;
    private $created_at;
    private $commentaires = null;
    private $place = 1;

    public function __construct(array $data = null)
    {
        $this->setCreatedAt(new \DateTime());
        if(is_array($data)){
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
     * @return $this
     */
    public function setId($id)
    {
        $this->id = intval($id);

        return $this;
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
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
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
     * @return $this
     */
    public function setCommentaire($commentaire)
    {
        if (is_string($commentaire)) {
            $this->commentaire = $commentaire;
        }

        return $this;
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
     * @return $this
     */
    public function setChapitre(Chapitre $chapitre)
    {
        $this->chapitre = $chapitre;

        return $this;
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
     * @return $this
     */
    public function setCommentaireParent(Commentaire $commentaireParent)
    {
        $this->commentaireParent = $commentaireParent;

        return $this;
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
     * @return $this
     */
    public function setSignaled($signaled)
    {
        $this->signaled = $signaled;

        return $this;

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
     * @return $this
     */
    public function setBanished($banished)
    {
        $this->banished = $banished;

        return $this;

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
     * @return $this
     */
    public function setCreatedAt(\DateTime $created_at)
    {
        $this->created_at = $created_at->format('Y-m-d H:i:s');

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCommentaires()
    {
        return $this->commentaires;
    }

    /**
     * @param array $commentaires
     * @return $this
     */
    public function setCommentaires($commentaires)
    {
        $this->commentaires = $commentaires;

        return $this;
    }

    public function addCommentaire(Commentaire $commentaire)
    {
        $this->commentaires[] = $commentaire;

        return $this;
    }

    /**
     * @return bool
     */
    public function isParent()
    {
        return $this->parent;
    }

    /**
     * @param bool $parent
     * @return $this
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return bool
     */
    public function isLastChild()
    {
        return $this->lastChild;
    }

    /**
     * @param bool $lastChild
     * @return $this
     */
    public function setLastChild($lastChild)
    {
        $this->lastChild = $lastChild;

        return $this;
    }

    /**
     * @return int
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * @param int $place
     * @return $this
     */
    public function setPlace($place)
    {
        $this->place = intval($place);
        return $this;
    }


}