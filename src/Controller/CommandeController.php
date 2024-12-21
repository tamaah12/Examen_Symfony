<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Client;
use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Doctrine\ORM\EntityManagerInterface;

class CommandeController extends AbstractController
{
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande', name: 'commande')]
    public function index(Request $request): Response
    {

        $commande = new Commande();


        $form = $this->createFormBuilder()
            ->add('telephone', TextType::class, [
                'label' => 'Numéro de téléphone du client'
            ])
            ->getForm();

        $form->handleRequest($request);

        $client = null;

        if ($form->isSubmitted() && $form->isValid()) {

            $telephone = $form->getData()['telephone'];


            $client = $this->entityManager->getRepository(Client::class)->findOneBy(['telephone' => $telephone]);

            if ($client) {

                $commande->setClient($client);


                $articlesForm = $this->createFormBuilder()
                    ->add('article', TextType::class, [
                        'label' => 'ID de l\'Article (Numéro)'
                    ])
                    ->add('prix', NumberType::class, [
                        'label' => 'Prix de l\'article'
                    ])
                    ->add('quantite', NumberType::class, [
                        'label' => 'Quantité'
                    ])
                    ->getForm();

                $articlesForm->handleRequest($request);

                if ($articlesForm->isSubmitted() && $articlesForm->isValid()) {

                    $articleData = $articlesForm->getData();
                    $article = $this->entityManager->getRepository(Article::class)->find($articleData['article']);
                    
                    if ($article && $article->getStock() >= $articleData['quantite']) {

                        $commande->addArticle($article, $articleData['prix'], $articleData['quantite']);
                    } else {

                        $this->addFlash('error', 'Article introuvable ou stock insuffisant');
                    }
                }


                $this->entityManager->persist($commande);
                $this->entityManager->flush();

                return $this->render('commande/index.html.twig', [
                    'form' => $form->createView(),
                    'client' => $client,
                    'commande' => $commande,
                    'articlesForm' => isset($articlesForm) ? $articlesForm->createView() : null,
                ]);
            } else {

                $this->addFlash('error', 'Client non trouvé');
            }
        }


        return $this->render('commande/index.html.twig', [
            'form' => $form->createView(),
            'client' => $client,
            'commande' => null,
        ]);
    }
}
