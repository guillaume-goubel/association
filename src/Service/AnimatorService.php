<?php

namespace App\Service;

use App\Entity\Animator;

class AnimatorService{

    private $mediaService;

    public function __construct(
        MediaService $mediaService,
        )
    {
        $this->mediaService = $mediaService;
    }

    public function process(Animator $animator)
    {        
        if ($animator->getPictureFile()) {
            $path = $this->mediaService->uploadAnimatorPictures($animator->getPictureFile());

            if ($animator->getPicture()) {
                $this->mediaService->deleteAnimatorPictures($animator->getPicture());
            }

            $animator->setPicture($path);
        }


    }

    public function deleteAllAnimatorPictures(Animator $animator){

        if ($animator->getPicture()) {
            $this->mediaService->deleteAnimatorPictures($animator->getPicture());
        }
    }

}