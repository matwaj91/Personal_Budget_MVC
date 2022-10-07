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

    public function save(){
		
        if($user = Auth::getUser()){

            $userId = $user->id;
            
            $db = static::getDB();
            $stmt = $db->query("SELECT id FROM incomes_category_assigned_to_users WHERE user_id = '$userId' AND name ='$this->category'");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $income_category_id = $results[0]['id'];

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

			    $datefrom = '';
			    $dateTo = '';
			}
		
			$db = static::getDB();
            $stmt = $db->query("SELECT name, SUM(amount) AS sum FROM incomes,incomes_category_assigned_to_users AS category WHERE incomes.user_id = '$userId' AND category.id = incomes.income_category_assigned_to_user_id AND date_of_income BETWEEN '$dateFrom' AND '$dateTo' GROUP BY name");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;
		}
	}

    public static function getDataOfIndividualIncome(){

		if($user = Auth::getUser()){

            $userId = $user->id;

            if (isset($_SESSION['dateFrom']) && isset($_SESSION['dateTo']) ){

			    $dateFrom = $_SESSION['dateFrom'];
			    $dateTo =$_SESSION['dateTo'] ;

			}else{

			    $datefrom = '';
			    $dateTo = '';
			}

            $db = static::getDB();
            $stmt = $db->query("SELECT incomes.amount AS individual_amount, incomes.date_of_income AS individual_date, incomes_category_assigned_to_users.name as nameOfCategory FROM incomes INNER JOIN incomes_category_assigned_to_users on incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id WHERE incomes.user_id = '$userId' AND date_of_income BETWEEN '$dateFrom' AND '$dateTo'");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "<pre>";
                print_r($results);
            echo "</pre>";
            
        }
    }
}