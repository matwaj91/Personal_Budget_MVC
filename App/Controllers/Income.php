<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Incomes;
use \App\Auth;
use \App\Flash;
use \App\Models\displayIncomes;

class Income extends \Core\Controller
{
	public function newAction(){
        View::renderTemplate('Menu/addIncome.html');
    }

    public function addAction(){
		$income = new Incomes($_POST);

        if ($income->save()) {

            Flash::addMessage('Income has been added!');

            $this->redirect('/Menu/income');

        } else {

            View::renderTemplate('Menu/addIncome.html',[
                'income' => $income
            ]);    
        }
    }

    public function displayIncomesAction(){

		displayIncomes::displayAllIncomes($_POST);
	}
}
