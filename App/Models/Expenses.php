<?php

namespace App\Models;

use PDO;
use App\Auth;


class Expenses extends \Core\Model
{
	public $faults = [];
	
	public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    public function save()
    {
		
        if($user = Auth::getUser()){
            
            $userId = $user->id;
            
            $db = static::getDB();
            $stmt = $db->query("SELECT id FROM expenses_category_assigned_to_users WHERE user_id = '$userId' AND name ='$this->category'");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $expense_category_id = $results[0]['id'];
            
            $db = static::getDB();
            $stmt = $db->query("SELECT id FROM payment_methods_assigned_to_users WHERE user_id = '$userId' AND name ='$this->payment_method'");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $payment_method_id = $results[0]['id'];
            
            $sql = "INSERT INTO expenses(user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment) 
            VALUES( :userId, :expense_category_id, :payment_method_id, :amount, :date, :comment)";
            
            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
            $stmt->bindValue(':expense_category_id', $expense_category_id, PDO::PARAM_STR);
            $stmt->bindValue(':payment_method_id', $payment_method_id, PDO::PARAM_STR);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);          
            $stmt->bindValue(':date', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);

            return $stmt->execute();
        }
        else{
            return false;
        }
	}

    public static function getCategorySumExpenses(){
        
		if($user = Auth::getUser()){

			$userId = $user->id;

			if (isset($_SESSION['dateFrom']) && isset($_SESSION['dateTo']) ){

			    $dateFrom = $_SESSION['dateFrom'];
			    $dateTo =$_SESSION['dateTo'] ;

			}else{

			    $dateFrom = '';
			    $dateTo = '';
			}
		
			$db = static::getDB();
            $stmt = $db->query("SELECT name, SUM(amount) AS sum FROM expenses,expenses_category_assigned_to_users AS category WHERE expenses.user_id = '$userId' AND category.id = expenses.expense_category_assigned_to_user_id AND date_of_expense BETWEEN '$dateFrom' AND '$dateTo' GROUP BY name");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;
		}
	}

    public static function getDataOfIndividualExpense(){

		if($user = Auth::getUser()){

            $userId = $user->id;

            if (isset($_SESSION['dateFrom']) && isset($_SESSION['dateTo']) ){

			    $dateFrom = $_SESSION['dateFrom'];
			    $dateTo =$_SESSION['dateTo'] ;

			}else{

			    $dateFrom = '';
			    $dateTo = '';
			}

            $db = static::getDB();
            $stmt = $db->query("SELECT expenses.amount AS individual_amount, expenses.date_of_expense AS individual_date, expenses_category_assigned_to_users.name AS nameOfCategory, expenses.expense_comment AS comment FROM expenses INNER JOIN expenses_category_assigned_to_users on expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id WHERE expenses.user_id = '$userId' AND date_of_expense BETWEEN '$dateFrom' AND '$dateTo'");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;
            
        }
    }
}