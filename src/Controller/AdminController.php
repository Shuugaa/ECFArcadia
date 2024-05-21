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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('', name:'app_admin_crud')]
    public function crudAdmin() : Response
    {
        if ($this->isGranted('ROLE_ADMIN') == false) {
            return $this->render('failedToAuth.html.twig');
        }

        return $this->render('admin/auth.html.twig');
    }
    
    #[Route('/statistiques', name:'app_admin_stat')]
    public function modStatis() : Response
    {
        if ($this->isGranted('ROLE_ADMIN') == false) {
            return $this->render('failedToAuth.html.twig');
        }

        return $this->render('admin/crudStat.html.twig');
    }

    //Le meme schema a été utilisé pour les services et les habitats
    #[Route('/services', name:'app_admin_crud_serv')]
    public function modServices() : Response
    {
        if ($this->isGranted('ROLE_ADMIN') == false) {
            return $this->render('failedToAuth.html.twig');
        }

        return $this->render('admin/crudServ.html.twig');
    }

    #[Route('/services/create', name:'app_admin_crud_serv_create')]
    public function cServices(EntityManagerInterface $entityManager) : Response
    {
        if ($this->isGranted('ROLE_ADMIN') == true || $this->isGranted('ROLE_EMPLOYE') == true) {
            $service = new Services();
            $service->setNom($_POST['_nom']);
            $service->setDescription($_POST['_description']);
    
            $entityManager->persist($service);
            $entityManager->flush();
    
            if($this->isGranted('ROLE_EMPLOYE') == true) {
                return $this->redirectToRoute('app_employe_admin');
            }
            return $this->redirectToRoute('app_admin_crud_serv');
            
        }
        return $this->render('failedToAuth.html.twig');
    }

    #[Route('/services/delete', name:'app_admin_crud_serv_delete')]
    public function dServices(EntityManagerInterface $entityManager, ServicesRepository $servRepo) : Response
    {
        if ($this->isGranted('ROLE_ADMIN') == true || $this->isGranted('ROLE_EMPLOYE') == true) {
            if ($servRepo->checkServicesExists($_POST['_nom'])) {
                $id = $servRepo->findOneOccurence($_POST['_nom']);
                $service = $servRepo->find($id);
                $entityManager->remove($service);
                $entityManager->flush();
            }

            if($this->isGranted('ROLE_EMPLOYE') == true) {
                return $this->redirectToRoute('app_employe_admin');
            }
            return $this->redirectToRoute('app_admin_crud_serv');
        }

        return $this->render('failedToAuth.html.twig');
    }
    
    #[Route('/services/update', name:'app_admin_crud_serv_update')]
    public function uServices(EntityManagerInterface $entityManager, ServicesRepository $servRepo) : Response
    {
        if ($this->isGranted('ROLE_ADMIN') == false || $this->isGranted('ROLE_EMPLOYE') == false) {
                if ($servRepo->checkServicesExists($_POST['_nom'])) {
                    $id = $servRepo->findOneOccurence($_POST['_nom']);
                    $service = $servRepo->find($id);
                    if($_POST['_nom2'] != 0) {
                        $service->setNom($_POST['_nom2']);
                    }
                    if($_POST['_description'] != 0) {
                        $service->setDescription($_POST['_description']);
                    }
                    $entityManager->persist($service);
                    $entityManager->flush();
                }

                if($this->isGranted('ROLE_EMPLOYE') == true) {
                    return $this->redirectToRoute('app_employe_admin');
                }
                return $this->redirectToRoute('app_admin_crud_serv');   
        }
        return $this->render('failedToAuth.html.twig');
    }

    #[Route('/habitat', name:'app_admin_crud_hab')]
    public function modHabitat(HabitatRepository $habRepo) : Response
    {
        if ($this->isGranted('ROLE_ADMIN') == false) {
            return $this->render('failedToAuth.html.twig');
        }

        return $this->render('admin/crudHab.html.twig');
    }

    #[Route('/habitat/create', name:'app_admin_crud_hab_create')]
    public function cHabitat(HabitatRepository $habRepo, EntityManagerInterface $entityManager) : Response
    {
        if ($this->isGranted('ROLE_ADMIN') == false) {
            return $this->render('failedToAuth.html.twig');
        }

        $habitat = new Habitat();
        $habitat->setNom($_POST['_nom']);
        $habitat->setDescription($_POST['_description']);
        if($_POST['_commentaire'] != 0) {
            $habitat->setCommentaireHabitat($_POST['_commentaire']);
        }

        $entityManager->persist($habitat);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_crud_hab');
    }

    #[Route('/habitat/delete', name:'app_admin_crud_hab_delete')]
    public function dHabitat(EntityManagerInterface $entityManager, HabitatRepository $habRepo, AnimalRepository $aniRepo) : Response
    {
        if ($this->isGranted('ROLE_ADMIN') == false) {
            return $this->render('failedToAuth.html.twig');
        }

        if ($valueS = $aniRepo->findAnimalInHabitatById($habRepo->findOneOccurence($_POST['_nom']))) {
            for ($i = 0; $i < count($valueS); ++$i) {
                $prenom = implode($valueS[$i]);
                $id = $aniRepo->findOneOccurence($prenom);
                $animal = $aniRepo->find($id);
                $entityManager->remove($animal);
                $entityManager->flush();
            }
        }

        if ($habRepo->checkHabitatExists($_POST['_nom'])) {
            $id = $habRepo->findOneOccurence($_POST['_nom']);
            $habitat = $habRepo->find($id);
            $entityManager->remove($habitat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_crud_hab');
    }
    
    #[Route('/habitat/update', name:'app_admin_crud_hab_update')]
    public function uHabitat(EntityManagerInterface $entityManager, HabitatRepository $habRepo) : Response
    {
        if ($this->isGranted('ROLE_ADMIN') == false) {
            return $this->render('failedToAuth.html.twig');
        }

        if ($habRepo->checkHabitatExists($_POST['_nom'])) {
            $id = $habRepo->findOneOccurence($_POST['_nom']);
            $habitat = $habRepo->find($id);
            if($_POST['_nom2'] != 0) {
                $habitat->setNom($_POST['_nom2']);
            }
            if($_POST['_description'] != 0) {
                $habitat->setDescription($_POST['_description']);
            }
            if($_POST['_commentaire'] != 0) {
                $habitat->setCommentaireHabitat($_POST['_commentaire']);
            }
            $entityManager->persist($habitat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_crud_hab');
    }

    #[Route('/animal', name:'app_admin_crud_ani')]
    public function modAnimal() : Response
    {
        if ($this->isGranted('ROLE_ADMIN') == false) {
            return $this->render('failedToAuth.html.twig');
        }

        return $this->render('admin/crudAni.html.twig');
    }

    #[Route('/animal/create', name:'app_admin_crud_ani_create')]
    public function cAnimal(AnimalRepository $aniRepo, EntityManagerInterface $entityManager, HabitatRepository $habRepo) : Response
    {
        if ($this->isGranted('ROLE_ADMIN') == false) {
            return $this->render('failedToAuth.html.twig');
        }

        $animal = new Animal();
        $animal->setPrenom($_POST['_prenom']);
        $animal->setEtat($_POST['_etat']);
        $animal->setRace($_POST['_race']);
        $habitat = $habRepo->findOneBySomeField($_POST['_habitat']);
        if(!$habitat) {
            return $this->render('admin/crudAni.html.twig' , [
                'error' => true,
            ]);
        } else {
            $animal->setHabitat($habRepo->findOneBySomeField($_POST['_habitat']));
        }
            

        $entityManager->persist($animal);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_crud_ani');
    }

    #[Route('/animal/delete', name:'app_admin_crud_ani_delete')]
    public function dAnimal(EntityManagerInterface $entityManager, AnimalRepository $aniRepo) : Response
    {
        if ($this->isGranted('ROLE_ADMIN') == false) {
            return $this->render('failedToAuth.html.twig');
        }

        if ($aniRepo->checkAnimalExists($_POST['_prenom'])) {
            $id = $aniRepo->findOneOccurence($_POST['_prenom']);
            $animal = $aniRepo->find($id);
            $entityManager->remove($animal);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_crud_ani');
    }
    
    #[Route('/animal/update', name:'app_admin_crud_ani_update')]
    public function uAnimal(EntityManagerInterface $entityManager, AnimalRepository $aniRepo) : Response
    {
        if ($this->isGranted('ROLE_ADMIN') == false) {
            return $this->render('failedToAuth.html.twig');
        }

        if ($aniRepo->checkAnimalExists($_POST['_prenom'])) {
            $id = $aniRepo->findOneOccurence($_POST['_prenom']);
            $animal = $aniRepo->find($id);
            if($_POST['_prenom2'] != 0) {
                $animal->setPrenom($_POST['_prenom2']);
            }
            if($_POST['_etat'] != 0) {
                $animal->setEtat($_POST['_etat']);
            }
            if($_POST['_race'] != 0) {
                $animal->setRace($_POST['_race']);
            }
            $entityManager->persist($animal);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_crud_ani');
    }
}