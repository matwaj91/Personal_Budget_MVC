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

            $sql = "SELECT name, category_limit FROM expenses_category_assigned_to_users WHERE user_id = :userId";

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

    public function checkIfCategoryExists($newCategoryName)
	{
		if($user = Auth::getUser())
		{
            $results = $this->getUserExpensesCategories();
			
			foreach($results as $result){

				if($result['name'] == $newCategoryName)
				return false;
			}
			return true;
		}				
	}

    public function addCategory(){
        
		$this->category = strtolower($this->category); //The strtolower() function is used to convert a string into lowercase.
		$newCategory = ucfirst($this->category); //Make a string's first character uppercase

        if($this->checkIfCategoryExists($newCategory)){

            if($user = Auth::getUser()) {

                $userId = $user->id;		
                
                $sql = "INSERT INTO expenses_category_assigned_to_users(user_id, name) VALUES( :userId, :category)";
                    
                $db = static::getDB();
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
                $stmt->bindValue(':category', $newCategory, PDO::PARAM_STR);
                
                return $stmt->execute();
            }
        }
    }

    public function getCategoryId($deleteCategoryName)
	{
		if($user = Auth::getUser()){

			$userId = $user->id;
				
			$sql = "SELECT id FROM expenses_category_assigned_to_users 
                    WHERE user_id = :userId AND name = :name LIMIT 1";
            
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
            $stmt->bindValue(':name', $deleteCategoryName, PDO::PARAM_STR);
            $stmt->execute();

            $fetchArray = $stmt->fetch(PDO::FETCH_ASSOC);

            return $fetchArray['id'] ?? 'default value';
		}
	}

    public function deleteSelectedFromCategories($id){
			
        $sql = "DELETE FROM expenses_category_assigned_to_users WHERE id = :id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
	}

    public function checkIfTransactionExists($categoryId){

        $sql = "SELECT * FROM expenses WHERE expense_category_assigned_to_user_id = :id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $categoryId, PDO::PARAM_STR);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($results)){

            return false;
        }
        else{
            return $results;
        }
    }

    public function deleteCategory(){

		$_SESSION['$categoryId'] = $this->getCategoryId($this->category);

        $categoryId = $_SESSION['$categoryId'];

        if($this->checkIfTransactionExists($categoryId) == false){

            $this->deleteSelectedFromCategories($categoryId);
            return true;
        }
        else{
            return false;
        }
	}

    public function deleteAllAssignedTransactions($categoryId){	
		
		$sql = "DELETE FROM expenses WHERE expense_category_assigned_to_user_id = :id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
	}

    public function transferTransaction($categoryId){
		
		$anotherId = $this->getCategoryId("Another");

		$sql = "UPDATE expenses SET expense_category_assigned_to_user_id = :anotherId WHERE expense_category_assigned_to_user_id = :categoryId";			
		
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':anotherId', $anotherId, PDO::PARAM_INT);
        $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
	}

    public function deleteCategoryWithTransactions(){

        $categoryId = $_SESSION['$categoryId'];

		if($this->deleteCategory == "delete"){

			$this->deleteAllAssignedTransactions($categoryId);
			$this->deleteSelectedFromCategories($categoryId);
			return 1;
		}
		else if($this->deleteCategory == "transfer"){

			$this->transferTransaction($categoryId);
            $this->deleteSelectedFromCategories($categoryId);
			return 2;
		}
    }

    public function validateLimit(){

        $amountInteger = $_POST['limitAmount'];

        $amountString = (string)$amountInteger;

        if($amountString[0] == "0"){

            return false;
        }
        
        if ($this->limitAmount == ''){
            
            return false;
        }
    
        if (!is_numeric($this->limitAmount)){

            return false;
        }

        if ($this->limitAmount > 100000){
            
            return false;
        }
                    
        $dot = '.';
        $checkIfDotExists = strpos($this->limitAmount, $dot); //Find the position of the first occurrence of "."
        
        if($checkIfDotExists == true){

            return false;
        }
        
        return true;
	}

    public function setLimit(){

		if($this->validateLimit()){

			if($user = Auth::getUser()){

				$userId = $user->id;
				
				$sql = "UPDATE expenses_category_assigned_to_users SET category_limit = :limitAmount WHERE name = :name AND user_id = :userId";

				$db = static::getDB();
				$stmt = $db->prepare($sql);
                $stmt->bindValue(':limitAmount', $this->limitAmount, PDO::PARAM_INT);
                $stmt->bindValue(':name', $this->category, PDO::PARAM_STR);
                $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
				$stmt->execute();

				return true;
			}
		}

		else return false;
	}
}