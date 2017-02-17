<?php


namespace App\Entity;


class Chapitre
{
    private $id;
    private $title;
    private $chapitre;
    private $published_at;
    private $published;

    public function __construct(array $data = null)
    {
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return $this
     */
    public function setTitle($title)
    {
        if (is_string($title)) {
            $this->title = $title;
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
    public function setChapitre($chapitre)
    {
        if (is_string($chapitre)) {
            $this->chapitre = $chapitre;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPublished_at()
    {
        return $this->published_at;
    }

    /**
     * @param mixed $published_at
     * @return $this
     */
    public function setPublished_at($published_at)
    {
        $this->published_at = $published_at;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * @param bool $published
     * @return $this
     */
    public function setPublished($published)
    {
            $this->published = $published;
            return $this;
    }


}