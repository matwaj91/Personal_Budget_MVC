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
}