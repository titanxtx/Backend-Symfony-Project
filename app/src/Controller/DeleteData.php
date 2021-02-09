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

class DeleteData extends AbstractFOSRestController{

    private $tbx;
    private $paramfetcher;
  //  private $entityManager;
    function __construct(toolbox $tb, ParamFetcherInterface $paramfetcher){
        $this->tbx=$tb;
        $this->paramfetcher=$paramfetcher;
        //$this->entityManager=$this->getDoctrine()->getManager();
    }
    /**
     * @QueryParam(name="user_id",requirements={@Assert\NotBlank,@Assert\Regex("/^\d+$/m"),@Assert\GreaterThanOrEqual(1)},nullable=true,default=NULL,strict=true,allowBlank=false,description="name to get users with")
     * @QueryParam(name="type",requirements={@Assert\Regex("/^(?:user|email|phone|social)$/mi")},nullable=true,default="user",strict=false,allowBlank=false,description="name to get users with")
     * @QueryParam(name="name",requirements={@Assert\NotBlank},nullable=true,default=NULL,strict=false,allowBlank=false,description="name to get users with")
     * @QueryParam(name="active_status",requirements="^(?:0|1)$",nullable=true,default=0,strict=false,allowBlank=false,description="Active or not")
     * @QueryParam(name="email",requirements={@Assert\NotBlank,@Assert\NotNull,@Assert\Email},nullable=true,default=NULL,strict=false,allowBlank=false,description="name to get users with")
     * @QueryParam(name="phone",requirements={@Assert\NotBlank},nullable=true,default=NULL,strict=false,allowBlank=false,description="name to get users with")
     * @QueryParam(name="social_type",requirements={@Assert\NotBlank,@Assert\NotNull,@Assert\Regex("/^(?:facebook|twitter|instagram)$/mi")},nullable=true,default=NULL,strict=false,allowBlank=false,description="name to get users with")
     * @QueryParam(name="social_link",requirements={@Assert\NotBlank,@Assert\NotNull,@Assert\Url},nullable=true,default=NULL,strict=false,description="name to get users with")
     * @Get("/replace",name="user_post")
     */
    public function replace()
    {

    }
}