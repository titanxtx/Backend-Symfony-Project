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
use FOS\RestBundle\Controller\Annotations\Delete;
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
    function __construct(toolbox $tb, ParamFetcherInterface $paramfetcher){
        $this->tbx=$tb;
        $this->paramfetcher=$paramfetcher;
    }
    private function delete_db($tb,$id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $element = $entityManager->getRepository($tb)->find($id);

        if (!$element) {
            throw new HttpException(400, "id {$id} is invalid");
        }
        $entityManager->remove($element);
        $entityManager->flush();
    }
    /**
     * @QueryParam(name="type",requirements={@Assert\Regex("/^(?:user|email|phone|social)$/mi")},default="user",strict=true,allowBlank=false,description="What area to delete")
     * @QueryParam(name="type_id",requirements={@Assert\NotBlank,@Assert\Regex("/^\d+$/m"),@Assert\GreaterThanOrEqual(1)},default="user",strict=true,allowBlank=false,description="ID number of the type you want to delete")
     * @Delete("/delete",name="delete_data")
     */
    public function delete()
    {
        $msg="";
        if(strtolower($this->paramfetcher->get('type'))=="user")
        {
            $this->delete_db('App\Entity\user',$this->paramfetcher->get('type_id'));
            $msg="Success: User was deleted";
        }
        else if(strtolower($this->paramfetcher->get('type'))=="email")
        {
            $this->delete_db('App\Entity\email',$this->paramfetcher->get('type_id'));
            $msg="Success: Email has been deleted";
        }
        else if(strtolower($this->paramfetcher->get('type'))=="phone")
        {
            $this->delete_db('App\Entity\phonenumber',$this->paramfetcher->get('type_id'));
            $msg="Success: Phone number has been deleted";
        }
        else if(strtolower($this->paramfetcher->get('type'))=="social")
        {
            $this->delete_db('App\Entity\socialmedia',$this->paramfetcher->get('type_id'));
            $msg="Success: Social media has been deleted";
        }
        return $this->handleView($this->view([
            'code'=>200,
            'Message'=>$msg
        ]));
    }
}