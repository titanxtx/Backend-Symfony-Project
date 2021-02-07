<?php
namespace App\Entity;

use Doctrine\ORM\Mapp as ORM;
use Symfony\Component\Validator\Constraints as Assert;


class phone{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer",options={"unsigned":true})
     */
    private $id;
    /**
     * @Assert\Type(type="integer")
     * @Assert\Positive
     * @ORM\ManyToOne(targetEntity="App\Entity\user", inversedBy="emails", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user_id;//many to one since it will be many user_ids in the email table attaching to one id inside the user table
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $phone;

    public function getId(){
        return  $this->id;
    }
    public function setId($val){
        $this->id=$val;
    }
    public function getPhone():string
    {
        return $this->email;
    }
    public function setPhone($val)
    {
        $this->phone=$val;
    }
}