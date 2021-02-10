<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_socialmedia")
 */
class socialmedia{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer",nullable=false,options={"unsigned":true})
     */
    private $id;
    /**
     * 
     * @Assert\NotNull
     * @Assert\NotBlank
     * @ORM\Column(type="integer",nullable=false,options={"unsigned":true})
     * @Assert\Type(type="integer")
     * @Assert\Positive
    * @Assert\Valid
     * 
     */
    private $user_id;//@ORM\ManyToOne(targetEntity="App\Entity\user", inversedBy="emails", cascade={"persist"})   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default":false})
     * @Assert\NotBlank
     * @Assert\NotNull
     * @Assert\Range(min=0,max=1)
    * @Assert\Valid
     */
    private $facebook=0;
    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default":false})
     * @Assert\NotBlank
     * @Assert\NotNull
     * @Assert\Range(min=0,max=1)
    * @Assert\Valid
     */
    private $twitter=0;
    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default":false})
     * @Assert\NotBlank
     * @Assert\NotNull
     * @Assert\Range(min=0,max=1)
    * @Assert\Valid
     */
    private $instagram=0;
    /**
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Url
     * @Assert\Valid
     * @ORM\Column(type="string",nullable=false, length=2083)
     */
    private $link;

    public function getId(){
        return  $this->id;
    }
    
    public function getUserID():string
    {
        return $this->user_id;
    }
    public function getFacebook():bool
    {
        return $this->facebook;
    }
    public function getTwitter():bool
    {
        return $this->twitter;
    }
    public function getInstagram():bool
    {
        return $this->instagram;
    }
    public function getSocialType():string{
        if($this->instagram==1) return 'Instagram';
        return ($this->Facebook==1)?'Facebook':'Twitter';
    }
    public function getLink():string
    {
        return $this->link;
    }

    public function setSocialType($val):socialmedia
    {
        $tmp=strtolower($val);
        if($tmp=='facebook') $this->setFacebook(1);
        else if($tmp=='twitter') $this->setTwitter(1);
        else if($tmp=='instagram') $this->setInstagram(1);
        return $this;
    }
    public function setUserID($val):socialmedia
    {
        $this->user_id=$val;
        return $this;
    }
    public function setFacebook($val):socialmedia
    {
        $this->facebook=$val;
        return $this;
    }
    public function setTwitter($val):socialmedia
    {
        $this->twitter=$val;
        return $this;
    }
    public function setInstagram($val):socialmedia
    {
        $this->instagram=$val;
        return $this;
    }
    public function setLink($val):socialmedia
    {
        $this->link=$val;
        return $this;
    }
}