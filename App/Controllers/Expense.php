<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Expenses;
use \App\Auth;
use \App\Flash;

class Expense extends \Core\Controller
{
	public function newAction(){
        View::renderTemplate('Menu/addExpense.twig');
    }

    public function changeCategoryAction(){
        View::renderTemplate('Menu/expenseCategories.twig');
    }

    public function addAction(){
		$expense = new Expenses($_POST);

        if ($expense->save()) {
            Flash::addMessage('Expense has been added!');
            $this->redirect('/Menu/expense');

        } else {
            View::renderTemplate('Menu/addExpense.twig',[
                'expense' => $expense
            ]);    
        }
    }

    public function displayExpenseCategoriesAction(){

        echo json_encode(Expenses::getUserExpensesCategories(), JSON_UNESCAPED_UNICODE);
    }

    public function addCategoryAction(){

		$newCategory = new Expenses($_POST);

		if($newCategory->addCategory()){

			Flash::addMessage("New category has been added!");
			View::renderTemplate('Menu/expenseCategories.twig');
		}
        else{

            Flash::addMessage("Category already exists!", Flash::WARNING);
			View::renderTemplate('Menu/expenseCategories.twig');
        }
	}

    public function deleteCategoryAction(){

		$deleteCategory = new Expenses($_POST);

		if($deleteCategory->deleteCategory()){

			Flash::addMessage("Selected category has been deleted!");
			View::renderTemplate('Menu/expenseCategories.twig');
		}
        else{
            //Flash::addMessage("", Flash::WARNING);
			//View::renderTemplate('Menu/expenseCategories.twig');
        }
	}
}