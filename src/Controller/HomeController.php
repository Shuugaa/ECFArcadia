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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/')]
    public function home(AvisRepository $aviRepo, ServicesRepository $servRepo, HabitatRepository $habRepo, AnimalRepository $aniRepo) : Response
    {
        return $this->render('master.html.twig', [
            'habitats' => $habRepo->findAllHabitat(),
            'services' => $servRepo->findAllServices(),
            'animals' => $aniRepo->findAllAnimals(),
            'avis' => $aviRepo->findAllAvisWhereVisible(),
        ]);
    }

    #[Route('/submitAvis', name:'app_home_submitAvis')]
    public function submitAvis(AvisRepository $avRepo, EntityManagerInterface $entityManager) : Response
    {
        $avis = new Avis();
        $avis->setPseudo($_POST['_pseudo']);
        $avis->setCommentaire($_POST['_avis']);
        $avis->setVisible(FALSE);

        $entityManager->persist($avis);
        $entityManager->flush();

        return $this->redirectToRoute('app_home_home');
    }

    #[Route('/services')]
    public function services(ServicesRepository $servRepo, HabitatRepository $habRepo) : Response
    {
        return $this->render('service.html.twig', [
            'habitats' => $habRepo->findAllHabitat(),
            'services' => $servRepo->findAllServices(),
        ]);
    }

    #[Route('/contact')]
    public function contact(ServicesRepository $servRepo, HabitatRepository $habRepo) : Response
    {
        return $this->render('contact.html.twig', [
            'habitats' => $habRepo->findAllHabitat(),
            'services' => $servRepo->findAllServices(),
        ]);
    }

    #[Route('/habitats')]
    public function habitat(ServicesRepository $servRepo, HabitatRepository $habRepo, AnimalRepository $aniRepo) : Response
    {
        return $this->render('habitat.html.twig', [
            'habitats' => $habRepo->findAllHabitat(),
            'services' => $servRepo->findAllServices(),
            'animals' => $aniRepo->findAnimalWithHabitatById(),
        ]);
    }

    #[Route('/veterinaire', name:'app_veterinaire_admin')]
    public function veterinaire(RapportsRepository $rapRepo, HabitatRepository $habRepo, AnimalRepository $aniRepo): Response
    {
        if ($this->isGranted('ROLE_VETERINAIRE') == false) {
            return $this->render('failedToAuth.html.twig');
        }

        return $this->render('employes/veterinaire.html.twig', [
            'habitats' => $habRepo->findAllHabitat(),
            'animals' => $aniRepo->findAllAnimals(),
            'rapports' => $rapRepo->findAllNourriture(),
        ]);
    }

    #[Route('/veterinaire/rapports', name:'app_veterinaire_admin_rapports')]
    public function veterinaireRapports(RapportsRepository $rapRepo, HabitatRepository $habRepo, AnimalRepository $aniRepo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('ROLE_VETERINAIRE') == false) {
            return $this->render('failedToAuth.html.twig');
        }

        $rapport = new Rapports();
        $rapport->setDate(new \DateTime($_POST['_date']));
        $rapport->setNourriture($_POST['_nourriture']);
        $rapport->setGrammage($_POST['_grammage']);
        $rapport->setPrenom($_POST['_prenom']);
        if($_POST['_detail'])
        {
            $rapport->setDetail($_POST['_detail']);
        }

        $entityManager->persist($rapport);
        $entityManager->flush();
        if ($aniRepo->checkAnimalExists($_POST['_prenom'])) {
            $id = $aniRepo->findOneOccurence($_POST['_prenom']);
            $animal = $aniRepo->find($id);
            if($_POST['_etat'] != 0) {
                $animal->setEtat($_POST['_etat']);
            }
            $entityManager->persist($animal);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_veterinaire_admin');
    }

    #[Route('/veterinaire/commentaireHab', name:'app_veterinaire_admin_commHab')]
    public function veterinairecommentaireHab(RapportsRepository $rapRepo, HabitatRepository $habRepo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('ROLE_VETERINAIRE') == false) {
            return $this->render('failedToAuth.html.twig');
        }

        if ($habRepo->checkHabitatExists($_POST['_nom'])) {
            $id = $habRepo->findOneOccurence($_POST['_nom']);
            $habitat = $habRepo->find($id);
            if($_POST['_commentaire'] != 0) {
                $habitat->setCommentaireHabitat($_POST['_commentaire']);
                $entityManager->persist($habitat);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('app_veterinaire_admin');
    }

    #[Route('/employe', name:'app_employe_admin')]
    public function employe(AvisRepository $aviRepo, ServicesRepository $servRepo, HabitatRepository $habRepo, AnimalRepository $aniRepo): Response
    {
        if ($this->isGranted('ROLE_EMPLOYE') == false) {
            return $this->render('failedToAuth.html.twig');
        }

        return $this->render('employes/employe.html.twig', [
            'habitats' => $habRepo->findAllHabitat(),
            'services' => $servRepo->findAllServices(),
            'animals' => $aniRepo->findAllAnimals(),
            'avis' => $aviRepo->findAllAvis(),
        ]);
    }

    #[Route('/employe/avis', name:'app_employe_admin_avis')]
    public function employeAvis(AvisRepository $aviRepo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('ROLE_EMPLOYE') == false) {
            return $this->render('failedToAuth.html.twig');
        }

        $id = rtrim($_POST['_value'], "/");
        //if($aviRepo->findOccurence($id))
        //{
            $flush = ($aviRepo->findOneBySomeField($id))->setVisible($aviRepo->findOccurence($id));
            $entityManager->persist($flush);
            $entityManager->flush();
        //}
        
        return $this->redirectToRoute('app_employe_admin');
    }

    #[Route('/employe/rapports', name:'app_employe_admin_rapports')]
    public function employeRapports(AvisRepository $aviRepo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('ROLE_EMPLOYE') == false) {
            return $this->render('failedToAuth.html.twig');
        }

        $rapport = new Rapports();
        $rapport->setDate(new \DateTime($_POST['_date']));
        $rapport->setNourriture($_POST['_nourriture']);
        $rapport->setGrammage($_POST['_grammage']);
        $rapport->setPrenom($_POST['_prenom']);

        $entityManager->persist($rapport);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_employe_admin');
    }
}