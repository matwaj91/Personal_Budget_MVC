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

    public function addCategoryAction(){

		$newCategory = new Incomes($_POST);

		if($newCategory->addCategory()){

			Flash::addMessage("New category has been added!");
			View::renderTemplate('Menu/incomeCategories.twig');
		}
        else{

            Flash::addMessage("Category already exists!", Flash::WARNING);
			View::renderTemplate('Menu/incomeCategories.twig');
        }
	}

    public function deleteCategoryAction(){

		$deleteCategory = new Incomes($_POST);

		if($deleteCategory->deleteCategory()){

			Flash::addMessage("Selected category has been deleted!");
			View::renderTemplate('Menu/incomeCategories.twig');
		}
        else{
            Flash::addMessage("There are already saved incomes in the category you want to delete!", Flash::WARNING);
			View::renderTemplate('Menu/deleteIncomeCategory.twig');
        }
	}

    public function deleteCategoryIfTransactionsExistAction(){
		
		$existCategoryTransactions = new Incomes($_POST);
		
		if($existCategoryTransactions->deleteCategoryWithTransactions() == 1 ){

			Flash::addMessage("Category and all assigned incomes have been deleted!");
			View::renderTemplate('Menu/incomeCategories.twig');
		}
		else if ($existCategoryTransactions->deleteCategoryWithTransactions() == 2 ){

			Flash::addMessage('Category has been deleted and all assigned transactions have been transferred to category "Another"!');
			View::renderTemplate('Menu/incomeCategories.twig');
		}
	}
}
