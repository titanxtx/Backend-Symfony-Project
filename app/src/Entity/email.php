<?php
namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_emails")
 */
class email{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer",options={"unsigned":true})
     */
    private $id;
    /**
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @Assert\Type(type="integer")
     * @Assert\Positive
    * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="App\Entity\user", inversedBy="emails", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user_id;//many to one since it will be many user_ids in the email table attaching to one id inside the user table
    /**
     * @Assert\Email
    * @Assert\Valid
     * @ORM\Column(type="string",length=320)
     */
    private $email;

    public function getId(){
        return  $this->id;
    }
    public function getEmail():string
    {
        return $this->email;
    }
    public function setEmail(string $val):email
    {
        $this->email=$val;
        return $this;
    }
    public function getUserID():string
    {
        return $this->user_id;
    }
    public function setUserID(int $val):email
    {
        $this->user_id=$val;
        return $this;
    }
}