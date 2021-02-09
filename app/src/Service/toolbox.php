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
    function __construct(Connection $connection)
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
    public function dbcall($sql,$args=[],$mode=0,$code=null)
    {
        try{
            $statement=$this->connection->prepare($sql);
            $statement->execute($args);
            if($mode==1) return $this->getJSONData($statement);//json mode
            else if($mode==2) return $statement->fetchAll();
            return ($code!=null)?$code($statement):$statement;
        }
        catch(DBALException $e)
        {
            return null;
        }
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
/*
    public function validate_user($data)
    {
        $user=new Assert\Collection(['fields'=>[
            'active_status'=>new Assert\Optional([new Assert\NotNull(),new Assert\NotBlank(),new Assert\Range(['min'=>0,'max'=>1,'notInRangeMessage' => 'The amount {{value}} is invalid. The amount should be equal to or between {{ min }} and {{ max }}.'])]),
            'phone_numbers'=>new Assert\Optional([new Assert\Optional([new Assert\AtLeastOneOf(['constraints'=>[
                    [
                        new Assert\Type('array'),
                        new Assert\All(['constraints'=>[
                            new Assert\NotNull(),
                            new Assert\NotBlank()
                            ]
                        ])
                    ],
                    [
                        new Assert\Type('string'),
                        new Assert\NotNull(),
                        new Assert\NotBlank()
                    ]
                ]])])]),
            'emails'=>
                new Assert\Optional([new Assert\AtLeastOneOf(['constraints'=>[
                    [
                        new Assert\Type('array'),
                        new Assert\All(['constraints'=>[
                            new Assert\NotNull(),
                            new Assert\NotBlank(),
                            new Assert\Email()
                            ]
                        ])
                    ],
                    [
                        new Assert\Type('string'),
                        new Assert\NotNull(),
                        new Assert\NotBlank(),
                        new Assert\Email()
                    ]
                ]])]),
                'socialmedia'=>new Assert\Optional([new Assert\AtLeastOneOf(['constraints'=>[
                    [
                        new Assert\Type('array'),
                        new Assert\All(['constraints'=>[
                            new Assert\Collection(['fields'=>[
                                'type'=>[new Assert\NotNull(),new Assert\Type('string'),new Assert\NotBlank(),new Assert\Regex("/^(?:facebook|twitter|instagram)$/mi")],
                                'link'=>[new Assert\NotNull(),new Assert\Type('string'),new Assert\NotBlank(),new Assert\Url()]
                            ]])
                            ]
                        ])
                    ],
                    [
                        new Assert\Collection(['fields'=>[
                            'type'=>[new Assert\NotNull(),new Assert\Type('string'),new Assert\NotBlank(),new Assert\Regex("/^(?:facebook|twitter|instagram)$/mi")],
                            'link'=>[new Assert\NotNull(),new Assert\Type('string'),new Assert\NotBlank(),new Assert\Url()]
                        ]])
                    ]
                ]])]),
            'updated_date'=>new Assert\Optional([[new Assert\Type('string'),new Assert\NotNull(),
            new Assert\NotBlank()]]),
            'created_date'=>new Assert\Optional([[new Assert\Type('string'),new Assert\NotNull(),
            new Assert\NotBlank()]]),
        ],'allowMissingFields'=>true,'allowExtraFields'=>true]);

        $body=new Assert\Collection(['fields'=>[
            'user'=>[
            new Assert\AtLeastOneOf(['constraints'=>[
                [new Assert\Type('array'),
                new Assert\All(['constraints'=>[$user]])],
                $user
            ]]),
            ]],'allowExtraFields'=>true,'allowMissingFields'=>false]);

        $constraint=new Assert\AtLeastOneOf(['constraints'=>[[new Assert\Type('array'),new Assert\All(['constraints'=>$body])],$body]]);
        return $this->validator->validate($data,$constraint);
    }*/
}
