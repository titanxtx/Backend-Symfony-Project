<?php
namespace App\Entity;
//Used for getting all the users out there or getting one users information all in one go
//use Doctrine\ORM\Mapp as ORM;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank
     * @Assert\NotNull
    * @Assert\Valid
    * @ORM\Column(type="string", length=255)
    */
    private $name;
    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":false})
     * @Assert\NotBlank
     * @Assert\NotNull
     * @Assert\Range(min=0,max=1)
    * @Assert\Valid
     */
    private $active_status=0;
    /**
     * 
     * @Assert\NotNull
    * @Assert\Valid
     * @ORM\Column(type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     * @ORM\Version
     */
    private $updated_date;
    /**
     * @Assert\NotNull
     * @ORM\Column(type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
    * @Assert\Valid
     * @ORM\Version
     */
    private $created_date;

    function __construct(){
        $this->updated_date=new \DateTime("now");
        $this->created_date=new \DateTime("now");
    }

    public function getID()
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
    
    public function setName(string $val):user
    {
        $this->name=$val;
        return $this;
    }
    public function setActiveStatus(?bool $val):user
    {
        $this->active_status=$val;
        return $this;
    }
    public function setUpdatedDate($val):user
    {
        $this->updated_date=$val;
        return $this;
    }
}


