<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

class Menu extends Authenticated
{
    protected function before(){
        parent::before();

        $this->user = Auth::getUser();
    }

	public function mainAction(){	
		View::renderTemplate('Menu/main.html');
	}

    public function incomeAction(){	
		View::renderTemplate('Menu/addIncome.html');
	}

    public function expenseAction(){	
		View::renderTemplate('Menu/addExpense.html');
	}

    public function currentMonthAction(){	

		$currentMonth = date('m');
		$currentYear = date('Y');
		$daysOfNumber = date('t');
		
		$_SESSION['dateFrom'] = "$currentYear".'-'."$currentMonth".'-01';
		$_SESSION['dateTo'] = "$currentYear".'-'."$currentMonth".'-'."$daysOfNumber";
	
		View::renderTemplate('Menu/showBalance.html');

	}

}