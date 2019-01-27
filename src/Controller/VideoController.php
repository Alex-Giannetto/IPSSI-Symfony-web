<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use App\Manager\CategoryManager;
use App\Manager\VideoManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(VideoManager $videoManager, CategoryManager $categoryManager)
    {
        $videos = $videoManager->getAllPublicVideo();
        $categories = $categoryManager->getAllCategory();

        return $this->render('/home.html.twig', [
            'videos' => $videos,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/video/{id}", name="viewVideo")
     */
    public function view(VideoManager $videoManager, int $id)
    {
        $video = $videoManager->getVideoById($id);

        if ($video === null || !$video->getPublished()) {
            return $this->redirectToRoute('home');
        }

        return $this->render('/video/view.html.twig', [
            'video' => $video
        ]);
    }


    /**
     * @Route("/video/manage/add", name="addVideo")
     */
    public function addVideo(EntityManagerInterface $entityManager, Request $request)
    {
        $video = new Video();

        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $video->setAuthor($this->getUser());
            $entityManager->persist($video);
            $entityManager->flush();

            $this->addFlash('success', 'The video has been added');
            return $this->redirectToRoute('manageVideos');
        }

        return $this->render('video/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/video/manage/list", name="manageVideos")
     */
    public function manageVideos(VideoManager $videoManager)
    {

        if ($this->isGranted('ROLE_ADMIN')) {
            $videos = $videoManager->getAllVideo();
        } else {
            $videos = $videoManager->getAllUserVideo($this->getUser());
        }

        return $this->render('video/manage.html.twig', [
            'videos' => $videos
        ]);
    }

    /**
     * @Route("/video/manage/modify/{id}", name="modifyVideo")
     */
    public function modifyVideo(
        VideoManager $videoManager,
        EntityManagerInterface $entityManager,
        Request $request,
        int $id
    ) {
        $video = $videoManager->getVideoById($id);
        if ($video === null) {
            return $this->redirectToRoute('manageVideos');
        }

        if ($video->getAuthor() !== $this->getUser()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $video->setAuthor($this->getUser());
            $entityManager->persist($video);
            $entityManager->flush();

            $this->addFlash('success', 'The video has been modified');
            return $this->redirectToRoute('manageVideos');
        }

        return $this->render('video/formModify.html.twig', [
            'form' => $form->createView(),
            'video' => $video
        ]);
    }

    /**
     * @Route("/video/manage/delete/{id}", name="deleteVideo")
     */
    public function deleteVideo(EntityManagerInterface $entityManager, VideoManager $videoManager, int $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $video = $videoManager->getVideoById($id);

        if ($this->isGranted('ROLE_ADMIN') || $video->getAuthor() === $this->getUser()) {
            $entityManager->remove($video);
            $entityManager->flush();
            $this->addFlash('success', 'The video has been deleted');

        } else {
            $this->addFlash('success', 'An error occured while attempted to delete the video');
        }

        return $this->redirectToRoute('manageVideos');
    }


}
