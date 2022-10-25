<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;

class Profile extends Authenticated{

    protected function before(){

        parent::before();

        $this->user = Auth::getUser();
    }

    public function editAction(){

        View::renderTemplate('Profile/edit.twig', [
            'user' => $this->user
        ]);
    }

    public function updateAction(){

        if ($this->user->updateProfile($_POST)) {

            Flash::addMessage('Changes were successfully saved!');

            $this->redirect('/Menu/main');

        } else {

            View::renderTemplate('Profile/edit.twig', [
                'user' => $this->user
            ]);
        }
    }
}
