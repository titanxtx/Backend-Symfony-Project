<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_emails")
 */
class email{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer",options={"unsigned":true})
     */
    private $id;
    /**
     * @ORM\Column(type="integer",options={"unsigned":true})
     */
    private $user_id;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\user", inversedBy="email")
     * @ORM\Column(type="string",length=320)
     */
    private $email;

    public function getId(){
        return  $this->id;
    }
    public function setId($val){
        $this->id=$val;
    }
    public function getEmail():string
    {
        return $this->email;
    }
    public function setEmail($val)
    {
        $this->email=$val;
    }
}