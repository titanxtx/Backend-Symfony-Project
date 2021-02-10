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
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use App\Service\toolbox;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\user;
use App\Entity\email;
use App\Entity\phonenumber;
use App\Entity\socialmedia;

class InsertData extends AbstractFOSRestController{

    private $tbx;
    private $paramfetcher;
    function __construct(toolbox $tb, ParamFetcherInterface $paramfetcher){
        $this->tbx=$tb;
        $this->paramfetcher=$paramfetcher;
    }
    private function insert_any($obj)
    {
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->persist($obj);
        $entityManager->flush();
        $entityManager->clear();
        return $obj;
    }
     /**
     * @QueryParam(name="type",requirements={@Assert\Regex("/^(?:user|email|phone|social)$/mi")},nullable=true,default="user",strict=false,allowBlank=false,description="name to get users with")
     * @QueryParam(name="name",requirements={@Assert\NotBlank},nullable=true,default=NULL,strict=false,allowBlank=false,description="name to get users with")
     * @QueryParam(name="active_status",requirements="^(?:0|1)$",nullable=true,default=0,strict=false,allowBlank=false,description="Active or not")
     * @QueryParam(name="user_id",requirements={@Assert\NotBlank,@Assert\Regex("/^\d+$/m"),@Assert\GreaterThanOrEqual(1)},nullable=true,default=NULL,strict=false,allowBlank=false,description="name to get users with")
     * @QueryParam(name="email",requirements={@Assert\NotBlank,@Assert\NotNull,@Assert\Email},nullable=true,default=NULL,strict=false,allowBlank=false,description="name to get users with")
     * @QueryParam(name="phone",requirements={@Assert\NotBlank},nullable=true,default=NULL,strict=false,allowBlank=false,description="name to get users with")
     * @QueryParam(name="social_type",requirements={@Assert\NotBlank,@Assert\NotNull,@Assert\Regex("/^(?:facebook|twitter|instagram)$/mi")},nullable=true,default=NULL,strict=false,allowBlank=false,description="name to get users with")
     * @QueryParam(name="social_link",requirements={@Assert\NotBlank,@Assert\NotNull,@Assert\Url},nullable=true,default=NULL,strict=false,description="name to get users with")
     * @Post("/",name="user_post")
     */
    public function insert_data()//insert data into the database
    {
        $msg="";
        $type=strtolower($this->paramfetcher->get('type'));
        if($type=="user")//inserting a new user here inside
        {
            if(empty($this->paramfetcher->get('name'))) throw new HttpException(400, "Name parameter not set or invalid");
            if(!empty($this->paramfetcher->get('social_link'))&&empty($this->paramfetcher->get('social_type'))) throw new HttpException(400, "social_type parameter not set or invalid");
            else if(!empty($this->paramfetcher->get('social_type'))&&empty($this->paramfetcher->get('social_link'))) throw new HttpException(400, "social_link parameter not set or invalid");
            $temp_user=(new user())->setName($this->paramfetcher->get('name'));
            
            if(!empty($this->paramfetcher->get('active_status'))) $temp_user->setActiveStatus($this->paramfetcher->get('active_status')); 
            $user=$this->insert_any($temp_user);
            if(!empty($this->paramfetcher->get('email'))) $this->insert_any((new email())->setEmail($this->paramfetcher->get('email'))->setUserID($user->getID()));
            if(!empty($this->paramfetcher->get('phone'))) $this->insert_any((new phonenumber())->setPhone($this->paramfetcher->get('phone'))->setUserID($user->getID()));
            if(!empty($this->paramfetcher->get('social_link'))) $this->insert_any((new socialmedia())->setSocialType($this->paramfetcher->get('social_type'))->setLink($this->paramfetcher->get('social_link'))->setUserID($user->getID()));
            $msg="Success: ".$user->getName()." was saved";
        }
        else if($type=="email")//inserting a new email information
        {
            if(empty($this->paramfetcher->get('email'))) throw new HttpException(400, "email parameter not set or invalid");
            if(empty($this->paramfetcher->get('user_id'))) throw new HttpException(400, "user_id parameter not set or invalid");
            $this->insert_any((new email())->setEmail($this->paramfetcher->get('email'))->setUserID($this->paramfetcher->get('user_id')));
            $msg="Success: Email has been added";
        }
        else if($type=="phone")//inserting a new phone number
        {
            if(empty($this->paramfetcher->get('phone'))) throw new HttpException(400, "phone parameter not set or invalid");
            if(empty($this->paramfetcher->get('user_id'))) throw new HttpException(400, "user_id parameter not set or invalid");
            $tmp=(new phonenumber())->setPhone($this->paramfetcher->get('phone'))->setUserID($this->paramfetcher->get('user_id'));
            $this->insert_any($tmp);
            $msg="Success: Phone number has been added";
        }
        else if($type=="social")//insert new social media information
        {
            if(empty($this->paramfetcher->get('social_type'))) throw new HttpException(400, "social_type parameter not set or invalid");
            if(empty($this->paramfetcher->get('social_link'))) throw new HttpException(400, "social_link parameter not set or invalid");
            if(empty($this->paramfetcher->get('user_id'))) throw new HttpException(400, "user_id parameter not set or invalid");

            $tmp=(new socialmedia())->setSocialType($this->paramfetcher->get('social_type'))->setLink($this->paramfetcher->get('social_link'))->setUserID($this->paramfetcher->get('user_id'));
            $this->insert_any($tmp);
            $msg="Success: Social media has been added";
        }
            
        return $this->handleView($this->view([//return the information for viewing
            'code'=>200,
            'Message'=>$msg
        ]));
    }
}

?>