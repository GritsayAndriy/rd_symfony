<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\UserForm;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/users', name: 'users_')]
class UserController extends AbstractController
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    #[Route('', methods: ['GET'], name: 'all')]
    public function index(): Response
    {
        $users = $this->userRepository->findAll();

        return $this->render('users/index.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/create', methods: ['GET', 'POST'], name: 'create')]
    public function create(Request $request)
    {
        $user = new User();
        $userForm = $this->createForm(UserForm::class, $user);

        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user = $userForm->getData();
            $this->userRepository->save($user, true);

            return $this->redirectToRoute('users_all');
        }

        return $this->render('users/form.html.twig', [
            'form' => $userForm->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(int $id, Request $request)
    {
        $user = $this->userRepository->find($id);
        $userForm = $this->createForm(UserForm::class, $user);

        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user = $userForm->getData();
            $this->userRepository->save($user, true);

            return $this->redirectToRoute('users_all');
        }

        return $this->render('users/form.html.twig', [
            'form' => $userForm->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(int $id)
    {
        $user = $this->userRepository->find($id);
        if ($user !== null) {
            $this->userRepository->remove($user, true);
        }
        return $this->redirectToRoute('users_all');
    }
}