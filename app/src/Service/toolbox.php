<?php
namespace App\Service;
//use Doctrine\ORM\Query\ResultSetMapping;

use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\DBALException;
class toolbox {
    private $connection;
    private $validator;
    private $constraint_data;
    function __construct(Connection $connection) //importing our database connection
    {
        $this->connection=$connection;
        $this->validator=Validation::createValidator();
        $this->constraint_data=new Assert\Collection(['fields'=>[
            'id'=>[new Assert\NotNull(),new Assert\NotBlank(),new Assert\GreaterThanOrEqual(['value'=>1,'message'=>'Page number {{value}} is invalid. Enter a number greater than or equal to one.'])],
            'name'=>[new Assert\NotNull(),new Assert\NotBlank()],
            'page'=>[new Assert\NotNull(),new Assert\NotBlank(),new Assert\GreaterThanOrEqual(['value'=>1,'message'=>'Page number {{value}} is invalid. Enter a number greater than or equal to one.'])],
            'amount'=>[new Assert\NotNull(),new Assert\NotBlank(),new Assert\Range(['min'=>1,'max'=>100,'notInRangeMessage' => 'The amount {{value}} is invalid. The amount should be equal to or between {{ min }} and {{ max }}.'])],
            'sort'=>[new Assert\NotNull(),new Assert\NotBlank(),new Assert\Regex(['pattern'=>'/^(?:asc|desc)$/i','message'=>"Value {{ value }} is not one of the options."])],
            'email'=>[new Assert\NotNull(),new Assert\NotBlank(),new Assert\Email(['message'=>'The email "{{ value }}" is not a valid email.'])]
        ],'allowMissingFields'=>true,'allowExtraFields'=>false]);
    }
    public function dbcall($sql,$args=[],&$empty,$mode=0,$code=null)//call the database here, get the information and decode the json
    {
        try{
            $statement=$this->connection->prepare($sql);
            $statement->execute($args);
            if($statement->rowCount()==0) $empty=true;
            if($mode==1) return $this->getJSONData($statement);//json mode
            else if($mode==2) return $statement->fetchAll();
            return ($code!=null)?$code($statement):$statement;
        }
        catch(DBALException $e)
        {
            return null;
        }
    }
    private function getJSONData($stmt):array//get json information from the database results
    {
        $data=[];
        while(($row=$stmt->fetch())!=false)
        {
            array_push($data,json_decode($row['result'],true));//decoding the json information recursively since there is nested information
        }
        return $data;
    }
    public function split_generate_placeholders(string $val,&$placeholders,$delimiter=','):array //create sql question mark placeholders so we can enter in data dynamically safely
    {
        $tmp=explode($delimiter,$val);
        $placeholders=(!is_null($tmp)&&!empty($tmp)&&is_array($tmp))?str_repeat('?,', count($tmp) - 1) . '?':'';
        return $tmp;
    }
    public function validate_params($data)// validate information with our class memeber contraints variable - constraint_data
    {
        return $this->validator->validate($data,$this->constraint_data);
    }
}
