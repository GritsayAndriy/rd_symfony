<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Entity\User;
use App\Form\RegisterForm;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    #[Route('/register', methods: ['GET', 'POST'], name: 'register')]
    public function formRegister(Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        $user = new User();
        $form = $this->createForm(RegisterForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $plaintextPassword = $user->getPassword();
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            $user->setPassword($hashedPassword);
            $this->userRepository->save($user, true);
            return $this->redirectToRoute('app_home');
        }
        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}