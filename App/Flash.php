<?php

namespace App;

class Flash
{
    const SUCCESS = 'positive';

    const INFO = 'informative';

    const WARNING = 'negative';

    public static function addMessage($message, $type = 'positive'){
        if (! isset($_SESSION['flash_notifications'])) {
            $_SESSION['flash_notifications'] = [];
        }

        $_SESSION['flash_notifications'][] = [
            'body' => $message,
            'type' => $type
        ];
    }

    public static function getMessages(){

        if (isset($_SESSION['flash_notifications'])) {
            $messages = $_SESSION['flash_notifications'];
            unset($_SESSION['flash_notifications']);

            return $messages;
        }
    }
}