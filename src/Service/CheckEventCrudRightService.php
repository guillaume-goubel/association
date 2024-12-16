<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Event;

class CheckEventCrudRightService{

    public function checkProcess(User $user, string $route)
    {    
        switch ($route) {
            case "admin_event_new":
                if(in_array('ROLE_SUPER_ADMIN', $user->getRoles()) || $user->getIsCrudCreate()) {
                    return true;
                } 
            case "admin_event_edit":
                if(in_array('ROLE_SUPER_ADMIN', $user->getRoles()) || $user->getIsCrudEdit()) {
                    return true;
                } 
            case "admin_event_delete":
                if(in_array('ROLE_SUPER_ADMIN', $user->getRoles()) || $user->getIsCrudDelete()) {
                    return true;
                } 
            case "admin_event_edit_canceler":
                if($user->getIsCrudEventCanceler()) {
                    return true;
                }                   
            default:
                return false;
        }
    }

}