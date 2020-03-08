<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Service\PaginationService;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index")
     */
    public function index(AdRepository $repo, $page, PaginationService $pagination)
    {
        // Méthode find : permet de retrouver un enregistrement par son identifiant
        $pagination->setEntityClass(Ad::class)
                   ->setPage($page);
        
        return $this->render('admin/ad/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @param Ad $ad
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * Permet d'afficher le form d'edit
     *
     * @Route("admin/ads/{id}/edit", name="admin_ads_edit")
     */
    public function edit(Ad $ad, Request $request, ObjectManager $manager) {
        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success', "l'annonce <strong>{$ad->getTitle()}</strong>a bien été enregistrée");
        }

        return $this->render('admin/ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Ad $ad
     * @param ObjectManager $manager
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     * @return RedirectResponse
     */
    public function delete(Ad $ad, ObjectManager $manager) {
        if(count($ad->getBookings()) > 0) {
            $this->addFlash('warning', "Vous ne pouvez pas supprimer l'annonce <strong>{$ad->getTitle()}</strong> car elle possède des réservations");
        } else {
            $manager->remove($ad);
            $manager->flush();

            $this->addFlash('success', "L'annonce {$ad->getTitle()} a bien été supprimée");
        }

        return $this->redirectToRoute('admin_ads_index');
    }
}
