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
class InsertData extends AbstractFOSRestController{

    private $tbx;
    private $paramfetcher;
    function __construct(toolbox $tb, ParamFetcherInterface $paramfetcher){
        $this->tbx=$tb;
        $this->paramfetcher=$paramfetcher;
    }
    /**
     * @QueryParam(name="json",requirements={@Assert\Json},default=null,strict=true,allowBlank=false,description="name to get users with")
     * @Post("/users",name="users_post")
     */
    public function insert_users()
    {
        $jsonx=json_decode($this->paramfetcher->get('json'));
    }
    /**
     * @QueryParam(name="name",requirements="^(?:[^,]+,?)+$",default=null,strict=true,allowBlank=false,description="name to get users with")
     * @QueryParam(name="email",requirements="^(?:[^,]+,?)+$",default=null,strict=true,allowBlank=false,description="name to get users with")
     * @Post("/user",name="user_post")
     */
    public function insert_user()
    {
        $entityManager=$this->getDoctrine()->getManager();
        $user= (new user())->setName($this->paramfetcher->get('name'));
        $entityManager->persist($user);
        $entityManager->flush();
        $email=(new email())->setEmail($this->paramfetcher->get('email'))->setUserID($user->getID());
        $entityManager->persist($email);
        $entityManager->flush();
        return $this->handleView($this->view([
            'code'=>200,
            'Message'=>"Success: ".$user->getName()." was saved"
        ]));
    }   
    /**
     * @QueryParam(name="name",requirements="^(?:[^,]+,?)+$",default=null,strict=true,allowBlank=false,description="name to get users with")
     * @QueryParam(name="email",requirements="^(?:[^,]+,?)+$",default=null,strict=true,allowBlank=false,description="name to get users with")
     * @Post("/user",name="email_post")
     */
    public function insert_email()
    {

    }
    /**
     * @QueryParam(name="name",requirements="^(?:[^,]+,?)+$",default=null,strict=true,allowBlank=false,description="name to get users with")
     * @QueryParam(name="email",requirements="^(?:[^,]+,?)+$",default=null,strict=true,allowBlank=false,description="name to get users with")
     * @Post("/user",name="data_post")
     */
    public function insert_data()//any data here but certain columns have to be here to be added, everything will be validated
    {

    }
}

?>