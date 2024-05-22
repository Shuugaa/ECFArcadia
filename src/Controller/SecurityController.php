<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Entity\Rapports;
use App\Repository\RapportsRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/authenticate')]
class SecurityController extends AbstractController
{
    #[Route('', name: 'app_login_auth')]
    public function authenticating(AuthenticationUtils $authenticationUtils, Security $security, UtilisateurRepository $userRepository, EntityManagerInterface $entityManager, RapportsRepository $rapRepo): Response
    {
        $correctId = $userRepository->findOneOccurence($_POST['_username']);
        if(!$correctId) {
            return $this->render('failedToAuth.html.twig');
        }
        $correctPass = $userRepository->findPassword($_POST['_username']);
        if(password_verify($_POST['_password'], $correctPass)) {
            $user2 = $entityManager->getRepository(Utilisateur::class)->find($correctId);
            if($user2) {
                $security->login($user2);
                if ($this->isGranted('ROLE_ADMIN') == true) {
                    return $this->redirectToRoute('app_admin');
                } else if ($this->isGranted('ROLE_VETERINAIRE') == true) {
                    return $this->redirectToRoute('app_veterinaire_admin');
                } else if ($this->isGranted('ROLE_EMPLOYE') == true) {
                    return $this->redirectToRoute('app_employe_admin');
                } else {
                    return $this->redirectToRoute('app_home_home', [], Response::HTTP_SEE_OTHER);
                }
            }
        }
        return $this->render('failed.html.twig');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        $security->logout();
    }

    #[Route(path: '/admin', name: 'app_admin')]
    public function logAdmin(UtilisateurRepository $userRepository, RapportsRepository $rapRepo): Response
    {
        if ($this->isGranted('ROLE_ADMIN') == false) {
            return $this->render('failedToAuth.html.twig');
        }

        return $this->render('admin/auth.html.twig', [
            'users' => $userRepository->findAllUsers(),
            'rapports' => $rapRepo->findAllRapportsVet(),
        ]);
    }

    #[Route('/admin/newUser', name:'app_createUser')]
    public function createUser(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, UtilisateurRepository $userRepository, MailerInterface $mailer): Response
    {
        if ($this->isGranted('ROLE_ADMIN') == false) {
            return $this->render('failedToAuth.html.twig');
        }

        if($userRepository->checkUsernameExists($_POST['_username'])) {
            return $this->render('duplicate.html.twig');
        }
        // ... e.g. get the user data from a registration form
        $user = new Utilisateur();
        $user->setUsername($_POST['_username']);
        $user->setRoles($_POST['_roleArray']);
        $plaintextPassword = $_POST['_password'];
        
        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($user);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        // Envoyer un mail la création de l'utilisateur...

        $concat = "Bonjour, votre compte a correctement été créer sous le nom d'utilisateur:".$_POST['_username'];
        $email = (new Email())
        ->from('shuugaa789@gmail.com')
        ->to($_POST['_username'])
        //->cc('cc@example.com')
        //->bcc('bcc@example.com')
        //->replyTo('fabien@example.com')
        //->priority(Email::PRIORITY_HIGH)
        ->subject('Your Account is Ready !')
        ->text($concat)
        ->html('<p>See Twig integration for better HTML integration!</p>');

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return $this->redirectToRoute('app_home_contact');
        }   

        return $this->render('admin/auth.html.twig', [
            'username' => $_POST['_username'],
            'phrase' => "créer",
            'users' => $userRepository->findAllUsers(),
        ]);
    }

    #[Route('/admin/deleteUser', name:'app_deleteUser')]
    public function deleteUser(EntityManagerInterface $entityManager, UtilisateurRepository $userRepository) : Response
    {
        if ($this->isGranted('ROLE_ADMIN') == false) {
            return $this->render('failedToAuth.html.twig');
        }

        if ($userRepository->checkUsernameExists($_POST['_username']) && $_POST['_username'] !== "shuugaa789@gmail.com") {
            $id = $userRepository->findOneOccurence($_POST['_username']);
            $user = $entityManager->getRepository(Utilisateur::class)->find($id);
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->render('admin/auth.html.twig', [
            'username' => $_POST['_username'],
            'phrase' => "supprimer",
            'users' => $userRepository->findAllUsers(),
        ]);
    }
}