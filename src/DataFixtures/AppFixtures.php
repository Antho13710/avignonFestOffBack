<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Date;
use App\Entity\Type;
use App\Entity\Event;
use App\Entity\Place;
use App\Entity\DayOff;
use Doctrine\DBAL\Connection;
use App\DataFixtures\TheaterProvider;
use App\Entity\Alert;
use App\Entity\Contact;
use App\Entity\User;
use DateInterval;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    // function to trucate database (id autoincrement restart after each fixtures load)
    private function truncate(Connection $connection)
    {
        $sql = $connection->query('SET foreign_key_checks = 0');
        $sql = $connection->query('TRUNCATE TABLE date');
        $sql = $connection->query('TRUNCATE TABLE alert');
        $sql = $connection->query('TRUNCATE TABLE contact');
        $sql = $connection->query('TRUNCATE TABLE day_off');
        $sql = $connection->query('TRUNCATE TABLE event');
        $sql = $connection->query('TRUNCATE TABLE place');
        $sql = $connection->query('TRUNCATE TABLE type');
        $sql = $connection->query('TRUNCATE TABLE user');
    }

    // load fixtures
    public function load(ObjectManager $manager)
    {
        // faker init
        $faker = Faker\Factory::create();

        // add my provider to Faker
        $faker->addProvider(new TheaterProvider());

        // truncate tables for reinit id's
        $this->truncate($manager->getConnection());

        //init places array
        $places = [];

        // Add 80 places
        for ($place = 0; $place < 80; $place++) {
            $newPlace = new Place();
            $newPlace->setName($faker->unique()->eventPlace());
            $newPlace->setAddress($faker->unique()->eventAdress());
            //persist new place in db
            $manager->persist($newPlace);
            // persist new place in array
            $places[] = $newPlace;
        }

        //init types array
        $types = [];

        // Add 30 types
        for ($type = 0; $type < 30; $type++) {
            $newType = new Type();
            $newType->setName($faker->unique()->eventStyle());
            //persist new Type in db
            $manager->persist($newType);
            // persist new Type in array
            $types[] = $newType;
        }

        // add 2 test users

        $artist = new User();
        $artist->setEmail('artist@artist.com');
        $artist->setFirstname('Art');
        $artist->setLastname('Ist');
        $artist->setRoles(["ROLE_ARTIST"]);
        $artist->setPassword('$argon2id$v=19$m=65536,t=4,p=1$wRMcE/QNGMIFsCk26fledg$+8sF20/02u8LI/Nk0HVY4toAiCyEsjYRpPd2uFApuoU');

        $manager->persist($artist);

        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setFirstname('Admin');
        $admin->setLastname('Istrateur');
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setPassword('$argon2id$v=19$m=65536,t=4,p=1$MAK++2ARbQSCkC3QEaHBYQ$aCiSfoGne3Nljv5TETvVTwGolkDiszmYdjkM/20xBuw');

        $manager->persist($admin);

        // Add 10 users
        $users = [];
        for ($user = 0; $user < 10; $user++) {
            $newUser = new User();
            $newUser->setFirstname($faker->unique()->firstName());
            $newUser->setLastname($faker->unique()->lastName());
            $newUser->setEmail(strtolower($newUser->getFirstname()) . '.' . strtolower($newUser->getLastname()) . $faker->userEmail());
            $newUser->setRoles(["ROLE_ARTIST"]);
            $newUser->setPassword('$argon2id$v=19$m=65536,t=4,p=1$MAK++2ARbQSCkC3QEaHBYQ$aCiSfoGne3Nljv5TETvVTwGolkDiszmYdjkM/20xBuw');

            $users[] = $newUser;
            $manager->persist($newUser);
        }

        // init events array
        $events = [];

        // Add 100 events
        for ($event = 0; $event < 100; $event++) {
            $newEvent = new Event();
            $newEvent->setTitle($faker->unique()->eventTitle());
            $newEvent->setPlace($places[mt_rand(0, count($places) - 1)]);
            $newEvent->setType($types[mt_rand(0, count($types) - 1)]);
            $newEvent->setDuration($faker->numberBetween(30, 180));
            $newEvent->setAuthorName($faker->firstName() . ' ' . $faker->lastName());
            $newEvent->setTroopName($faker->unique()->eventTroop());
            $newEvent->setEventDescription($faker->text(mt_rand(300, 800)));
            $newEvent->setTroopDescription($faker->text(mt_rand(100, 200)));
            $newEvent->setTime($faker->timeRand());
            $newEvent->setReservationContact('06' . $faker->randomNumber(8, true));
            $newEvent->setImage($faker->imageRand());
            $newEvent->setFullPrice($faker->numberBetween(0, 20));
            if ($newEvent->getFullPrice() > 0) {
                $newEvent->setSubscriberPrice($newEvent->getFullPrice() - 2);
                $newEvent->setReducedPrice($newEvent->getSubscriberPrice() - 2);
                $newEvent->setChildrenPrice($newEvent->getReducedPrice() - 2);
            }
            $newEvent->setUser($users[mt_rand(0, count($users) - 1)]);
            // persist new event in database
            $manager->persist($newEvent);
            // add new event to $events array
            $events[] = $newEvent;
        }

        /**
         * Add alerts to events
         */
        for ($alert = 0; $alert < 50; $alert++) {
            $newAlert = new Alert();
            $newAlert->setComment($faker->sentence(10));
            $newAlert->setEvent($events[mt_rand(0, 50)]);

            $manager->persist($newAlert);
        }

        /* TODO : trouver un moyen de saisir des jours de relaches qui ne sont pas égaux aux jours de 
        *        début ou de fin de l'event
        */

        // Add dates for 100 events
        for ($d = 0; $d < count($events); $d++) {
            $dates = [];
            for ($dateCount = 0; $dateCount < mt_rand(1, 3); $dateCount++) {
                $newDate = new Date();
                $newDate->setEvent($events[$d]);
                $newDate->setUser($newDate->getEvent()->getUser());
                if (!empty($dates) && end($dates)->getEndDate() < new DateTime('2021-07-20')) {
                    $newDate->setStartDate($faker->dateTimeBetween(end($dates)->getEndDate(), '2021/07/30'));
                    $newDate->setEndDate($faker->dateTimeBetween($newDate->getStartDate(), '2021/07/31'));
                    
                } elseif (empty($dates)) {
                    $newDate->setStartDate($faker->dateTimeBetween('2021/07/01', '2021/07/20'));
                    $newDate->setEndDate($faker->dateTimeBetween($newDate->getStartDate(), '2021/07/31'));
                } else {
                    break;
                }
                $dates[] = $newDate;
                $manager->persist($newDate);
            }
            // add dayOffs
            foreach ($dates as $date) {
                $startDate = $date->getStartDate();
                $endDate = $date->getEndDate();
                $interval = $startDate->diff($endDate);

                if (intval($interval->format('%d')) > 5) {
                    $newDayOff = new DayOff();
                    $newDayOff->setEvent($date->getEvent());
                    $newDayOff->setDate($faker->dateTimeBetween($startDate->add(new DateInterval('P2D')), $endDate->sub(new DateInterval('P2D'))));
                    $newDayOff->setUser($date->getEvent()->getUser());
                    $manager->persist($newDayOff);
                }
            }
        }

        // Add 10 messages
        for ($message = 0; $message < 10; $message++) {
            $newMessage = new Contact();
            $newMessage->setUser($users[mt_rand(0, count($users) - 1)]);
            $newMessage->setObject($faker->sentence(10));
            $newMessage->setContent($faker->paragraph(5));
            $newMessage->setIsRead(0);

            $manager->persist($newMessage);
        }



        $manager->flush();
    }
}
