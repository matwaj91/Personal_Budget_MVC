<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\AccountSettings;
use \App\Models\PaymentMethods;


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

    public function changeCategoryAction(){
        View::renderTemplate('Menu/paymentMethods.twig');
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

    public function displayPaymentMethodsAction(){

        echo json_encode(PaymentMethods::getUserPaymentMethods(), JSON_UNESCAPED_UNICODE);
    }

    public function addPaymentMethodAction(){

		$newPaymentMethod = new PaymentMethods($_POST);

		if($newPaymentMethod->addPaymentMethod()){

			Flash::addMessage("New payment method has been added!");
			View::renderTemplate('Menu/paymentMethods.twig');
		}
        else{

            Flash::addMessage("Payment method already exists!", Flash::WARNING);
			View::renderTemplate('Menu/paymentMethods.twig');
        }
	}

    public function deletePaymentMethodAction(){

		$deleteCategory = new PaymentMethods($_POST);

		if($deleteCategory->deletePaymentMethod()){

			Flash::addMessage("Selected payment method has been deleted!");
			View::renderTemplate('Menu/paymentMethods.twig');
		}
	}
}
