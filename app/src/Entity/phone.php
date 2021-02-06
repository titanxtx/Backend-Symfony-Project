<?php
namespace App\Entity;

use Doctrine\ORM\Mapp as ORM;


class phone{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer",options={"unsigned":true})
     */
    private $id;
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