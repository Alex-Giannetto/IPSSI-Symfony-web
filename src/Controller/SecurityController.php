<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\UserAdminUpdateInfoType;
use App\Form\UserRegisterType;
use App\Form\UserUpdateInfoType;
use App\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        $user = new User();
        $form = $this->createForm(LoginType::class, $user);
        $form->handleRequest($request);
        return $this->render('security/form.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager
    ) {
        $user = new User();
        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Your account has been created.');
            return $this->redirectToRoute('login');
        }
        return $this->render('security/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function profile(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserUpdateInfoType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('/');
        }

        return $this->render('security/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/users/", name="admin_usersList")
     */
    public function usersList(Request $request, EntityManagerInterface $entityManager, UserManager $userManager)
    {
        $users = $userManager->getAllUser();

        return $this->render('security/listeuser.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="admin_showUser")
     */
    public function showUser(Request $request, EntityManagerInterface $entityManager, UserManager $userManager, int $id)
    {
        $user = $userManager->getUserById($id);

        return $this->render('security/userInfo.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/user/modify/{id}", name="admin_modifyUser")
     */
    public function modifyUser(
        Request $request,
        EntityManagerInterface $entityManager,
        UserManager $userManager,
        int $id
    ) {
        $user = $userManager->getUserById($id);
        $form = $this->createForm(UserAdminUpdateInfoType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'The ' . $user->getEmail() . '\'s informations has been modified');
            return $this->redirectToRoute('admin_showUser', ['id' => $id]);
        }
        return $this->render('security/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/user/delete/{id}", name="admin_deleteUser")
     */
    public function deleteUser(
        Request $request,
        EntityManagerInterface $entityManager,
        UserManager $userManager,
        int $id
    ) {
        $user = $userManager->getUserById($id);
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('admin_usersList');

    }

}
