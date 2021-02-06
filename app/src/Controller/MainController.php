<?php
// src/Controller/MainController.php
namespace App\Controller;
use App\Entity\user;
use App\Entity\email;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validator\ValidatorInterface;
//use Doctrine\ORM\EntityManager;
use \Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\DBAL\Driver\Connection;

use App\Validator\validmin;
use App\Service\toolbox;
class MainController extends AbstractFOSRestController//AbstractController
{
    private  $entityManager;
    private $connection;
    private $tbx;
    private $paramfetcher;
    public function __construct(EntityManagerInterface $em, Connection $connection,toolbox $tb, ParamFetcherInterface $paramfetcher) {
      $this->entityManager = $em;
      $this->connection=$connection;
      $this->tbx=$tb;
      $this->paramfetcher=$paramfetcher;
    }
    //,"error_message" = "page must be an integer"
    /**
     * @QueryParam(name="page",requirements={"rule"="^\d+$","error_message" = "page must be an integer"},default=1,description="Page number of the result")
     * @QueryParam(name="amount",requirements="^\d+$",default=20,description="Amount of results from the page number")
     * @QueryParam(name="sort",requirements="^(asc|desc)$",default="asc",allowBlank=false,description="Sort direction")
     * @Route("/userz",name="userz",methods={"GET"})
     */
    public function users()
    {
        $amt=($this->paramfetcher->get('amount')<1)?1:$this->paramfetcher->get('amount');
        $start=(($this->paramfetcher->get('page')<1)?1:$this->paramfetcher->get('page')-1)*$amt;
        $tmp=$this->getDoctrine()->getRepository(user::class);
        $tmp=$tmp->findBy([],['created_date'=>'ASC']);
        //$this->tbx->getallusers();
        $qb=$this->entityManager->createQueryBuilder();
        //---------------------------------------------------------
        $rsm= new ResultSetMapping;
        $rsm->addEntityResult(user::class, 'u');
        $rsm->addFieldResult('u', 'id', 'id');
        $rsm->addFieldResult('u', 'name', 'name');
        $rsm->addFieldResult('u', 'active_status', 'active_status');
        $rsm->addFieldResult('u', 'updated_date', 'updated_date');
        $rsm->addFieldResult('u', 'created_date', 'created_date');

        $query=$this->entityManager->createNativeQuery('select * from users',$rsm);
        
        $query3=$this->connection->fetchAll('select * from users');
        $query4=$this->connection->fetchAll('select id,name,active_status,updated_date,created_date from users');//we dont need entities for pulling data off the database
        //$this->tbx->dbcall('select * from users');
        //$user=new user();
        //var_dump($query4);
        /*$rsm2= new ResultSetMapping; //join query
        $rsm2->addEntityResult(user::class, 'a');
        $rsm2->addFieldResult('a', 'a_id', 'id');
        $rsm2->addFieldResult('a', 'a_name', 'name');
        $rsm2->addFieldResult('a', 'active_status', 'active_status');
        $rsm2->addFieldResult('a', 'updated_date', 'updated_date');
        $rsm2->addFieldResult('a', 'created_date', 'created_date');
        $rsm2->addJoinedEntityFromClassMetadata (email::class,'b','a','b');
        $rsm2->addFieldResult('b', 'b_id', 'id');
        $rsm->addFieldResult('b', 'b_user_id', 'user_id');
        $rsm2->addFieldResult('b', 'email', 'email');
        $query2=$this->entityManager->createNativeQuery('select a.id as a_id,a.name as a_name,a.active_status,a.updated_date,a.created_date,b.user_id as b_user_id,b.id as b_id,b.email from users a left join user_emails b on a.id=b.user_id',$rsm2);
*/



        //var_dump();
        $view=$this->view(['Status'=>1,'Error'=>[],'Message'=>'Found','Data'=>$tmp,'data2'=>$this->getDoctrine()->getRepository(user::class)->createQueryBuilder('users')->getQuery()->execute(),
     //   'data3'=>$qb->select('a.id','a.name','a.active_status')->from(user::class,'a')->orderBy('a.created_date','DESC')->getQuery()->getResult(),
       // 'date4'=>$qb->select('b.id','group_concat(e.id)','group_concat(e.email)')->from(user::class,'b')->leftJoin(email::class,'e','with','b.id=e.user_id')->groupBy('e.user_id')->getQuery()->getResult()//
       'date4'=>$qb->select('distinct(e.user_id) as user_id','group_concat(e.email) as email','b.name as name','min(b.id) as id')->from(user::class,'b')->leftJoin(email::class,'e','with','b.id=e.user_id')->groupBy('e.user_id,b.name')->getQuery()->getResult(),
        'data5'=>$query->getResult(),
        //'data6'=>$query2->getResult()
        'data7'=>$query3,
        'date8'=>$query4,
        'date9'=>$this->tbx->dbcall('select * from users limit '.(($this->paramfetcher->get('page')-1)*$this->paramfetcher->get('amount')).','.$this->paramfetcher->get('amount'),[1]),
        'page'=>$this->paramfetcher->get('page'),
        'amount'=>$this->paramfetcher->get('amount'),
        'start_index'=>$start,
        'sql'=>'select * from users limit '.$start.','.$amt
        //not possible to do unions in doctrine
        

/*select distinct(b.user_id),group_concat(b.email),a.name,min(a.id) from users a left join user_emails b on a.id=b.user_id group by a.name,b.user_id union all select distinct(b.user_id),group_concat(b.email),a.name,min(a.id) from users a right join user_emails b on a.id=b.user_id where a.id is null group by a.name,b.user_id;*/

        ]);
        return $this->handleView($view);
    }
    /**
     * @Route("/{page<\d+>?null}",name="index",methods={"GET"})
     */
    public function index($page,database $database): Response
    {
        $number = random_int(0, 100);
       /* return $this->render('index.html.twig',[
            'testing'=>"This is a real test right here :)"
        ]);*/

        //$tmp=//->getManager();
        //var_dump($tmp);


        $view=$this->view(['User'=>'Not a user','Message'=>'This is a index function right now','test'=>$database->getallusers()]);
        return $this->handleView($view);
       // return new Response(
      //      '<html><body>Lucky number: '.$number.'</body></html>'
      //  );
    }
    /**
    *   @Route("/user12321/{id<\d+>?}",name="userx",methods={"GET"})
    **/
    public function show($id)
    {
        throw new HttpException(400, "This is a serious error");
        $tmp=['User'=>'Jack','Message'=>"This is a test right here right now :)","id"=>$id];
        return $this->handleView($this->view($tmp));
        //return $this->json($tmp);
    }
}