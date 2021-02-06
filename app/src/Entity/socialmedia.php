<?php
namespace App\Entity;

use Doctrine\ORM\Mapp as ORM;


class email{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer",options={"unsigned":true})
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=320)
     */
    private $socialmedia;

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