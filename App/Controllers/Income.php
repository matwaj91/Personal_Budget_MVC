<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Incomes;
use \App\Auth;
use \App\Flash;

class Income extends \Core\Controller{

	public function newAction(){
        View::renderTemplate('Menu/addIncome.twig');
    }

    public function changeCategoryAction(){
        View::renderTemplate('Menu/incomeCategories.twig');
    }

    public function addAction(){
		$income = new Incomes($_POST);

        if ($income->save()) {
            Flash::addMessage('Income has been added!');
            $this->redirect('/Menu/income');

        } else {
            View::renderTemplate('Menu/addIncome.twig',[
                'income' => $income
            ]);    
        }
    }

    public function displayIncomeCategoriesAction(){

        echo json_encode(Incomes::getUserIncomesCategories(), JSON_UNESCAPED_UNICODE);
    }
}
