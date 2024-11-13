<?php

namespace App\Service;

use App\Entity\Event;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Util\Json;

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

    public function getEventListForCalendarEvents(array $events): string    
    {
        $eventDatesArray = [];

        foreach ($events as $event) {
            $startDate = $event->getDateStartAt();
            $endDate = $event->getDateEndAt() ?? $startDate; // Utilise la date de début si la fin est null
            $timeStart = $event->getTimeStartAt()->format('H:i:s');
            $timeEnd = $event->getTimeEndAt() ? $event->getTimeEndAt()->format('H:i:s') : $timeStart;

            // Vérifie si l'événement s'étend sur plusieurs jours (date de fin > date de début)
            if ($endDate > $startDate) {
                // Boucler pour chaque jour entre startDate et endDate
                $currentDate = clone $startDate;
                while ($currentDate <= $endDate) {
                    $eventDatesArray[] = [
                        'start' => $currentDate->format('Y-m-d') . 'T' . $timeStart,
                        'end' => $currentDate->format('Y-m-d') . 'T' . $timeEnd,
                        'backgroundColor' => '#4581cc',
                        'extendedProps' => [
                            'id' => $event->getId(),
                            'title' => $event->getName(),
                            'genre' => $event->getActivity()->getName(),
                            'rdv' => trim($event->getRdvAddress() . ' ' . ($event->getRdvPlaceName() ?? '')),
                            'rdvDate' => $currentDate->format('d/m/Y'),
                            'rdvTime' => $timeStart,
                            'type' => 'activity',
                            'infosDisplay' => true,
                        ]
                    ];
                    // Passe au jour suivant
                    $currentDate->modify('+1 day');
                }
            } else {
                // Sinon, ajouter un seul événement pour ce jour unique
                $eventDatesArray[] = [
                    'start' => $startDate->format('Y-m-d') . 'T' . $timeStart,
                    'end' => $endDate->format('Y-m-d') . 'T' . $timeEnd,
                    'backgroundColor' => '#4581cc',
                    'extendedProps' => [
                        'id' => $event->getId(),
                        'title' => $event->getName(),
                        'genre' => $event->getActivity()->getName(),
                        'rdv' => trim($event->getRdvAddress() . ' ' . ($event->getRdvPlaceName() ?? '')),
                        'rdvDate' => $startDate->format('d/m/Y'),
                        'rdvTime' => $timeStart,
                        'type' => 'activity',
                        'infosDisplay' => true,
                    ]
                ];
            }
        }

        return json_encode($eventDatesArray);
    }

}