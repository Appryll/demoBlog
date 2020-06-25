<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use DateTime o puedo usar \antes de DateTime

class BlogController extends AbstractController
{
    /*
        $ctrl = new BlogController;

        $ctrl->index()

        Symfony fonctionne toujours avec un système de routage. Une méthode d'un controlleur sera executée en fonction de la route transmise dans l'URL.
        ex : Si nous envoyons la route '/blog' dans l'url (http://localhost:8000/blog), cela fait appel au controller 'BlogController' et execute la méthode 'index()'. Cette méthode renvoi un template sur le navigateur (méthode render())
        Symfony se sert des annotations (@Route())
        Les annotations doivent toujours contenir 4 astérix
    */

    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
    {
        /*
            Un des principes de principe de base de Symfony est l'injection de dépendances.
            Par exemple, ici dans le cas de la méthode index(), cette a besoin de la classe ArticleRepository pour fonctionner correctement, c'est à dire que la méthode index() dépend de la classe ArticleRepository
            Donc ici on injecte une dépendance en argument de la méthode index(), on impose un objet issu de la classe ArticleRepository
            Du coup plus besoin de faire appel à Doctrine (getDoctrine())
            $repo est un objet issu de la classe ArticleRepository et nous avons accès à toute les méthodes issues de cette classe
            Les méthodes sont moins chargé et c'est plus simple d'utilisation 

            Pour selectionner des données en BDD, nous avons besoin de la classe Repository de la classe Article
            Une classe Repository permet uniquement de selectionner des données en BDD (requete SQL SELECT)
            On a besoin de l'ORM DOCTRINE pour faire la relation entre la BDD et le controller (getDoctrine())
            getRepository() : méthode issue de l'objet DOCTRINE qui permet d'importer une classe Repository (SELECT)

            $repo est un objet issu de la classe ArticleRepository, cette contient des méthodes prédéfinies par SYMFONY permettant de selectionner des données en BDD (find(), findBy(), FindOneBy(), findAll())

            findAll() est une méthode issue de la classe ArticleRepository permetttant de selectionner l'ensemble de la table SQL, donc ici la table Article
        */

        // $repo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $repo->findAll(); // équivalent à SELECT * FROM Article + FETCH_ALL

        dump($articles); // equivalent de var_dump() / print_r()

        return $this->render('blog/index.html.twig', [
            'articles' => $articles  // on envoie sur le template 'index.html.twig' les articles selectionnés en BDD ($articles) que nous allons traiter avec le langage TWIG sur le template
        ]);
    }

        /*       
                Fixtures
             doctrine (select)
                BDD  <_______  
                |             |           $article  FRONT
                |              CONTROLLER _______ > libère les templates + données BDD sur la navigateur
                |____________>|
                    doctrine (resultat requete)
        */

    // home() : méthode 

    /**
     * @Route("/", name="home") 
     */
    public function home()
    {
        return $this->render('blog/home.html.twig', [
            'title' => 'Bienvenue sur le blog Symfony',
            'age' => 25
        ]);
    }

    // create() : méthode permettant d'insérer un nouvel article en BDD

    /**
     * @Route("/blog/new", name="blog_create")
     */
    public function create(Request $request, EntityManagerInterface $manager) 
    {
        dump($request);

        if($request->request->count()>0)
        {
            $article = new Article;

            $article->setTitle($request->request->get('title'))
                    ->setContent($request->request->get('content'))
                    ->setImage($request->request->get('image'))
                    ->setCreatedAt(new \DateTime());

            $manager->persist($article);
            $manager->flush();

        }
        return $this->render('blog/create.html.twig');
    }
    

    // show() : méthode permettant de voir le détail d'un article
                     ///blog/1
    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(ArticleRepository $repo, $id) // id 1 
    {
        /*
            show(ArticleRepository $repo) --> $repo c'est une variable de reception que nous nommons à souhait et quireceptionne un objet issu de la classe ArticleRepository

            Pour selectionner 1 article en BDD, c'est à dire voir le détail d'1 article, nous utilisons le principe de route paramétrée ("/blog/{id}"), notre route attends un paramètre de type {id}, donc l'id d'1 article qui est stocké en BDD
            Lorsque nous tranmettons dans l'URL une route par exempl "/blog/9", on envoi un id connu dans l'URL, Symfony va automatiquement recupéré ce paramètre pour le transmettre en argument de la méthode show($id)
            Cela veut dire que nous avons accès à l'{id} à l'intérieur de la méthode show()
            Le but est de selectionner les données en BDD de l'{id} récupéré en paramètre
            Nous avons besoin pour cela de la classe ArticleRepository afin de pouvoir selectionner en BDD
            La méthode find() est issue de la classe ArticleRepository et permet de selectionner des données en BDD avec un argument de type {id}
            DOCTRINE fait ensuite tout le travail pour nous, c'est à dire qu'elle recupère la requete de selection pour l'executer en BDD et elle retourne le resultat au controller

            $repo est un objet issu de la classe ArticleRepository, cette contient des méthodes prédéfinies par SYMFONY permettant de selectionner des données en BDD (find(), findBy(), FindOneBy(), findAll())
        */

        // $repo = $this->getDoctrine()->getRepository(Article::class);

        $article = $repo->find($id); // id 1 en argument de la méthode

        dump($article);

        return $this->render('blog/show.html.twig', [
            'article' => $article // envoi de l'article id 1 sur le template 'show.html.twig', on envoie avec le template 'show.html.twig' l'article selectionné en BDD 
        ]);

        // Arguments render('template_a_envoyer', 'ARRAY options')
    }

}
               
        



