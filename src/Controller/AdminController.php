<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }



    /**
     * @Route("/admin/articles", name="admin_articles")
     */

     public function adminArticles(ArticleRepository $repo)
     {

        //on appele getManager() qui est le gestionnaire d'entités de Doctrine. Il est responsable de l'enrigestrement des objets et 
        //leure récuperation dans la base de donnés
        $em = $this->getDoctrine()->getManager(); //permite recuperer les donnes lies à la colonne


        //getClassMetadata() permet de recolter les métadonnées d'une table SQL (noms des champs; clé primaire, type de champs) à 
        //partir d' une entité / class
        //getFieldName() permet de recupérer les noms des champs / colonnes d' une table SQL à pqrtir d'une table
        $colonnes =$em->getClassMetadata(Article::class)->getFieldNames();
        
        $articles = $repo->findAll();

        dump($articles);
        dump($colonnes);

        return $this->render('admin/admin_articles.html.twig', [
            'articles' =>$articles,
            'colonnes' =>$colonnes
        ]);
     }

     /**
      * @Route ("/admin/{id}/edit-article", name="admin_edit_article")
      */

      public function editArticle(Article $article)
      {
          dump($article);

          $form = $this->createForm(ArticleType::class, $article);

          return $this->render('admin/edit_article.html.twig', [
              'formEdit'=> $form->createView()
          ]);
      }
}
