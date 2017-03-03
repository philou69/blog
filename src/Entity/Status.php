<?php


namespace App\Entity;


class Status
{
    protected $id = 3;
    protected $status;

    function __construct($data = null)
    {
        if(is_array($data)){
            foreach ($data as $key => $value){
                $methodName = 'set'. ucfirst($key);
                if(method_exists($this, $methodName)){
                    $this->$methodName($value);
                }
            }
        }
    }

    /**
     * Get id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param mixed $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get status
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param mixed $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
}