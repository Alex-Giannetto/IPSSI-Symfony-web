<?php

namespace App\Manager;

use App\Entity\Category;
use App\Entity\Video;
use App\Repository\VideoRepository;

class VideoManager
{
    private $videoRepository;

    public function __construct(VideoRepository $videoRepository)
    {
        $this->videoRepository = $videoRepository;
    }

    /**
     * Return the video who have the given in parameter id
     * @param int $id
     * @return Category|null
     */
    public function getVideoById(int $id): ?Video
    {
        return $this->videoRepository->find($id);
    }

    /**
     * Return all the videos
     * @return array|null
     */
    public function getAllVideo(): ?array
    {
        return $this->videoRepository->findAll();
    }

    public function getAllPublicVideo(): ?array
    {
        return $this->videoRepository->findBy(['published' => 1]);
    }

    public function getVideoImage(): ?string
    {

    }
}