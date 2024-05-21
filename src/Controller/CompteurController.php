<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Avis;
use App\Entity\Habitat;
use App\Entity\Rapports;
use App\Entity\Services;
use App\Entity\Utilisateur;
use App\Repository\AnimalRepository;
use App\Repository\AvisRepository;
use App\Repository\HabitatRepository;
use App\Repository\RapportsRepository;
use App\Repository\ServicesRepository;
use App\Repository\UtilisateurRepository;
use App\Document\Compteur;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/mongoDb', name:'app_mongo')]
class CompteurController extends AbstractController
{

    #[Route('/createMongo', name:'create')]
    public function createMongo(DocumentManager $docManager): JsonResponse
    {
        $new = new Compteur();
        $new->setName($_POST['_prenom']);
        $new->setCompteur(0);

        $docManager->persist($new);
        $docManager->flush();
        return $this->json(['compteur' => 'created']);
    }

    #[Route('/updateMongo', name:'update')]
    public function updateMongo(DocumentManager $docManager, Compteur $cptRepo): JsonResponse
    {
        return $this->json(['compteur' => 'updated']);
    }
}