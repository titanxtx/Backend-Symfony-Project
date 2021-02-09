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


class GetData extends AbstractFOSRestController{
    private $tbx;
    private $paramfetcher;
    function __construct(toolbox $tb, ParamFetcherInterface $paramfetcher){
        $this->tbx=$tb;
        $this->paramfetcher=$paramfetcher;
    }
    private function get_page(&$start,&$amt)
    {
        $amt=($this->paramfetcher->get('amount')<1)?1:$this->paramfetcher->get('amount');
        $start=(($this->paramfetcher->get('page')<1)?1:$this->paramfetcher->get('page')-1)*$amt;
    }
    private function filter_sql(array &$paramx,&$in,&$in2,int $start,int $amt):string //The sql statement is made here
    {
    //"select (z.result) as result from (select JSON_OBJECT('user_id',w.id,'name',w.name,'emails:',m.emails,'phone_numbers',m.phone,'social_media',m.social,'active_status',w.active_status,'updated_date',updated_date,'created_date',created_date) as result from users w left join (select * from (select a.id as idx,if(count(b.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('email_id',b.id,'email',b.email))) as emails from users a left join user_emails b on a.id=b.user_id group by a.id,a.name,b.user_id order by a.id asc ) r left join (select c.id as idz,if(count(d.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('phone_id',d.id,'phone_number',d.phonenumber))) as phone from users c left join user_phone d on c.id=d.user_id group by c.id,c.name,d.user_id order by c.id asc) s on r.idx=s.idz left join (select c.id,if(count(d.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('social_id',d.id,'socialmedia_type',if(d.facebook=1,'Facebook',if(d.twitter=1,'Twitter','Instagram')),'socialmedia_link',d.link))) as social from users c left join user_socialmedia d on c.id=d.user_id group by c.id,c.name,d.user_id order by c.id asc) t on t.id=s.idz ) m on w.id=m.idx order by w.id asc limit {$start},{$amt}) z";
    if(is_null($paramx['only'])) return "select (z.result) as result from (select JSON_OBJECT('user_id',w.id,'name',w.name,'emails:',m.emails,'phone_numbers',m.phone,'social_media',m.social,'active_status',w.active_status,'updated_date',updated_date,'created_date',created_date) as result from users w left join (select * from (select a.id as idx,if(count(b.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('email_id',b.id,'email',b.email))) as emails from users a left join user_emails b on a.id=b.user_id group by a.id,a.name,b.user_id order by a.id asc ) r left join (select c.id as idz,if(count(d.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('phone_id',d.id,'phone_number',d.phonenumber))) as phone from users c left join user_phone d on c.id=d.user_id group by c.id,c.name,d.user_id order by c.id asc) s on r.idx=s.idz left join (select c.id,if(count(d.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('social_id',d.id,'socialmedia_type',if(d.facebook=1,'Facebook',if(d.twitter=1,'Twitter','Instagram')),'socialmedia_link',d.link))) as social from users c left join user_socialmedia d on c.id=d.user_id group by c.id,c.name,d.user_id order by c.id asc) t on t.id=s.idz ) m on w.id=m.idx ".((!empty($in)||!empty($in2))?" where ":" ").((!empty($in))?"w.id in (".$in.")":"").((!empty($in)&&!empty($in2))?' or':'').((!empty($in2))?' w.name in ('.$in2.')':'')." order by w.id asc limit {$start},{$amt}) z";
    else {
            $val=['user_id'=>['type'=>0,'data'=>'w.id'],'name'=>['type'=>0,'data'=>'w.name'],'active_status'=>['type'=>0,'data'=>'w.active_status'],
            'updated_date'=>['type'=>0,'data'=>'a.updated_date'],'created_date'=>['type'=>0,'data'=>'a.created_date'],'emails'=>['type'=>1,'data'=>'m.emails','table_alias'=>'r','id'=>'idx','join'=>"(select a.id as idx,if(count(b.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('email_id',b.id,'email',b.email))) as emails from users a left join user_emails b on a.id=b.user_id group by a.id,a.name,b.user_id order by a.id asc )"],
            'phone_numbers'=>['type'=>1,'data'=>'m.phone','table_alias'=>'s','id'=>'idz','join'=>"(select c.id as idz,if(count(d.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('phone_id',d.id,'phone_number',d.phonenumber))) as phone from users c left join user_phone d on c.id=d.user_id group by c.id,c.name,d.user_id order by c.id asc)"],
            'social_media'=>['type'=>1,'data'=>'m.social','table_alias'=>'t','id'=>'id','join'=>"(select c.id,if(count(d.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('social_id',d.id,'socialmedia_type',if(d.facebook=1,'Facebook',if(d.twitter=1,'Twitter','Instagram')),'socialmedia_link',d.link))) as social from users c left join user_socialmedia d on c.id=d.user_id group by c.id,c.name,d.user_id order by c.id asc)"]];//regular columns   check for join columns emails later manually
            $only=[];
            foreach(explode(',',$paramx['only']) as $x) $only[strtolower($x)]=1;//turning the only parameter value into a associative array
            $joins=[];
            $columns=[];
            $last_index=null;
            $last_join_id=null;
            foreach(array_keys($only) as $x)//we are getting the keys of the only parameter
            {
                array_push($columns,"'{$x}',{$val[$x]['data']}");
                 if($val[$x]['type']==1)
                 {
                    array_push($joins,((count($joins)==0)?'select * from ':' left join ').$val[$x]['join']." {$val[$x]['table_alias']}".((!is_null($last_join_id))?" on {$val[$x]['table_alias']}.{$val[$x]['id']}={$last_join_id}":''));
                    $last_join_id="{$val[$x]['table_alias']}.{$val[$x]['id']}";
                    $last_index=$val[$x]['id'];
                 }
                
            }
            $sqlstr= "select (z.result) as result from (select JSON_OBJECT(".implode(',',$columns).") as result from users w ".((!empty($joins))?'left join ('.implode(' ',$joins).") m on m.{$last_index}=w.id":'').' '.((!empty($in)||!empty($in2))?" where ":" ").((!empty($in))?"w.id in (".$in.")":"").((!empty($in)&&!empty($in2))?' or':'').((!empty($in2))?' w.name in ('.$in2.')':'')." order by w.id asc limit {$start},{$amt}".') z';
            return $sqlstr;
        }
    }

    /**
     * @QueryParam(name="only",requirements={@Assert\Regex("/^(?:(?:user_id|name|active_status|emails|email_id|email_address|phone_numbers|social_media|updated_date|created_date),?)+$/mi")},nullable=true,strict=true,allowBlank=false,default=null,description="Page number of the result")
     * @QueryParam(name="page",requirements={@Assert\Regex("/^\d+$/m"),@Assert\GreaterThanOrEqual(1)},nullable=true,strict=true,allowBlank=false,default=1,description="Page number of the result")
     * @QueryParam(name="amount",requirements={@Assert\Regex("/^\d+$/m"),@Assert\Range(min=1,max=100)},nullable=true,strict=true,allowBlank=false,default=20,description="Amount of results from the page number")
     * @QueryParam(name="sort",requirements="^(asc|desc)$",nullable=true,strict=true,allowBlank=false,default="asc",description="Sort direction")
     * @Get("/users",name="users",methods={"GET"})
     */
    public function users()
    {
        $this->get_page($start,$amt);
        //$sql="select JSON_OBJECT('user_id',a.id,'name',a.name,'active_status',active_status,'updated_date',a.updated_date,'created_date',a.created_date,'emails',if(count(b.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('email_id',b.id,'email',b.email)))) as result from users a left join user_emails b on a.id=b.user_id group by a.id,a.name,a.updated_date,a.created_date,a.active_status,b.user_id limit ".$start.','.$amt;        
        $paramx=$this->paramfetcher->all();
        $sql=$this->filter_sql($paramx,$in,$in2,$start,$amt);
        $data=$this->tbx->dbcall($sql,[],1);
        if(!is_null($data))
        {
            return $this->handleView($this->view([
                'code'=>200,
                'Message'=>"Success",
                'users'=>$data
            ]));
        }
        else{
            return $this->handleView($this->view([
                'code'=>400,
                'Message'=>"Failed",
                'users'=>[]
            ]));
        }
        
    }
    /**
     * @QueryParam(name="only",requirements={@Assert\Regex("/^(?:(?:user_id|name|active_status|emails|email_id|email_address|phone_numbers|social_media|updated_date|created_date),?)+$/mi")},nullable=true,strict=true,allowBlank=false,default=null,description="Page number of the result")
     * @QueryParam(name="page",requirements={@Assert\Regex("/^\d+$/m"),@Assert\GreaterThanOrEqual(1)},nullable=true,strict=true,allowBlank=false,default=1,description="Page number of the result")
     * @QueryParam(name="amount",requirements={@Assert\Regex("/^\d+$/m"),@Assert\Range(min=1,max=100)},nullable=true,strict=true,allowBlank=false,default=20,description="Amount of results from the page number")
     * @QueryParam(name="name",requirements="^(?:[^,]+,?)+$",nullable=true,default=null,strict=true,allowBlank=false,description="name to get users with")
     * @QueryParam(name="user_id",requirements={@Assert\Regex("/^(?:\d+,?)+$/m")},strict=true,default=null,allowBlank=false,description="Users to get can be a single number or an array 1,2,3,4,5")
     * @QueryParam(name="sortby",requirements={@Assert\Regex("/^(?:user_id|name|created_date|updated_date|email_amt)$/mi")},nullable=true,strict=true,allowBlank=false,default="user_id",description="Sort by what data")
     * @QueryParam(name="order",requirements="^(asc|desc)$",nullable=true,strict=true,allowBlank=false,default="asc",description="Sort order")
     * @Get("/user",name="user",methods={"GET"})
     */
    function user()
    {
        $this->get_page($start,$amt);
        $paramx=$this->paramfetcher->all();
        $db_vals=[];
        $in=null;
        $in2=null;
        if(!is_null($paramx['user_id'])) $db_vals=array_merge($db_vals,$this->tbx->split_generate_placeholders($paramx['user_id'],$in));//split and generate placeholders for sql statement
        if(!is_null($paramx['name']))  $db_vals=array_merge($db_vals,$this->tbx->split_generate_placeholders($paramx['name'],$in2));
        $sql=$this->filter_sql($paramx,$in,$in2,$start,$amt);
        $data=$this->tbx->dbcall($sql,$db_vals,1);
        if(!is_null($data))
        {
            return $this->handleView($this->view([
                'code'=>200,
                'Message'=>"Success",
                'users'=>$data
            ]));
        }
        else{
            return $this->handleView($this->view([
                'code'=>400,
                'Message'=>"Failed",
                'users'=>[]
            ]));
        }
        
    }
   //@Assert\All({@Assert\Range(min=1,max=100),@Assert\Regex("/^\d+$/")})  @Assert\All(@Assert\Range(min=1,max=100))
    /**
     * @QueryParam(name="only",requirements={@Assert\Regex("/^(?:(?:user_id|name|active_status|emails|email_id|email_address|updated_date|created_date),?)+$/mi")},nullable=true,strict=true,allowBlank=false,default=null,description="Page number of the result")
     * @Get("/user/{idx}",name="idx",requirements={"idx"="^[1-9]\d*$"},methods={"GET"})
     */
    function user_id($idx)//  /user/id
    {
        $output=$this->tbx->validate_params(['id'=>$idx]);
        if(count($output)!=0) throw new HttpException(500, "{$idx} - Invalid id");
        $paramx=['only'=>$this->paramfetcher->get('only')];
        $in='?';
        $sql=$this->filter_sql($paramx,$in,$in2,1,1);
        $data=$this->tbx->dbcall($sql,[$idx],1);
        if(!is_null($data))
        {
            return $this->handleView($this->view([
                'code'=>200,
                'message'=>'Success',
                'users'=>$data
            ]));
        }
        else{
            return $this->handleView($this->view([
                'code'=>400,
                'message'=>'Failed',
                'users'=>$data
            ]));
        }
        
    }
}

/*select distinct(b.user_id),group_concat(b.email),a.name,min(a.id)      from users a left join user_emails b on a.id=b.user_id group by a.name,b.user_id union all select distinct(b.user_id),group_concat(b.email),a.name,min(a.id) from users a right join user_emails b on a.id=b.user_id where a.id is null group by a.name,b.user_id;*/
/*
// $this->tbx->dbcall($sql);//'select * from users left limit '.$start.','.$amt
       //select JSON_OBJECT('user_id',distinct(b.user_id),'emails',JSON_ARRAYAGG(b.email) as emails,'id',a.name,min(a.id) as id) from users a left join user_emails b on a.id=b.user_id group by a.name,b.user_id
       //select JSON_ARRAYAGG(JSON_OBJECT('id',id,'name',name,'updated_date',updated_date,'created_date',created_date)) as result,count(*) as amt from users;
        //select JSON_ARRAYAGG(JSON_OBJECT('id',a.id,'name',a.name,'updated_date',a.updated_date,'created_date',a.created_date,'emails',b.email)) as result,count(*) as amt from users a left join user_emails b on a.id=b.user_id;
        //select JSON_ARRAYAGG(JSON_OBJECT('emails',JSON_ARRAYAGG(b.email),'id',min(a.id),'updated_date',a.updated_date,'created_date',a.created_date)) as result,count(*) as amt from users a left join user_emails b on a.id=b.user_id group by a.name,b.user_id;
        //'emails',JSON_ARRAYAGG(b.email) 'name',distinct(a.name),
        //select JSON_OBJECT('emails',JSON_ARRAYAGG(JSON_OBJECT('email_id',b.id,'email',b.email)),'id',min(a.id)) as result,count(*) as amt from users a left join user_emails b on a.id=b.user_id group by a.name,b.user_id;
        //select JSON_OBJECT('name',a.name,'active_status',active_status,'updated_date',a.updated_date,'created_date',a.created_date,'emails',if(count(b.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('email_id',b.id,'email',b.email))),'id',min(a.id)) as result,count(*) as amt from users a left join user_emails b on a.id=b.user_id group by a.name,a.updated_date,a.created_date,a.active_status,b.user_id;
*/