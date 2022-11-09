<?php

namespace App\Models;

use PDO;
use App\Auth;

class PaymentMethods extends \Core\Model{

	public $faults = [];
	
	public function __construct($data = []){

        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }
				
	public static function getUserPaymentMethods(){

		if($user = Auth::getUser()){

			$userId = $user->id;

            $sql = "SELECT * FROM payment_methods_assigned_to_users WHERE user_id = :userId";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
            $stmt->execute();
		
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
	}

    public function checkIfPaymentMethodExists($newPaymentMethod)
	{
		if($user = Auth::getUser())
		{
            $results = $this->getUserPaymentMethods();
			
			foreach($results as $result){

				if($result['name'] == $newPaymentMethod)
				return false;
			}
			return true;
		}				
	}

    public function addPaymentMethod(){
        
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
}