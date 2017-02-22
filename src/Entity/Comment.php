<?php


namespace App\Entity;


use App\Manager\UserManager;

class Comment
{
    private $id;
    private $user;
    private $comment;
    private $chapter;
    private $commentParent = null;
    private $signaled = false;
    private $banished = false;
    private $created_at;
    private $comments = null;
    private $place = 1;
    private $signaledBy;
    private $signaledAt;
    private $banishedBy;
    private $banishedAt;


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
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     * @return $this
     */
    public function setComment($comment)
    {
        if (is_string($comment)) {
            $this->comment = $comment;
        }

        return $this;
    }

    /**
     * @return Chapter
     */
    public function getChapter()
    {
        return $this->chapter;
    }

    /**
     * @param Chapter $chapter
     * @return $this
     */
    public function setChapter(Chapter $chapter)
    {
        $this->chapter = $chapter;

        return $this;
    }

    /**
     * @return null
     */
    public function getCommentParent()
    {
        return $this->commentParent;
    }

    /**
     * @param Comment $commentParent
     * @return $this
     */
    public function setCommentParent(Comment $commentParent)
    {
        $this->commentParent = $commentParent;

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
        $this->signaled = filter_var($signaled, FILTER_VALIDATE_BOOLEAN);

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
        $this->banished = filter_var($banished, FILTER_VALIDATE_BOOLEAN);

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
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param array $comments
     * @return $this
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;

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

    /**
     * @return User
     */
    public function getSignaledBy()
    {
        return $this->signaledBy;
    }

    /**
     * @param User  $user
     * @return $this
     */
    public function setSignaledBy(User $user = null)
    {
        $this->signaledBy = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSignaledAt()
    {
        return $this->signaledAt;
    }

    /**
     * @param mixed $signaledAt
     * @return $this
     */
    public function setSignaledAt($signaledAt = null)
    {
            $this->signaledAt = $signaledAt;
        return $this;
    }

    /**
     * @return User
     */
    public function getBanishedBy()
    {
        return $this->banishedBy;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setBanishedBy(User $user = null)
    {
        $this->banishedBy = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBanishedAt()
    {
        return $this->banishedAt;
    }

    /**
     * @param mixed $banishedAt
     */
    public function setBanishedAt($banishedAt = null)
    {
        $this->banishedAt = $banishedAt;
    }


}