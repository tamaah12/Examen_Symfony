<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\DetailCommande;
use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
public function load(ObjectManager $manager): void
{
        $client = new Client();
        $client->setNom('Diallo')
               ->setPrenom('Saliou')
               ->setAdresse('Rufisque')
               ->setTelephone('781129018');
        
        $manager->persist($client);

        $article1 = new Article();
        $article1->setNom('Sac à dos')
                ->setPrixUnitaire(15000)
                ->setQuantiteStock(50);

        $manager->persist($article1);

        $article2 = new Article();
        $article2->setNom('Sac à main')
                 ->setPrixUnitaire(25000)
                 ->setQuantiteStock(25);
        $manager->persist($article2);

        $commande = new Commande();
        $commande->setDate(new \DateTime())
                 ->setClient($client);
        
        $manager->persist($commande);

        $detail1 = new DetailCommande();
        $detail1->setQuantite(2)
                ->setPrix($article1->getPrixUnitaire())
                ->setArticle($article1)
                ->setCommande($commande);
        
        $manager->persist($detail1);

        $detail2 = new DetailCommande();
        $detail2->setQuantite(1)
                ->setPrix($article2->getPrixUnitaire())
                ->setArticle($article2)
                ->setCommande($commande);
        
        $manager->persist($detail2);

        $manager->flush();
    }
}
