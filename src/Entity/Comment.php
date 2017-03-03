<?php
/*
 * PDO::FETCH_OBJECT
 * createdAt
 *
 */

namespace App\Entity;


use App\Manager\UserManager;

class Comment
{
    private $id;
    private $user;
    private $comment;
    private $chapter;
    private $commentParent = null;
    private $createdAt;
    private $comments = null;
    private $place = 1;
    private $statuseddBy;
    private $statusedAt;
    private $status;


    public function __construct(array $data = null)
    {
        $this->status = new Status();
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $method = 'set'.ucfirst($key);
                if (method_exists($this, $method)) {
                    $this->$method($value);
                }
            }
        }
        if ($this->createdAt == null) {
            $this->createdAt = new \DateTime();
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
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        if ($createdAt == null) {
            $this->createdAt = $createdAt;
        } elseif (is_string($createdAt)) {
            $this->createdAt = new \DateTime($createdAt);
        } elseif (is_a($createdAt, 'DateTime')) {
            $this->createdAt = $createdAt;
        }

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
    public function getStatusedBy()
    {
        return $this->statuseddBy;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setStatusedBy(User $user = null)
    {
        $this->statuseddBy = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatusedAt()
    {
        return $this->statusedAt;
    }

    /**
     * @param mixed $signaledAt
     * @return $this
     */
    public function setStatusedAt($statusedAt = null)
    {
        if ($statusedAt == null) {
            $this->statusedAt = null;
        } elseif (is_string($statusedAt)) {
            $this->statusedAt = new \DateTime($statusedAt);
        } elseif (is_a($statusedAt, 'DateTime')) {
            $this->statusedAt = $statusedAt;
        }

        return $this;
    }


    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(Status $status)
    {
        $this->status = $status;

        return $this;
    }

}