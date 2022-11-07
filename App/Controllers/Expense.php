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
}