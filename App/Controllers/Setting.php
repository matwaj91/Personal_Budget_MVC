<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\AccountSettings;

class Setting extends \Core\Controller{

    protected function before(){

        parent::before();

        $this->user = Auth::getUser();
    }

    public function changeAccountAction(){

        View::renderTemplate('Menu/user.twig', [
            'user' => $this->user
        ]);
    }

    public function editAccountAction(){

        View::renderTemplate('Menu/editProfile.twig', [
            'user' => $this->user
        ]);
    }

    public function updateAction(){

        if ($this->user->updateProfile($_POST)) {

            Flash::addMessage('Changes were successfully saved!');

            $this->redirect('/Menu/main');

        } else {

            View::renderTemplate('Menu/editProfile.twig', [
                'user' => $this->user
            ]);
        }
    }

    public function deleteTransactionsAction(){

		$deleteTransaction = new AccountSettings();

		if($deleteTransaction->deleteAllTransactions()){

			Flash::addMessage("All transactions have been deleted!");
			$this->redirect('/Menu/main');
		}
		else{
            
			Flash::addMessage("An unknown error occurred. Please try again!", Flash::WARNING);
			$this->redirect('/Menu/main');
		}
	}

    public function deleteAccountAction(){

		if(AccountSettings::deleteAccount()){		

			Auth::logout();
			Flash::addMessage("Account has been deleted");
			$this->redirect('/');
		}
		else{

			Flash::addMessage("An unknown error occurred. Please try again!", Flash::WARNING);
			$this->redirect('/');
		}
	}
}
