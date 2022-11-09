<?php

namespace App\Models;

use PDO;
use App\Auth;

class Incomes extends \Core\Model
{
	
	public function __construct($data = []){
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    public static function getUserIncomesCategories(){

		if($user = Auth::getUser()){

			$userId = $user->id;

            $sql = "SELECT name FROM incomes_category_assigned_to_users WHERE user_id = :userId";

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

            $sql = "SELECT id FROM incomes_category_assigned_to_users 
                    WHERE user_id = :userId AND name = :name LIMIT 1";
            
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
            $stmt->bindValue(':name', $this->category, PDO::PARAM_STR);
            $stmt->execute();

            $fetchArray = $stmt->fetch(PDO::FETCH_ASSOC);
            $income_category_id = $fetchArray['id'];


            $sql = "INSERT INTO incomes(user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment) 
                    VALUES( :userId, :income_category_id, :amount, :date, :comment)";
            
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
            $stmt->bindValue(':income_category_id', $income_category_id, PDO::PARAM_STR);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);          
            $stmt->bindValue(':date', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);

            return $stmt->execute();
        }
        else{
            return false;
        }
    }

    public static function getCategorySumIncomes(){
        
		if($user = Auth::getUser()){

			$userId = $user->id;

			if (isset($_SESSION['dateFrom']) && isset($_SESSION['dateTo']) ){

			    $dateFrom = $_SESSION['dateFrom'];
			    $dateTo =$_SESSION['dateTo'] ;

			}else{

			    $dateFrom = '';
			    $dateTo = '';
			}

            $sql = "SELECT name, SUM(amount) AS sum FROM incomes,incomes_category_assigned_to_users AS category 
                    WHERE incomes.user_id = :userId AND category.id = incomes.income_category_assigned_to_user_id 
                    AND date_of_income BETWEEN :dateFrom AND :dateTo GROUP BY name";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
            $stmt->bindValue(':dateFrom', $dateFrom, PDO::PARAM_STR);
            $stmt->bindValue(':dateTo', $dateTo, PDO::PARAM_STR);
            $stmt->execute();
		
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
	}

    public static function getDataOfIndividualIncome(){

		if($user = Auth::getUser()){

            $userId = $user->id;

            if (isset($_SESSION['dateFrom']) && isset($_SESSION['dateTo']) ){
			    $dateFrom = $_SESSION['dateFrom'];
			    $dateTo =$_SESSION['dateTo'] ;

			}else{
			    $dateFrom = '';
			    $dateTo = '';
			}

            $sql = "SELECT incomes.amount AS individual_amount, incomes.date_of_income AS individual_date, incomes_category_assigned_to_users.name 
                    AS nameOfCategory, incomes.income_comment AS comment FROM incomes INNER JOIN incomes_category_assigned_to_users 
                    on incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id 
                    WHERE incomes.user_id = :userId AND date_of_income BETWEEN :dateFrom AND :dateTo";
                                
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
            $results = $this->getUserIncomesCategories();
			
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
                
                $sql = "INSERT INTO incomes_category_assigned_to_users(user_id, name) VALUES( :userId, :category)";
                    
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
				
			$sql = "SELECT id FROM incomes_category_assigned_to_users 
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
			
        $sql = "DELETE FROM incomes_category_assigned_to_users WHERE id = :id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
	}

    public function deleteCategory(){

		$categoryId = $this->getCategoryId($this->category);
				
		$this->deleteSelectedFromCategories($categoryId);

        return true;
	}
}