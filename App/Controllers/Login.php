<?php   

namespace   App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

class Login extends \Core\Controller
{
    public function newAction(){
        View::renderTemplate('Home/index.twig');
    }

    public function createAction(){
        $user = User::authenticate($_POST['email'], $_POST['password']);

        $remember_me = isset($_POST['remember_me']);

        if($user) {
            Auth::login($user, $remember_me);
            $this->redirect('/Menu/main');
        }else {
            Flash::addMessage('Authenticated failed! Please check your credentials and try again.', Flash::WARNING);
            View::renderTemplate('Home/index.twig', [
            'email' => $_POST['email'],
            'remember_me' => $remember_me
            ]);
        }
    }

    public function destroyAction(){
        Auth::logout();
        $this->redirect('/login/show-logout-message');
    }

    public function showLogoutMessageAction(){
      Flash::addMessage('You have successfully logged out!');
      $this->redirect('/');
    }
}