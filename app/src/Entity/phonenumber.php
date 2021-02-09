<?php
namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_phone")
 */
class phonenumber{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer",nullable=false,options={"unsigned":true})
     */
    private $id;
    /**
     * @ORM\Column(type="integer",nullable=false,options={"unsigned":true})
     * @Assert\NotNull
     * @Assert\NotBlank 
     * 
     * 
     * @Assert\Type(type="integer")
     * @Assert\Positive
    * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="App\Entity\user", inversedBy="emails", cascade={"persist"}) 
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user_id;//many to one since it will be many user_ids in the email table attaching to one id inside the user table      
    /**
     * @ORM\Column(type="string",nullable=false, length=255)
    * @Assert\NotBlank
    * @Assert\NotNull
    * @Assert\Valid
     */
    private $phonenumber;

    public function getId(){
        return  $this->id;
    }
    public function getUserID():string
    {
        return $this->user_id;
    }
    public function setUserID($val):self
    {
        $this->user_id=$val;
        return $this;
    }
    public function getPhone():string
    {
        return $this->phonenumber;
    }
    public function setPhone($val):self
    {
        $this->phonenumber=$val;
        return $this;
    }
}