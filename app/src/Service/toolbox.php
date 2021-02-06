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

class toolbox {
    private $connection;
    private $validator;
    private $constraint_data;
    function __construct(Connection $connection)
    {
        $this->connection=$connection;
        $this->validator=Validation::createValidator();
        $this->constraint_data=new Assert\Collection(['fields'=>[
            'id'=>[new Assert\NotNull(),new Assert\NotBlank(),new Assert\GreaterThanOrEqual(1)],
            'name'=>[new Assert\NotNull(),new Assert\NotBlank()],
            'page'=>[new Assert\NotNull(),new Assert\NotBlank(),new Assert\GreaterThanOrEqual(1)],
            'amount'=>[new Assert\NotNull(),new Assert\NotBlank(),new Assert\Range(['min'=>1,'max'=>100])],
            'sort'=>[new Assert\NotNull(),new Assert\NotBlank(),new Assert\Regex('/^(?:asc|desc)$/i')]

        ],'allowMissingFields'=>true,'allowExtraFields'=>false]);
    }
    public function dbcall($sql,$args=[],$mode=0,$code=null)
    {
        $statement=$this->connection->prepare($sql);
        $statement->execute($args);
        if($mode==1) return $this->getJSONData($statement);//json mode
        else if($mode==2) return $statement->fetchAll();
        return ($code!=null)?$code($statement):$statement;
    }
    private function getJSONData($stmt):array
    {
        $data=[];
        while(($row=$stmt->fetch())!=false)
        {
            array_push($data,json_decode($row['result'],true));
        }
        return $data;
    }
    public function split_generate_placeholders(string $val,&$placeholders,$delimiter=','):array
    {
        $tmp=explode($delimiter,$val);
        $placeholders=(!is_null($tmp)&&!empty($tmp)&&is_array($tmp))?str_repeat('?,', count($tmp) - 1) . '?':'';
        return $tmp;
    }
    public function validate_params($data)
    {
        return $this->validator->validate($data,$this->constraint_data);
    }
}
