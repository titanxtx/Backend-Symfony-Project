<?php
namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\QueryParam;

use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use App\Service\toolbox;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Entity\user;
use App\Entity\email;
use App\Entity\phonenumber;
use App\Entity\socialmedia;


class GetData extends AbstractFOSRestController{
    private $tbx;
    private $paramfetcher;
    function __construct(toolbox $tb, ParamFetcherInterface $paramfetcher){
        $this->tbx=$tb;
        $this->paramfetcher=$paramfetcher;
    }
    private function get_db($tb,$id,$func)
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
    private function get_page(&$start,&$amt)
    {
        $amt=($this->paramfetcher->get('amount')<1)?1:$this->paramfetcher->get('amount');
        $start=(($this->paramfetcher->get('page')<1)?1:$this->paramfetcher->get('page')-1)*$amt;
    }
    private function filter_sql(array &$paramx,&$in,&$in2,int $start,int $amt):string //The sql statement is made here
    {
        $sortingby=['user_id'=>'w.id','active_status'=>'w.active_status','updated_date'=>'updated_date','created_date'=>'created_date','email_amt'=>'JSON_LENGTH(m.emails)','phone_amt'=>'JSON_LENGTH(m.phone)','social_amt'=>'JSON_LENGTH(m.social)'];//name|created_date|updated_date|email_amt|phone_amt|social_amt']

    //"select (z.result) as result from (select JSON_OBJECT('user_id',w.id,'name',w.name,'emails:',m.emails,'phone_numbers',m.phone,'social_media',m.social,'active_status',w.active_status,'updated_date',updated_date,'created_date',created_date) as result from users w left join (select * from (select a.id as idx,if(count(b.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('email_id',b.id,'email',b.email))) as emails from users a left join user_emails b on a.id=b.user_id group by a.id,a.name,b.user_id order by a.id asc ) r left join (select c.id as idz,if(count(d.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('phone_id',d.id,'phone_number',d.phonenumber))) as phone from users c left join user_phone d on c.id=d.user_id group by c.id,c.name,d.user_id order by c.id asc) s on r.idx=s.idz left join (select c.id,if(count(d.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('social_id',d.id,'socialmedia_type',if(d.facebook=1,'Facebook',if(d.twitter=1,'Twitter','Instagram')),'socialmedia_link',d.link))) as social from users c left join user_socialmedia d on c.id=d.user_id group by c.id,c.name,d.user_id order by c.id asc) t on t.id=s.idz ) m on w.id=m.idx order by w.id asc limit {$start},{$amt}) z";
    if(empty($paramx['only'])) return "select (z.result) as result from (select JSON_OBJECT('user_id',w.id,'name',w.name,'emails:',m.emails,'phone_numbers',m.phone,'social_media',m.social,'active_status',w.active_status,'updated_date',updated_date,'created_date',created_date) as result from users w left join (select * from (select a.id as idx,if(count(b.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('email_id',b.id,'email',b.email))) as emails from users a left join user_emails b on a.id=b.user_id group by a.id,a.name,b.user_id order by a.id asc ) r left join (select c.id as idz,if(count(d.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('phone_id',d.id,'phone_number',d.phonenumber))) as phone from users c left join user_phone d on c.id=d.user_id group by c.id,c.name,d.user_id order by c.id asc) s on r.idx=s.idz left join (select c.id,if(count(d.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('social_id',d.id,'socialmedia_type',if(d.facebook=1,'Facebook',if(d.twitter=1,'Twitter','Instagram')),'socialmedia_link',d.link))) as social from users c left join user_socialmedia d on c.id=d.user_id group by c.id,c.name,d.user_id order by c.id asc) t on t.id=s.idz ) m on w.id=m.idx ".((!empty($in)||!empty($in2))?" where ":" ").((!empty($in))?"w.id in (".$in.")":"").((!empty($in)&&!empty($in2))?' or':'').((!empty($in2))?' w.name in ('.$in2.')':'')." order by {$sortingby[$paramx['sortby']]} {$paramx['order']} limit {$start},{$amt}) z";
    else {
            $val=['user_id'=>['type'=>0,'data'=>'w.id'],'name'=>['type'=>0,'data'=>'w.name'],'active_status'=>['type'=>0,'data'=>'w.active_status'],
            'updated_date'=>['type'=>0,'data'=>'a.updated_date'],'created_date'=>['type'=>0,'data'=>'a.created_date'],'emails'=>['type'=>1,'data'=>'m.emails','table_alias'=>'r','id'=>'idx','join'=>"(select a.id as idx,if(count(b.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('email_id',b.id,'email',b.email))) as emails from users a left join user_emails b on a.id=b.user_id group by a.id,a.name,b.user_id order by a.id asc )"],
            'phone_numbers'=>['type'=>1,'data'=>'m.phone','table_alias'=>'s','id'=>'idz','join'=>"(select c.id as idz,if(count(d.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('phone_id',d.id,'phone_number',d.phonenumber))) as phone from users c left join user_phone d on c.id=d.user_id group by c.id,c.name,d.user_id order by c.id asc)"],
            'social_media'=>['type'=>1,'data'=>'m.social','table_alias'=>'t','id'=>'id','join'=>"(select c.id,if(count(d.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('social_id',d.id,'socialmedia_type',if(d.facebook=1,'Facebook',if(d.twitter=1,'Twitter','Instagram')),'socialmedia_link',d.link))) as social from users c left join user_socialmedia d on c.id=d.user_id group by c.id,c.name,d.user_id order by c.id asc)"]];//regular columns   check for join columns emails later manually
           // $only=[];
           // foreach(explode(',',$paramx['only']) as $x) $only[strtolower($x)]=1;//turning the only parameter value into a associative array
            $joins=[];
            $columns=[];
            $last_index=null;
            $last_join_id=null;
            foreach(explode(',',$paramx['only']) as $x)//we are getting the keys of the only parameter
            {
                array_push($columns,"'{$x}',{$val[$x]['data']}");
                 if($val[$x]['type']==1)
                 {
                    array_push($joins,((count($joins)==0)?'select * from ':' left join ').$val[$x]['join']." {$val[$x]['table_alias']}".((!is_null($last_join_id))?" on {$val[$x]['table_alias']}.{$val[$x]['id']}={$last_join_id}":''));
                    $last_join_id="{$val[$x]['table_alias']}.{$val[$x]['id']}";
                    $last_index=$val[$x]['id'];
                 }
                
            }
            $sqlstr= "select (z.result) as result from (select JSON_OBJECT(".implode(',',$columns).") as result from users w ".((!empty($joins))?'left join ('.implode(' ',$joins).") m on m.{$last_index}=w.id":'').' '.((!empty($in)||!empty($in2))?" where ":" ").((!empty($in))?"w.id in (".$in.")":"").((!empty($in)&&!empty($in2))?' or':'').((!empty($in2))?' w.name in ('.$in2.')':'')." order by {$sortingby[$paramx['sortby']]} {$paramx['order']} limit {$start},{$amt}".') z';
            return $sqlstr;
        }
    }
    private function get_tableinformation($ids,$func)  //QueryParam(name="user_id",requirements={@Assert\Regex("/^(?:\d+,?)+$/m")},nullable=true,strict=true,default=null,allowBlank=false,description="Users to get can be a single number or an array 1,2,3,4,5")
    {
        $data=[];
        foreach($ids as $id)
        {
            array_push($data,$func($id));
        }
        //var_dump($data);
        return $data;
    }
    /**
     * @QueryParam(name="type",requirements={@Assert\Regex("/^(?:user|email|phone|social)$/mi")},nullable=true,default="user",strict=true,allowBlank=false,description="What area to get")
     * @QueryParam(name="type_id",requirements={@Assert\Regex("/^(?:[1-9]\d*,?)+$/m")},nullable=true,default=NULL,strict=true,allowBlank=false,description="ID number of the type")
     * @QueryParam(name="only",requirements={@Assert\Regex("/^(?:(?:user_id|name|active_status|emails|email_id|email_address|phone_numbers|social_media|updated_date|created_date),?)+$/mi")},nullable=true,strict=true,allowBlank=false,default=null,description="Page number of the result")
     * @QueryParam(name="page",requirements={@Assert\Regex("/^\d+$/m"),@Assert\GreaterThanOrEqual(1)},nullable=true,strict=true,allowBlank=false,default=1,description="Page number of the result")
     * @QueryParam(name="amount",requirements={@Assert\Regex("/^\d+$/m"),@Assert\Range(min=1,max=100)},nullable=true,strict=true,allowBlank=false,default=20,description="Amount of results from the page number")
     * @QueryParam(name="name",requirements="^(?:[^,]+,?)+$",nullable=true,default=null,strict=true,allowBlank=false,description="name to get users with")

     * @QueryParam(name="sortby",requirements={@Assert\Regex("/^(?:user_id|name|created_date|updated_date|email_amt|phone_amt|social_amt)$/mi")},nullable=true,strict=true,allowBlank=false,default="user_id",description="Sort by what data")
     * @QueryParam(name="order",requirements="^(asc|desc)$",nullable=true,strict=true,allowBlank=false,default="asc",description="Sort order")
     * @Get("/",name="get_info",methods={"GET"})
     */
    public function get_alldata()//get any data from the tables in the database. A mix of dbal queries and doctrine ORM for stuff other than the users
    {
        $type=strtolower($this->paramfetcher->get('type'));
        if($type=='user')
        {
            $this->get_page($start,$amt);
            $paramx=$this->paramfetcher->all();
            $db_vals=[];
            $in=null;
            $in2=null;
            if(!empty($paramx['type_id'])) $db_vals=array_merge($db_vals,$this->tbx->split_generate_placeholders($paramx['type_id'],$in));//split and generate placeholders for sql statement   'user_id'
            if(!empty($paramx['name']))  $db_vals=array_merge($db_vals,$this->tbx->split_generate_placeholders($paramx['name'],$in2));
            $sql=$this->filter_sql($paramx,$in,$in2,$start,$amt);
            $data=$this->tbx->dbcall($sql,$db_vals,1);
            if(!is_null($data))
            {
                return $this->handleView($this->view([
                    'code'=>200,
                    'Message'=>"Success",
                    'data'=>$data
                ]));
            }
            else{
                return $this->handleView($this->view([
                    'code'=>400,
                    'Message'=>"Failed",
                    'data'=>[]
                ]));
            }
        }
        else{
            if(empty($this->paramfetcher->get('type_id'))) throw new HttpException(400, "type_id is invalid or not present");
            $output=['code'=>200,'Message'=>"Success",'data'=>null];
            $ids=explode(',',$this->paramfetcher->get('type_id'));//get multiple ids of any email in the table
            if($type=='email')//Get emails
            {
                $output['data']=$this->get_tableinformation($ids,function(&$id){
                    $tmp=['email_id'=>null,'user_id'=>null,'email'=>null];
                    $this->get_db('App\Entity\email',$id,function(&$element)use(&$tmp){
                            $tmp['email_id']=$element->getID();
                            $tmp['user_id']=$element->getUserID();
                            $tmp['email']=$element->getEmail();
                    });
                    return $tmp;
                });
            }
            else if($type=='phone')//Get any phone numbers
            {
                $output['data']=$this->get_tableinformation($ids,function(&$id){
                    $tmp=['phone_id'=>null,'user_id'=>null,'phonenumber'=>null];
                    $this->get_db('App\Entity\phonenumber',$id,function(&$element)use(&$tmp){
                        $tmp['phone_id']=$element->getID();
                        $tmp['user_id']=$element->getUserID();
                        $tmp['phonenumber']=$element->getPhone();
                    });
                    return $tmp;
                });

            }
            else if($type=='social')//get any socialmedia information
            {
                $output['data']=$this->get_tableinformation($ids,function(&$id){
                    $tmp=['social_id'=>null,'user_id'=>null,'social_type'=>null,'social_link'=>null];
                    $this->get_db('App\Entity\social',$id,function(&$element)use(&$tmp){
                        $tmp['phone_id']=$element->getID();
                        $tmp['user_id']=$element->getUserID();
                        $tmp['social_type']=$element->getSocialType();
                        $tmp['social_link']=$element->getLink();
                    });
                    return $tmp;
                });
            }
            return $this->handleView($this->view($output));
        }
    }
}