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
    private function get_params(array $list,$data=[])
    {
        foreach($list as $x)
        {
            array_push($data,$this->paramfetcher->get($x));
        }
        return $data;
    }
    
    private function filter_sql(array &$paramx,&$in,&$in2,$start,$amt):string
    {

        if(is_null($paramx['only'])) return "select JSON_OBJECT('user_id',a.id,'name',a.name,'active_status',active_status,'updated_date',a.updated_date,'created_date',a.created_date,'emails',if(count(b.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('email_id',b.id,'email',b.email)))) as result from users a left join user_emails b on a.id=b.user_id".((!empty($in)||!empty($in2))?" where ":" ").((!empty($in))?"a.id in (".$in.")":"").((!empty($in)&&!empty($in2))?' or':'').((!empty($in2))?' a.name in ('.$in2.')':'')." group by a.id,a.name,a.updated_date,a.created_date,a.active_status,b.user_id limit ".$start.','.$amt;        
        else {
            $val=['user_id'=>'a.id','name'=>'a.name','active_status'=>'a.active_status','updated_date'=>'a.updated_date','created_date'=>'a.created_date'];//check for emails later manually
            $only=[];
            foreach(explode(',',$paramx['only']) as $x) $only[strtolower($x)]=1;
            $joins=[];
            $groupby=[];
            $columns=[];
            if(array_key_exists('emails',$only))
            {
                foreach(array_keys($val) as $z)
                {
                    if(array_key_exists($z,$only)){
                        array_push($columns,"'{$z}',{$val[$z]}");
                        array_push($groupby,$val[$z]);
                    }
                }
            }
            else{
                foreach(array_keys($val) as $z){
                    if(array_key_exists($z,$only)){
                        array_push($columns,"'{$z}',{$val[$z]}");
                    }
                }
            }
            if(array_key_exists('emails',$only)){
                array_push($columns,"'emails',if(count(b.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('email_id',b.id,'email',b.email)))");
                array_push($joins,'left join user_emails b on a.id=b.user_id');
                array_push($groupby,'b.user_id');
            }
            return "select JSON_OBJECT(".implode(',',$columns).") as result from users a ".implode(' ',$joins).((!empty($in)||!empty($in2))?" where ":" ").((!empty($in))?"a.id in (".$in.")":"").((!empty($in)&&!empty($in2))?' or':'').((!empty($in2))?' a.name in ('.$in2.')':'')." ".((!empty($groupby))?'group by '.implode(',',$groupby):'')." limit ".$start.','.$amt;      
        }
    }
    /**
     * @QueryParam(name="only",requirements={@Assert\Regex("/^(?:(?:user_id|name|active_status|emails|email_id|email_address|updated_date|created_date),?)+$/mi")},nullable=true,strict=true,allowBlank=false,default=null,description="Page number of the result")
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
      //  $db_vals=[];
       // $db_vals=array_merge($db_vals,$this->tbx->split_generate_placeholders($paramx['id'],$in),$this->tbx->split_generate_placeholders($paramx['name'],$in2));//split and generate placeholders for sql statement
        $sql=$this->filter_sql($paramx,$in,$in2,$start,$amt);
        return $this->handleView($this->view([
            'code'=>0,
            'Message'=>"",
            'users'=>$this->tbx->dbcall($sql,[],1)
        ]));
    }
    /**
     * @QueryParam(name="only",requirements={@Assert\Regex("/^(?:(?:user_id|name|active_status|emails|email_id|email_address|updated_date|created_date),?)+$/mi")},nullable=true,strict=true,allowBlank=false,default=null,description="Page number of the result")
     * @QueryParam(name="page",requirements={@Assert\Regex("/^\d+$/m"),@Assert\GreaterThanOrEqual(1)},nullable=true,strict=true,allowBlank=false,default=1,description="Page number of the result")
     * @QueryParam(name="amount",requirements={@Assert\Regex("/^\d+$/m"),@Assert\Range(min=1,max=100)},nullable=true,strict=true,allowBlank=false,default=20,description="Amount of results from the page number")
     * @QueryParam(name="name",requirements="^(?:[^,]+,?)+$",nullable=true,default=null,strict=true,allowBlank=false,description="name to get users with")
     * @QueryParam(name="id",requirements={@Assert\Regex("/^(?:\d+,?)+$/m")},strict=true,default=null,allowBlank=false,description="Users to get")
     * @QueryParam(name="sort",requirements="^(asc|desc)$",nullable=true,strict=true,allowBlank=false,default="asc",description="Sort direction")
     * @Get("/user",name="user",methods={"GET"})
     */
    function user()
    {
        //var_dump('testing here right now');
        $this->get_page($start,$amt);
        $paramx=$this->paramfetcher->all();
        $db_vals=[];
        $db_vals=array_merge($db_vals,$this->tbx->split_generate_placeholders($paramx['id'],$in),$this->tbx->split_generate_placeholders($paramx['name'],$in2));//split and generate placeholders for sql statement
       $output=$this->tbx->validate_params($this->paramfetcher->all());
      //foreach($output as $x)
      // {
           //var_dump($x->getInvalidValue(),$x->getMessage());
       //}
       //var_dump(count($output));
//select JSON_OBJECT('name',a.name,'active_status',active_status,'updated_date',a.updated_date,'created_date',a.created_date,'emails',if(count(b.id)=0,JSON_ARRAY(),JSON_ARRAYAGG(JSON_OBJECT('email_id',b.id,'email',b.email))),'id',min(a.id)) as result,count(b.id) as amt from users a left join user_emails b on a.id=b.user_id where amt<3 group by a.name,a.updated_date,a.created_date,a.active_status,b.user_id

        
        $sql=$this->filter_sql($paramx,$in,$in2,$start,$amt);
        return $this->handleView($this->view([
            'code'=>200,
            'Message'=>"",
            //'sql'=>$sql,
           // 'user_str'=>$this->paramfetcher->get('id'),
            'user'=>$this->tbx->dbcall($sql,$db_vals,1)
        ]));
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
        return $this->handleView($this->view([
            'status'=>0,
            'error'=>[],
            'user'=>$this->tbx->dbcall($sql,[$idx],1)
        ]));
         //  
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