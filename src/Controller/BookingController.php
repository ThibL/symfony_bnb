<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * @Route("/ads/{slug}/book", name="booking_create")
     *
     * @IsGranted("ROLE_USER")
     */
    public function book(Ad $ad, Request $request, EntityManagerInterface $manager)
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking,  [
        "validation_groups" => ["Default", "front"]
    ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            $booking->setBooker($user)
                    ->setAd($ad)
                ;

            //Si les date ne sont pas disponibles, message d'erreur

            if(!$booking->isBookableDate()) {
                $this->addFlash(
                    'warning',
                    "Les dates que vous avesz choisies sont déjà reservées"
                );
            } else {
                $manager->persist($booking);
                $manager->flush();

                return $this->redirectToRoute('booking_show', ['id' => $booking->getId(),
                    'withAlert' => true
                ]);
            }


        }

        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Booking $booking
     *
     * @Route("/booking/{id}", name="booking_show")
     * @return Response
     */
    public function show(Booking $booking, EntityManagerInterface $manager, Request $request) {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAd($booking->getAd());
            $comment->setAuthor($this->getUser());

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre commentaire a bien été déposé !"
            );

            return $this->redirectToRoute('booking_show', ['id' => $booking->getID()]);
        }

        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }
}
