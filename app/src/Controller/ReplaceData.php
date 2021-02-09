<?php
namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\QueryParam;

use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\user;
use App\Entity\email;
use App\Entity\phonenumber;
use App\Entity\socialmedia;

class ReplaceData extends AbstractFOSRestController{
    private $paramfetcher;
    function __construct(ParamFetcherInterface $paramfetcher){
        $this->paramfetcher=$paramfetcher;
    }

    private function update_db($tb,$id,$func)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $element = $entityManager->getRepository($tb)->find($id);

        if (!$element) {
            throw new HttpException(400, "id {$id} is invalid");
        }
        $func($element);
        $entityManager->flush();
        $entityManager->clear();
    }
    /**
     * @QueryParam(name="type",requirements={@Assert\Regex("/^(?:user|email|phone|social)$/mi")},nullable=true,default="user",strict=true,allowBlank=false,description="What area to update")
     * @QueryParam(name="type_id",requirements={@Assert\NotBlank,@Assert\Regex("/^\d+$/m"),@Assert\GreaterThanOrEqual(1)},nullable=true,default="user",strict=true,allowBlank=false,description="ID number of the type")
     * @QueryParam(name="name",requirements={@Assert\NotBlank},nullable=true,default=NULL,strict=false,allowBlank=false,description="name of the user")
     * @QueryParam(name="active_status",requirements="^(?:0|1)$",nullable=true,default=0,strict=false,allowBlank=false,description="Active or not")
     * @QueryParam(name="email",requirements={@Assert\NotBlank,@Assert\NotNull,@Assert\Email},nullable=true,default=NULL,strict=false,allowBlank=false,description="email address")
     * @QueryParam(name="phone",requirements={@Assert\NotBlank},nullable=true,default=NULL,strict=false,allowBlank=false,description="phone number")
     * @QueryParam(name="social_type",requirements={@Assert\NotBlank,@Assert\NotNull,@Assert\Regex("/^(?:facebook|twitter|instagram)$/mi")},nullable=true,default=NULL,strict=false,allowBlank=false,description="Social media type")
     * @QueryParam(name="social_link",requirements={@Assert\NotBlank,@Assert\NotNull,@Assert\Url},nullable=true,default=NULL,strict=false,description="Social media link")
     * @Put("/",name="update_data")
     */
    public function update()
    {
        $msg="";
        $type=strtolower($this->paramfetcher->get('type'));
        if($type=="user")
        {
            if(empty($this->paramfetcher->get('name'))&&empty($this->paramfetcher->get('active_status'))) throw new HttpException(400, "One parameter at least for user has to be set or some of your parameters are invalid");
            $this->update_db('App\Entity\user',$this->paramfetcher->get('type_id'),function(&$element){
                if($this->paramfetcher->get('name')!="") $element->setName($this->paramfetcher->get('name'));
                if($this->paramfetcher->get('active_status')!="") $element->setActiveStatus($this->paramfetcher->get('active_status'));
                $element->setUpdatedDate(new \DateTime("now"));
            });
            $msg="Success: User was updated";
        }
        else if($type=="email")
        {
            if(empty($this->paramfetcher->get('email'))) throw new HttpException(400, "One parameter at least for user has to be set or some of your parameters are invalid");
            $this->update_db('App\Entity\email',$this->paramfetcher->get('type_id'),function(&$element){
                $element->setEmail($this->paramfetcher->get('email'));
                $this->update_db('App\Entity\user',$element->getUserID,function(&$element){
                    $element->setUpdatedDate(new \DateTime("now"));
                });
            });
            $msg="Success: Email has been updated";
        }
        else if($type=="phone")
        {
            if(empty($this->paramfetcher->get('phone'))) throw new HttpException(400, "One parameter at least for user has to be set or some of your parameters are invalid");
            $this->update_db('App\Entity\phonenumber',$this->paramfetcher->get('type_id'),function(&$element){
                $element->setPhone($this->paramfetcher->get('phone'));
                $this->update_db('App\Entity\user',$element->getUserID,function(&$element){
                    $element->setUpdatedDate(new \DateTime("now"));
                });
            });
            $msg="Success: Phone number has been updated";
        }
        else if($type=="social")
        {
            if(empty($this->paramfetcher->get('social_type'))&&empty($this->paramfetcher->get('social_link'))) throw new HttpException(400, "One parameter at least for user has to be set or some of your parameters are invalid");
            $this->update_db('App\Entity\socialmedia',$this->paramfetcher->get('type_id'),function(&$element){
                if(strtolower($this->paramfetcher->get('social_type'))=="facebook")
                {
                    $element->setFacebook(1);
                    $element->setTwitter(0);
                    $element->setInstagram(0);
                }
                else if(strtolower($this->paramfetcher->get('social_type'))=="twitter"){
                    $element->setFacebook(0);
                    $element->setTwitter(1);
                    $element->setInstagram(0);
                }
                else if(strtolower($this->paramfetcher->get('social_type'))=="instagram"){
                    $element->setFacebook(0);
                    $element->setTwitter(0);
                    $element->setInstagram(1);
                }
                $element->setLink($this->paramfetcher->get('social_link'));
                $this->update_db('App\Entity\user',$element->getUserID,function(&$element){
                    $element->setUpdatedDate(new \DateTime("now"));
                });
            });
            $msg="Success: Social media has been updated";
        }
        return $this->handleView($this->view([
            'code'=>200,
            'Message'=>$msg
        ]));
    }
}