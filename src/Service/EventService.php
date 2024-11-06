<?php

namespace App\Service;

use App\Entity\Event;

class EventService{

    private $mediaService;

    public function __construct(
        MediaService $mediaService,
        )
    {
        $this->mediaService = $mediaService;
    }

    public function process(Event $event)
    {        
        if ($event->getMainPictureFile()) {
            $path = $this->mediaService->upload($event->getMainPictureFile());

            if ($event->getMainPicture()) {
                $this->mediaService->delete($event->getMainPicture());
            }

            $event->setMainPicture($path);
        }

        if ($event->getPicture2File()) {
            $path = $this->mediaService->upload($event->getPicture2File());

            if ($event->getPicture2()) {
                $this->mediaService->delete($event->getPicture2());
            }

            $event->setPicture2($path);
        }

        if ($event->getPicture3File()) {
            $path = $this->mediaService->upload($event->getPicture3File());

            if ($event->getPicture3()) {
                $this->mediaService->delete($event->getPicture3());
            }

            $event->setPicture3($path);
        }
    }

    public function deleteAllPictures(Event $event){

        if ($event->getMainPicture()) {
            $this->mediaService->delete($event->getMainPicture());
        }

        if ($event->getPicture2()) {
            $this->mediaService->delete($event->getPicture2());
        }

        if ($event->getPicture3()) {
            $this->mediaService->delete($event->getPicture3());
        }

    }

}