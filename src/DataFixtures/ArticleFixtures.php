<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        // La librairie FAKER permet d'inserer en bdd des fausses donnes (fixtures), il genere via differentes methodes, des donnes
        $faker = \Faker\Factory::create('fr=FR');//gener des nom et des mots en français

        //création de 3 catégories
        for ($i = 1; $i <= 3 ; $i++)
        {  // on intancie l'entité Category afin d' insérer des categories dans la bdd
            $category = new Category;
            // on appel les setteurs de l'objet $category
            //sentence():methode issue de l'objet $faker
            $category->setTitle($faker->sentence())
                    ->setDescription($faker->paragraph());

            $manager->persist($category);

            //Création entre 4 a 6 articles par catégorie

            for($j = 1; $j <= mt_rand(4,6); $j++)
            {

            $article = new Article;

            $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';

            $article->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setImage($faker->imageUrl())
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setCategory($category);

            $manager->persist($article);

            // Création entre 4 et 10 commentaires par article
            for($k = 1; $k <= mt_rand(4,10); $k++)
            {
                $comment = new Comment;

                $content = '<p>' . join($faker->paragraphs(2), '</p><p>') . '</p>';

                $now = new \DateTime;
                $interval = $now->diff($article->getCreatedAt());//représente le temps en timestamp entre la date de création 
                // de l'article  et maintenant

                $days = $interval->days; // nombre de jour entre la date de création de l'article et maintenant
                $minimum ='-' .$days . 'days';

                $comment->setAuthor($faker->name)
                        ->setContent($content)
                        ->setCreatedAt($faker->dateTimeBetween($minimum))
                        ->setArticle($article);
                
                $manager->persist($comment);
            }
        }
    }
        
     $manager->flush();   

    }
}
