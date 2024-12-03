<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Event;

class CheckAuthorizationService{

    public function checkProcess(User $user, string $route, ?Event $event)
    {    
        switch ($route) {
            case "admin_activity_edit":
                if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                    return true;
                }
            case "admin_event_edit":
                if(in_array('ROLE_SUPER_ADMIN', $user->getRoles()) || $user->getId() == $event->getUser()->getId()) {
                    return true;
                }
            case "admin_event_delete":
                    if(in_array('ROLE_SUPER_ADMIN', $user->getRoles()) || $user->getId() == $event->getUser()->getId()) {
                        return true;
                    }
            case "admin_administrator_index":
                if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                    return true;
                }  
            case "admin_administrator_new":
                if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                    return true;
                }  
            case "admin_administrator_edit":
                if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                    return true;
                }   
            case "admin_administrator_delete":
                if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                    return true;
                }  
            case "admin_animator_new":
                if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                    return true;
                }  
            case "admin_animator_edit":
                if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                    return true;
                }   
            case "admin_animator_delete":
                if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                    return true;
                }
            case "admin_notification_new":
                if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                    return true;
                }  
            case "admin_notification_edit":
                if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                    return true;
                }   
            case "admin_notification_delete":
                if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                    return true;
                }               
            default:
                return false;
        }
    }

}