<?php


namespace App\Entity;


class Chapter
{
    private $id;
    private $title;
    private $chapter;
    private $publishedAt;
    private $published;

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
    public function getChapter()
    {
        return $this->chapter;
    }

    /**
     * @param mixed $chapter
     * @return $this
     */
    public function setChapter($chapter)
    {
        if (is_string($chapter)) {
            $this->chapter = $chapter;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @param mixed $publishedAt
     * @return $this
     */
    public function setPublishedAt($publishedAt)
    {
        if(is_string($publishedAt)){
            $this->publishedAt = new \DateTime($publishedAt);
        }
        elseif (is_a($publishedAt, 'DateTime')){
            $this->publishedAt = $publishedAt;
        }
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