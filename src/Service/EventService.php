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
            $path = $this->mediaService->uploadEventPictures($event->getMainPictureFile());

            if ($event->getMainPicture()) {
                $this->mediaService->deleteEventPictures($event->getMainPicture());
            }

            $event->setMainPicture($path);
        }

        if ($event->getPicture2File()) {
            $path = $this->mediaService->uploadEventPictures($event->getPicture2File());

            if ($event->getPicture2()) {
                $this->mediaService->deleteEventPictures($event->getPicture2());
            }

            $event->setPicture2($path);
        }

        if ($event->getPicture3File()) {
            $path = $this->mediaService->uploadEventPictures($event->getPicture3File());

            if ($event->getPicture3()) {
                $this->mediaService->deleteEventPictures($event->getPicture3());
            }

            $event->setPicture3($path);
        }
    }

    public function deleteAllEventPictures(Event $event){

        if ($event->getMainPicture()) {
            $this->mediaService->deleteEventPictures($event->getMainPicture());
        }

        if ($event->getPicture2()) {
            $this->mediaService->deleteEventPictures($event->getPicture2());
        }

        if ($event->getPicture3()) {
            $this->mediaService->deleteEventPictures($event->getPicture3());
        }
    }

    public function getEventListForCalendarEvents(array $events): string
    {
        $eventDatesArray = [];

        foreach ($events as $event) {
            $startDate = $event->getDateStartAt();
            $endDate = $event->getDateEndAt() ?? $startDate; // Si pas de date de fin, on utilise la date de début comme date de fin
            $timeStart = $event->getTimeStartAt()->format('H:i');
            $timeEnd = $event->getTimeEndAt() ? $event->getTimeEndAt()->format('H:i') : null; // Fin par défaut à 23:59
            $eventDateStartAtFormat = $startDate->format('d/m/Y');
            $eventDateEndAtFormat = $endDate->format('d/m/Y');

            // Déterminer si l'événement est de type "long" ou "short"
            $eventDuration = ($endDate > $startDate) ? 'long' : 'short';

            // Si l'événement dure plusieurs jours (date de fin > date de début)
            if ($endDate > $startDate) {
                // Calculer le nombre total de jours de l'événement
                $totalDays = $startDate->diff($endDate)->days + 1; // +1 car on inclut aussi le jour de début

                $currentDate = clone $startDate;
                $dayCounter = 1; // Compteur pour le numéro du jour

                // On boucle sur chaque jour entre la date de début et la date de fin
                while ($currentDate <= $endDate) {
                    $eventDatesArray[] = $this->formatEventData(
                        $event,
                        $currentDate,
                        $timeStart,
                        $timeEnd,
                        $eventDuration,
                        $dayCounter,
                        $totalDays,
                        $eventDateStartAtFormat,
                        $eventDateEndAtFormat
                    );
                    $currentDate->modify('+1 day');
                    $dayCounter++;
                }
            } else {
                
                // Événement d'une seule journée
                // if ($endDate == $startDate && $timeEnd && $timeStart > $timeEnd) {
                //     $timeEnd = $timeStart; // Corrige les incohérences horaires
                // }

                $eventDatesArray[] = $this->formatEventData(
                    $event,
                    $startDate,
                    $timeStart,
                    $timeEnd,
                    $eventDuration,
                    1,
                    1,
                    null,
                    null
                );
            }
        }

        return json_encode($eventDatesArray);
    }

    private function formatEventData(
        $event,
        $date,
        $timeStart,
        $timeEnd,
        $eventDuration,
        $dayNumber,
        $totalDays,
        $eventDateStartAtFormat,
        $eventDateEndAtFormat
    ): array {
        return [
            'start' => $date->format('Y-m-d') . 'T' . '01:00',
            'end' => $timeEnd ? $date->format('Y-m-d') . 'T' . '23:59' : null, // Ajout de la condition pour éviter une heure de fin nulle
            'backgroundColor' => '#4581cc',
            'extendedProps' => [
                'id' => $event->getId(),
                'title' => $event->getName(),
                'genre' => $event->getActivity()->getName(),
                'rdv' => trim($event->getRdvAddress() . ' ' . ($event->getRdvPlaceName() ?? '')),
                'rdvDate' => $date->format('d/m/Y'),
                'rdvTime' => $timeStart,
                'rdvTimeEnd' => $timeEnd ?? null,
                'duration' => $eventDuration,
                'durationTotal' => ($eventDuration === 'long') ? $totalDays : 1,
                'durationDayNumber' => ($eventDuration === 'long') ? "$dayNumber/$totalDays" : null,
                'eventDateStartAt' => $eventDateStartAtFormat,
                'eventDateEndAt' => $eventDateEndAtFormat
            ]
        ];
    }

}