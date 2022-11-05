<?php

namespace App\Models;

use PDO;
use App\Auth;


class Expenses extends \Core\Model {

	public $faults = [];
	
	public function __construct($data = []){
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    public static function getUserExpensesCategories(){

		if($user = Auth::getUser()){

			$userId = $user->id;

            $sql = "SELECT name FROM expenses_category_assigned_to_users WHERE user_id = :userId";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
            $stmt->execute();
		
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
	}

    public function save(){
        
        if($user = Auth::getUser()){
            
            $userId = $user->id;
            
            $sql = "SELECT id FROM expenses_category_assigned_to_users 
                    WHERE user_id = :userId AND name = :name LIMIT 1";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
            $stmt->bindValue(':name', $this->category, PDO::PARAM_STR);
            $stmt->execute();

            $fetchArray = $stmt->fetch(PDO::FETCH_ASSOC);
            $expense_category_id = $fetchArray['id'];

            $sql = "SELECT id FROM payment_methods_assigned_to_users 
                    WHERE user_id = :userId AND name = :name LIMIT 1";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
            $stmt->bindValue(':name', $this->payment, PDO::PARAM_STR);
            $stmt->execute();

            $fetchArray = $stmt->fetch(PDO::FETCH_ASSOC);
            $payment_method_id = $fetchArray['id'];

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
		
            $sql = "SELECT name, SUM(amount) AS sum FROM expenses,expenses_category_assigned_to_users AS category 
                    WHERE expenses.user_id = :userId AND category.id = expenses.expense_category_assigned_to_user_id 
                    AND date_of_expense BETWEEN :dateFrom AND :dateTo GROUP BY name";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
            $stmt->bindValue(':dateFrom', $dateFrom, PDO::PARAM_STR);
            $stmt->bindValue(':dateTo', $dateTo, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

            $sql = "SELECT expenses.amount AS individual_amount, expenses.date_of_expense 
                    AS individual_date, expenses_category_assigned_to_users.name AS nameOfCategory, expenses.expense_comment 
                    AS comment FROM expenses INNER JOIN expenses_category_assigned_to_users on expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id 
                    WHERE expenses.user_id = :userId AND date_of_expense BETWEEN :dateFrom AND :dateTo";
                                
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
            $stmt->bindValue(':dateFrom', $dateFrom, PDO::PARAM_STR);
            $stmt->bindValue(':dateTo', $dateTo, PDO::PARAM_STR);
            $stmt->execute();
        
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}