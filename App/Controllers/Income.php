<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Incomes;
use \App\Auth;
use \App\Flash;

class Income extends \Core\Controller
{
	public function newAction(){
        View::renderTemplate('Menu/addIncome.html');
    }

    public function addAction(){
		$income = new Incomes($_POST);
		
        if ($income->save()) {

            Flash::addMessage('Dodano przychód');

        } else {

            Flash::addMessage('Niepoprawne dane', Flash::WARNING);

            View::renderTemplate('Menu/addIncome.html',[
                'income' => $income
            ]);    
        }
    }
}
