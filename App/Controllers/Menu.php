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

	public function previousMonthAction(){	

		$previousMonth = date('m') -1;
		if($previousMonth < 10){

			$previousMonth = '0'."$previousMonth";
		}

		$previousMonthDaysOfNumber = date('t', strtotime("-1 MONTH"));

		if($previousMonth == 12){

			$year = date('Y') -1;
		}
		else $year = date('Y');
		
		$_SESSION['dateFrom'] =  "$year".'-'."$previousMonth".'-01';
		$_SESSION['dateTo'] =  "$year".'-'."$previousMonth".'-'."$previousMonthDaysOfNumber";
		
		View::renderTemplate('Menu/showBalance.html');
	}

	public function currentYearAction(){	

		$currentYear = date('Y');
		$_SESSION['dateFrom'] = "$currentYear".'-01-01';
		$_SESSION['dateTo'] = "$currentYear".'-12-31';
		
		View::renderTemplate('Menu/showBalance.html');
	}

	public function nonstandardAction(){	

		$_SESSION['dateFrom'] = $_POST['dateFrom'];
		$_SESSION['dateTo'] = $_POST['dateTo'];	
		
		if(($_SESSION['dateFrom']) > ($_SESSION['dateTo'])){

			Flash::addMessage('The specified date range is not valid as first chosen date cannot be later than the second one!', Flash::WARNING);

			View::renderTemplate('Menu/main.html');
		}
		else {
			View::renderTemplate('Menu/showBalance.html');
		}
	}
}