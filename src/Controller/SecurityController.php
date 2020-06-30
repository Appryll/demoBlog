<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     * 
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {

        //por insérer dans lq table SQL User, nous devons intancier un objet issu de l' Entity User
        //l' entité User refléte la table SQL user
        $user = new User;
        
        //on appel la classe RegistrationType afin de créer le formulaire d'inscription
        $form = $this->createForm(RegistrationType::class, $user);

        dump($request);///para saber si recupera bien toda la informacion

        //handleRequest recupére toutes les données saisies dans le formularie et les envois directement dans les setteurs de l'objet $user
        $form->handleRequest($request);

        //si le formulaire q bien été validé (isSu,itted) et aue les setteurs de $user sont correctement remplie; qlogrs on entre dqns le IF
        if($form->isSubmitted() && $form->isValid())
        {
            //on trqns,et là la méthode encodePqssword() de l'interface UsserPqsswordEncoderInterface le mot de passe du formulaire q encoder
            //$hash contien le mot de passe endocé
            $hash=$encoder->encodePassword($user, $user->getPassword());

            //on transmet le MDP encodé qu setteur de l'objet User
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('Success', 'Félicitations !! Vous étes maintenant inscrit, vous pouvez maintenant vous connecter.');

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/registration.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    /**
     *@Route("/connexion", name="security_login")
     */
    public function login()
    {
        return $this->render('security/login.html.twig');
    }
    

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout()
    {
        //cette méthode ne retourne rien; il nous suffit d'avoir une route pour la deconnexion
    }
}
