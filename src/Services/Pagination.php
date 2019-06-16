<?php 

namespace App\Services;

use Twig\Environment;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;

Class Pagination {

    //1- Utiliser la pagination à partir de n'importe quelle entité / on devra préciser l'entité concernée

    private $entityClass;
    private $limit=10;
    private $page=1;

    private $manager;

    private $twig;

    private $templatePath;

    public function __construct(ObjectManager $manager, Environment $twig, RequestStack $request,$templatePath){

        $this->manager = $manager;

        $this->twig = $twig;

        $this->route = $request->getCurrentRequest()->attributes->get('_route');
   
        $this->templatePath = $templatePath;
    }

    public function display(){
        //appel le moteur twig et on précise quelle template on veut utiliser

        $this->twig->display($this->templatePath,[
            //options necessaire à l'affichage des données
            //variables : route / page / pages

            'page'=>$this->page,
            'pages'=>$this->getPages(),
            'route'=>$this->route
        ]);
    }


    public function setEntityClass($entityClass){
         
        // ma donnée entityClass = donnée qui va m'être envoyé

        $this->entityClass = $entityClass;

        return $this;
    }

    public function getEntityClass(){

        return $this->entityClass;
    }

    //2- Quelle est la limite

    public function setLimit($limit){

        $this->limit = $limit;

        return $this;
    }

    public function getLimit(){

        return $this->limit;
    }

    //3- Sur quelle page je me trouve actuellement

    public function setPage($page){

        $this->page=$page;

        return $this;
    }

    public function getPage(){
        return $this->page;
    }

    //4-on va chercher le nb total de pages

    public function getData(){
        //calculer l'offset

        $offset = ($this->page * $this->limit) - $this->limit;

        // demande au repository de trouver les éléments
        // on va charcher le bon repository

        $repo = $this->manager->getRepository($this->entityClass);

        //on construit notre requete

        $data = $repo->findBy([],[],$this->limit,$offset);

        return $data;
    }

    public function getPages(){

        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());

        //ceil 3.8=>4 ou 2.2=>2

        $pages = ceil($total/$this->limit);

        return $pages;
    }


    public function getRoute(){

        return $this->route;

    }

    public function setRoute($route){

        $this->route = $route;
        return $this;

    }

    public function getTemplatePath(){
        return $this->templatePath;
    }

    public function setTemplatePath($templatePath){

        $this->templatePath = $templatePath;

        return $this;
    }





}




?>