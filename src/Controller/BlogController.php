<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
     * @Route("/blog/{id}/edit", name="blog_edit")
     */                             // 12
    public function form(Article $article = null, Request $request, EntityManagerInterface $manager)
    {
        /* de base el articulo esta vacio si no es nulo de base va a haber errores
            article para buscar el id y lo envie en el url
            
            La classe Request est une classe prédéfinie en Symfony qui stocke toute les données véhiculées par les superglobales ($_POST, $_COOKIE, $_SERVER etc...)
            Nous avons accès aux données saisie dans le formulaire via l'objet $request
            La propriété '$request->request' représente la superglobale $_POST, les données saisies dans le formulaire sont accesssible via cette proprité
            Pour insérer un nouvel article, nous devons instancier la classe / Entitée Article pour avoir un objet Article vide, afin de renseigner tous les setteurs de l'objet $article

            EntityManagerInterface est une interface prédéfinie en Symfony qui permet de manipuler les lignes de la BDD (INSET, UPDATE, DELETE). Elle possède des méthodes permettant de péparer et d'executer les requetes SQL (persist() | flush())

            persist() est une méthode issue de l'interface EntityManagerInterface qui pemret de préparer et sticker la requete SQL
            flush() est une méthode issue de l'interface EntityManagerInterface qui permet de libérer et d'executer la requete SQL

            redirectToRoute() est une méthode prédéfinie en Symfony qui permet de rediriger vers une route spécifique, dans notre cas on redirige après insertion vers la route 'blog_show' (détail de l'article que l'on vient d'insérer) et on transmet à la méthode l'id de l'article a envoyer dans l'URL

            get() : méthode de l'objet $request qui permet de récupérer les données saisie aux différents indices 'name' du formulaire
        */  

        // dump($request);

        // if($request->request->count() > 0)
        // {
        //     $article = new Article;

        //     $article->setTitle($request->request->get('title'))
        //             ->setContent($request->request->get('content'))
        //             ->setImage($request->request->get('image'))
        //             ->setCreatedAt(new \DateTime());

        //     $manager->persist($article);
        //     $manager->flush();

        //     dump($article);

        //     return $this->redirectToRoute('blog_show', [
        //         'id' => $article->getId()
        //     ]);
        // }

        /*
            ->add('title', TextType::class, [
                    'attr' => [
                        'placeholder' => "Saisir le titre de l'article",
                        'class' => "col-md-6 mx-auto"
                    ]
                ])

                $article->setTitle("Titre à  la con")
                        ->setContent("Contenu à la con");
        */
        //Si l'article n' est pas existant, n'est pas dèfinit, qu'il es NuLL, cela veut dire qu' aucun ID n' a été trans,i dans ll' URL,
        // donce c'est une insertion, alors on instanie la classe Article afin d' avoir un objet vide.
        //on entre dans la condition suelement dans le cas d' une insertion d' un article

        if(!$article)
        {
            $article = new Article;
        }
        
        // $form = $this->createFormBuilder($article) /*/para crear campos*/
        //              ->add('title')
        //              ->add('content')
        //              ->add('image')
        //              /*->add('save', SumitType::class) button pero no puedo cambiar el label */
        //              ->getForm();

         //On importe  la classe permettant de créer le formulaire d'ajout / modification d'article (ArticleType)
        //On envoi en 2éme argument l'objet $article pour bien spécifier que le formulaire est destiné à remplir l'objet $article
        $form = $this->createForm(ArticleType::class, $article);

        //La méthode handleRequest() permet de récupérer toutes les valeurs du formulaire contenu dans $request ($_POST) afin de les 
        // directement dans less setteurs de l'objets $article
        $form->handleRequest($request);

        // Si le formulaire q bien été soumis, que l'on a cliqué sur la bouton de validation 'sublit' et que tout est bien validé, c'est à 
        // dire que chaque valeur du formulaire q bien été envoyé dans les bons setteurs, alors on entre dans la condition IF
 
        if($form->isSubmitted() && $form->isValid())
        {


            
            if(!$article->getId())
            { 
                $article->setCreatedAt(new \DateTime);
            }

            $manager->persist($article);
            $manager->flush();

            dump($article);

            return $this->redirectToRoute('blog_show', [
                'id' => $article->getId()
            ]);
        }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article-> getId() !== null
        ]);
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
               
        



