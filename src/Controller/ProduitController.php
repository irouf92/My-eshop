<?php

namespace App\Controller;

use DateTime;
use App\Entity\Produit;
use App\Form\ProduitFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin')]
class ProduitController extends AbstractController
{
    #[Route('/voir-les-produits', name: 'show_produits', methods: ['GET'])]
    public function showProduits(EntityManagerInterface $entityManager): Response
    {

        $produits = $entityManager->getRepository(Produit::class)->findBy(['deletedAt' => null]);

        return $this->render('admin/produit/show_produits.html.twig', [
            'produits' => $produits

        ]);
    }
#[Route('/voir-les-archives', name: 'show_trash', methods: ['GET'])]
    public function showTrash(EntityManagerInterface $entityManager): Response
{
return $this->render('admin/produit/show_trash.html.twig');
}

    #[Route('/ajouter-un-produit', name: "create_produit", methods: ['GET', 'POST'])]
    public function createProduit(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $produit = new Produit();

        $form = $this->createForm(ProduitFormType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $produit->setCreatedAt(new DateTime());
            $produit->setUpdatedAt(new DateTime());

            $photo = $form->get('photo')->getData();

            if ($photo) {
                  //Méthode créee par nous même pour réutiliser du code (create() et update())
                $this->handleFile($produit, $photo, $slugger);
            } //end if $photo

            $entityManager->persist($produit);

            $entityManager->flush();

            $this->addFlash('success', 'Votre produit a été mis en ligne avec succès, bravo !! :) ');

            return $this->redirectToRoute('show_produits');
        } //end if $form

        return $this->render('admin/produit/create_produit.html.twig', [
            'form' => $form->createView()
        ]);

    } //end function create




    #[Route('/modifier-un-produit/{id}', name: 'update_produit', methods: ['GET', 'POST'])]
    function updateProduit(Produit $produit, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        #Récupération de la photo actuelle
        $originalPhoto = $produit->getPhoto();



        $form = $this->createForm(ProduitFormType::class, $produit, [
            'photo' => $originalPhoto
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produit->setUpdatedAt(new DateTime());
            $photo = $form->get('photo')->getData();

            if ($photo) {
                //Méthode créee par nous même pour réutiliser du code (create() et update())
                $this->handleFile($produit, $photo, $slugger);
            } else {
                $produit->setPhoto($originalPhoto);
            }

          $entityManager->persist($produit);
          $entityManager->flush();

          $this->addFlash('success', 'La modification est réussie !');
          return $this->redirectToRoute('show_produits');

        }//end if $form

        return $this->render('admin/produit/create_produit.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit
        ]);
    } //end function update

    #[Route('/archiver-un-produit/{id}', name: 'soft_delete_produit', methods: ['GET'])]
public function softDeleteProduit(Produit $produit, EntityManagerInterface $entityManager): RedirectResponse
 {
  $produit->setDeletedAt(new DateTime());  

  $entityManager->persist($produit);
  $entityManager->flush();

  $this->addFlash('success','le produit a bien ete archive !!');
  return $this->redirectToRoute('show_produits');
}




    private function handleFile(Produit $produit, UploadedFile $photo, SluggerInterface $slugger): void
    {
        # 1 - Déconstruire le nom du fichier

        # a - On récupère l'extension grâce à la méthode guessExtension()
        $extension = '.' . $photo->guessExtension();

        # 2 - Sécuriser lel nom et reconstruire le nouveau nom du fichier
        # a - On assainit le nom du fichier pour supprimer les espaces et les accents.
        // $safeFilename = $slugger->slug($photo->getClientOriginalName());
        $safeFilename = $slugger->slug($produit->getTitle());

        # b - On reconstruit le nom du fichier
        # uniquid() est une fonction native de PHP et génère u identifiant unique. 
        # 4 Cela évite les possibilités de doublons.
        $newFilename = $safeFilename . '_' . uniqid() . $extension;

        # 3 - Déplacer le fichier dans le bon dossier.
        # a- On utilise un try/catch lorsqu'une méthode "throws" (lance) une Exception (erreur)

        try {
            # On a défini un paramètre dans confi/service.yamlqui est le chemin du dossier "uploads".
            #On récupère la valeur avec getParameter() et le nom du paramètre.


            $photo->move($this->getParameter('uploads_dir'), $newFilename);
            $produit->setPhoto($newFilename);
        } 
        
        catch (FileException $exception) {
            $this->addFlash('warning', 'La photo du produit ne s\'est pas importée avec succès. Veuillez réessayer.');
            // return $this->redirectToRoute('create_produit');
        }
    } 




} //end class
