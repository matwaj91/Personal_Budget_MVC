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
		if($user = Auth::getUser()){

            $results = $this->getUserPaymentMethods();
			
			foreach($results as $result){

				if($result['name'] == $newPaymentMethod)
				return false;
			}
			return true;
		}				
	}

    public function addPaymentMethod(){
        
		$this->payment = strtolower($this->payment); //The strtolower() function is used to convert a string into lowercase.
		$newMethod = ucfirst($this->payment); //Make a string's first character uppercase

        if($this->checkIfPaymentMethodExists($newMethod)){

            if($user = Auth::getUser()) {

                $userId = $user->id;		
                
                $sql = "INSERT INTO payment_methods_assigned_to_users(user_id, name) VALUES( :userId, :paymentMethod)";
                    
                $db = static::getDB();
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
                $stmt->bindValue(':paymentMethod', $newMethod, PDO::PARAM_STR);
                
                return $stmt->execute();
            }
        }
    }

    public function getPaymentMethodId($deletePaymentMethod){

		if($user = Auth::getUser()){

			$userId = $user->id;
				
			$sql = "SELECT id FROM payment_methods_assigned_to_users 
                    WHERE user_id = :userId AND name = :name LIMIT 1";
            
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
            $stmt->bindValue(':name', $deletePaymentMethod, PDO::PARAM_STR);
            $stmt->execute();

            $fetchArray = $stmt->fetch(PDO::FETCH_ASSOC);

            return $fetchArray['id'] ?? 'default value';
		}
	}

    public function deleteSelectedPaymentMethod($id){
			
        $sql = "DELETE FROM payment_methods_assigned_to_users WHERE id = :id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
	}

    public function deletePaymentMethod(){

		$paymentMethodId = $this->getPaymentMethodId($this->payment);
				
		$this->deleteSelectedPaymentMethod($paymentMethodId);

        return true;
	}
}