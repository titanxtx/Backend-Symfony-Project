<?php
namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;

use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Entity\user;
use App\Entity\email;
use App\Entity\phonenumber;
use App\Entity\socialmedia;

class DeleteData extends AbstractFOSRestController{
    private $paramfetcher;
    function __construct(ParamFetcherInterface $paramfetcher){
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
     * @Delete("/",name="delete_data")
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

/** -- DELETE request
 *  delete user by user id
 * http://localhost:8080/?type=user&type_id=1
 *  delete email by email id
 * http://localhost:8080/?type=email&type_id=1
 *  delete phone number by phone number id
 * http://localhost:8080/?type=phone&type_id=1
 *  delete social media by social media info by id
 * http://localhost:8080/?type=social&type_id=1
 */

?>