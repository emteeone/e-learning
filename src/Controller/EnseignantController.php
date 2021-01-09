<?php

namespace App\Controller;

use App\Entity\Enseignant;
use App\Form\EnseignantType;
use App\Repository\EnseignantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/enseignant")
 */
class EnseignantController extends AbstractController
{
    /**
     * @Route("/", name="enseignant_index", methods={"GET"})
     */
    public function index(EnseignantRepository $enseignantRepository): Response
    {
        return $this->render('enseignant/index.html.twig', [
            'enseignants' => $enseignantRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="enseignant_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $enseignant = new Enseignant();
        $form = $this->createForm(EnseignantType::class, $enseignant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // On récupère  l'image transmise
            $images = $form->get('images')->getData();
            
            // On génère le nom de fichier
            $fichier = $enseignant->getNom().'_'.uniqid().'.'.$images->guessExtension();
            
            // On copie le fichier dans le dossier public/uploads/enseiagant
            $images->move(
                $this->getParameter('images_enseignant'),
                $fichier
            );
            

            // on met le nom de la photo sur l'attribut photo
            $enseignant->setPhoto($fichier);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($enseignant);
            $entityManager->flush();

            return $this->redirectToRoute('enseignant_index');
        }

        return $this->render('enseignant/new.html.twig', [
            'enseignant' => $enseignant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="enseignant_show", methods={"GET"})
     */
    public function show(Enseignant $enseignant): Response
    {
        // le chemin pour enregistrer les images des enseignants
        $chemin = $this->getParameter('images_enseignant');

        return $this->render('enseignant/show.html.twig', [
            'enseignant' => $enseignant,
            'chemin' => $chemin
        ]);
    }

    /**
     * @Route("/{id}/edit", name="enseignant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Enseignant $enseignant): Response
    {
        // le chemin pour enregistrer les images des enseignants
        $chemin = $this->getParameter('images_enseignant');

        $form = $this->createForm(EnseignantType::class, $enseignant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On récupère  l'image transmise
            $images = $form->get('images')->getData();
            
            $img =  $enseignant->getPhoto();
            // On génère le nom de fichier
            $fichier = $enseignant->getNom().'_'.uniqid().'.'.$images->guessExtension();
            
            if($img!=$fichier){

                @unlink($chemin.$img) ;
                // On copie le fichier dans le dossier public/uploads/enseiagant
                $images->move(
                    $chemin,
                    $fichier
                );
                // on met le nom de la photo sur l'attribut photo
                $enseignant->setPhoto($fichier);
                }
            

            
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('enseignant_index');
        }

        return $this->render('enseignant/edit.html.twig', [
            'enseignant' => $enseignant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="enseignant_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Enseignant $enseignant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enseignant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            // le chemin pour enregistrer les images des enseignants
            $chemin = $this->getParameter('images_enseignant');
            $img = $enseignant->getPhoto();
            // on supprime l'image
            @unlink($chemin.$img);

            $entityManager->remove($enseignant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('enseignant_index');
    }
}
