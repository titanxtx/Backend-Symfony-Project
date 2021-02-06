<?php
namespace App\Entity;
//Used for getting all the users out there or getting one users information all in one go
//use Doctrine\ORM\Mapp as ORM;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class user{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer",options={"unsigned":true})
     */

    private $id;
     /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;
    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default":0})
     */
    private $active_status;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_date;
    /**
     * @ORM\Column(type="datetime")
     */
    private $created_date;

    function __construct($id,$name,$active_status,$updated_date,$created_date)
    {
        $this->id=$id;
        $this->name=$name;
        $this->active_status=$active_status;
        $this->updated_date=$updated_date;
        $this->created_date=$created_date;
    }

    public function getId()
    {
        return $this->id;
    }
    public function getName():string
    {
        return $this->name;
    }
    public function getActiveStatus()
    {
        return $this->active_status;
    }
    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->updated_date;
    }
    public function getCreatedDate():\DateTimeInterface
    {
        return $this->created_date;
    }

    public function setActiveStatus($val)
    {
        $this->active_status=$val;
        return $this;
    }
}


