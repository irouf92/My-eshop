<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Form\RegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
  #[Route('/inscription', name: 'user_register', methods: ['GET', 'POST'])]
  public function register(
    Request $request,
    EntityManagerInterface $entityManager,
    UserPasswordHasherInterface $passwordHasher
  ): Response {

    # Cette condition permet de détecter si un utilisateur est connecté.
        # Si oui, alors l'utilisateur est redirigé.
        # Cela interdit l'inscription à un user connecté.

   if ($this->getUser()) {
      return $this->redirectToRoute('default_home');
  }
    #instanciation
    $user = new User();

    # 2 - Création du formulaire + mécanisme d'auto-hydratation

    $form = $this->createForm(RegisterFormType::class, $user)->handleRequest($request);

    #Au clic du boutton "validé"
    if ($form->isSubmitted() && $form->isValid()) {

      #Set des proprietes qui ne sont pas dans le formulaire
      $user->setCreatedAt(new DateTime());
      $user->setUpdatedAt(new DateTime());
      #La propriete "role" est un array [tablaeu]
      $user->setRoles(['ROLE_USER']);

      #Nous devons resetter manuellement le password car par defaut il n est pas haché
      #Pour cela nous devons utiliser une methode de hachage, appellée hashPassword() :
      # => cette méthode prend 2 arguments : $user, $plainPassword


      $user->setPassword(
        $passwordHasher->hashPassword(
          $user,
          $form->get('password')->getData()
        )
      );

      $entityManager->persist($user);
      $entityManager->flush();

      # La méthode addFlash() nous permet d'ajouter des messages destinés à l'utilisateur.
      # On pourra tous les afficher en front (avec Twig)
      $this->addFlash('success', 'Votre inscription a été effectué avec succés !');
      return $this->redirectToRoute('default_home');
    } //end if

    # 3 - Rendu de la vue Twig, avec le formulaire

    return $this->render('register/form.html.twig', [
      'form' => $form->createView() # createView() permet de générer le HTML pour l'affichage

    ]);
  }
}
