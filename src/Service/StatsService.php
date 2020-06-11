<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class StatsService {
    private $manager;

    public function __construct(EntityManagerInterface $manager) {
        $this->manager = $manager;
    }

    public function getstats() {
        $users = $this->getUsersCount();
        $ads = $this->getAdsCount();
        $bookings = $this->GetBookingsCount();
        $comments = $this->getCommentsCount();

        return compact('users', 'ads', 'bookings', 'comments');
    }

    public function getUsersCount() {
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    public function getAdsCount() {
        return $this->manager->createQuery('SELECT COUNT(a) FROM App\Entity\Ad a')->getSingleScalarResult();
    }

    public function getCommentsCount() {
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Comment c')->getSingleScalarResult();
    }

    public function getBookingsCount() {
        return $this->manager->createQuery('SELECT COUNT(b) FROM App\Entity\Booking b')->getSingleScalarResult();
    }

    public function getAdsStats($order) {
        return $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.id, u.firstname,u.lastName, u.picture
            FROM App\Entity\Comment c 
            JOIN c.ad a
            JOIN a.author u
            GROUP BY a
            ORDER BY note ' . $order
        )->setMaxResults(5)
        ->getResult();
    }
}
