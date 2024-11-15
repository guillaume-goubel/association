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
            $endDate = $event->getDateEndAt() ?? $startDate; // If no end date, use start date as end date
            $timeStart = $event->getTimeStartAt()->format('H:i');
            $timeEnd = $event->getTimeEndAt() ? $event->getTimeEndAt()->format('H:i') : null;
    
            // Check if event spans multiple days (end date > start date)
            if ($endDate > $startDate) {
                // Loop through each day between startDate and endDate
                $currentDate = clone $startDate;
                while ($currentDate <= $endDate) {
                    $eventDatesArray[] = $this->formatEventData($event, $currentDate, $timeStart, $timeEnd);
                    $currentDate->modify('+1 day');
                }
            } else {
                // Single day event
                $eventDatesArray[] = $this->formatEventData($event, $startDate, $timeStart, $timeEnd);
            }
        }
    
        return json_encode($eventDatesArray);
    }
    
    /**
     * Helper function to format event data for calendar.
     */
    private function formatEventData($event, $date, $timeStart, $timeEnd): array
    {
        return [
            'start' => $date->format('Y-m-d') . 'T' . $timeStart,
            'end' => $date->format('Y-m-d') . 'T' . $timeEnd,
            'backgroundColor' => '#4581cc',
            'extendedProps' => [
                'id' => $event->getId(),
                'title' => $event->getName(),
                'genre' => $event->getActivity()->getName(),
                'rdv' => trim($event->getRdvAddress() . ' ' . ($event->getRdvPlaceName() ?? '')),
                'rdvDate' => $date->format('d/m/Y'),
                'rdvTime' => $timeStart,
                'rdvTimeEnd' => $timeEnd,
                'type' => 'activity',
                'infosDisplay' => true,
            ]
        ];
    }
    

}