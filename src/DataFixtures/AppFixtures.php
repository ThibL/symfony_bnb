<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        //création d'un role user
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstname('Thibaud')
                  ->setLastName('Levy')
                  ->setEmail('thibaudlevy@titi.com')
                  ->setHash($this->encoder->encodePassword($adminUser, 'password'))
                  ->setPicture('https://randomuser.me/portraits/men/25.jpg')
                  ->setDescription('<p>' . join('</p><p>' , $faker->paragraphs(5)) . '</p>')
                  ->setIntroduction($faker->sentence())
                  ->addUserRole($adminRole);

        $adminUser2 = new User();
        $adminUser2->setFirstname('Thibaud')
            ->setLastName('Levy')
            ->setEmail('thibaudlevy@titi2.com')
            ->setHash($this->encoder->encodePassword($adminUser, 'password'))
            ->setPicture('https://randomuser.me/portraits/men/26.jpg')
            ->setDescription('<p>' . join('</p><p>' , $faker->paragraphs(5)) . '</p>')
            ->setIntroduction($faker->sentence())
            ->addUserRole($adminRole);

        $manager->persist($adminUser);
        $manager->persist($adminUser2);

        //utilisateurs
        $users  = [];
        $genres = ['male', 'female'];

        for ($i= 1; $i<=10; $i++) {
            $user      = new User();
            $genre     = $faker->randomElement($genres);
            $picture   = "https://randomuser.me/api/portraits/";
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';

            $picture = $picture . ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstname($faker->firstName($genre))
                 ->setLastName($faker->lastName)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence())
                 ->setDescription('<p>' . join('</p><p>' , $faker->paragraphs(5)) . '</p>')
                 ->setHash($hash)
                 ->setPicture($picture)
                ;

            $manager->persist($user);
            $users[]= $user;
        }


        //nous gérons les annonces
        for ($i = 1; $i <= 30; $i++) {
            $ad = new Ad();

            $title        = $faker->sentence(6);
            $coverImage   = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);
            $content      = '<p>' . join('</p><p>' , $faker->paragraphs(5)) . '</p>';

            $user         = $users[mt_rand(0, count($users) - 1)];

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40,200))
                ->setRooms(mt_rand(1,6))
                ->setAuthor($user);
            // $product = new Product();
            // $manager->persist($product);

            for ($j=0; $j <= mt_rand(2,5); $j++) {
                $image = new Image();
                $image->setUrl($faker->imageUrl())
                        ->setCaption($faker->sentence(6))
                        ->setAd($ad);
                $manager->persist($image);
            }

            //gestion des reservations
            for ($j = 1; $j <= mt_rand(0,10); $j++) {
                $booking   = new Booking();

                $createdAt = $faker->dateTimeBetween('-6 months');
                $startDate = $faker->dateTimeBetween('-3 months');
                //gestion date de fin
                $duration  = mt_rand(3,10);
                $endDate   = (clone $startDate)->modify("+$duration days");

                $amount    = $ad->getPrice() * $duration;

                $booker    = $users[mt_rand(0,count($users) - 1)];

                $comment   = $faker->paragraph();

                $booking->setBooker($booker)
                        ->setAd($ad)
                        ->setStartDate($startDate)
                        ->setEndDate($endDate)
                        ->setCreatedAt($createdAt)
                        ->setAmount($amount)
                        ->setComment($comment)
                    ;
                $manager->persist($booking);

                // Gestion des commentaires sur les annonces
                if(mt_rand(0, 1)) {
                    $comment = new Comment();
                    $comment->setContent($faker->paragraph())
                            ->setRating(mt_rand(1,5))
                            ->setAuthor($booker)
                            ->setAd($ad);
                    $manager->persist($comment);
                }
            }
            $manager->persist($ad);
        }
        $manager->flush();
    }
}
