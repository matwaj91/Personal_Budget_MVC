<?php

namespace App\Models;

use PDO;
use App\Auth;

class AccountSettings extends \Core\Model{

    public function __construct($data = []){

        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    public static function deleteAllTransactions(){

		if($user = Auth::getUser()){

            $userId = $user->id;

            $sql = "DELETE FROM expenses WHERE user_id = :userId";	

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
            if($stmt->execute()){

                $sql = "DELETE FROM incomes WHERE user_id = :userId";	
                
                $db = static::getDB();
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
                return $stmt->execute();
            }
		}
		return false;
	}

    public static function deleteAccount(){

		if($user = Auth::getUser()){

            $userId = $user->id;

            AccountSettings::deleteAllTransactions();

            $sql1 ="DELETE FROM payment_methods_assigned_to_users WHERE user_id = :userId";
            
            $db = static::getDB();
            $stmt = $db->prepare($sql1);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);

            if($stmt->execute()){		

                $sql2 ="DELETE FROM incomes_category_assigned_to_users WHERE user_id = :userId";
                
                $db = static::getDB();
                $stmt = $db->prepare($sql2);
                $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);

                if($stmt->execute()){

                    $sql3 ="DELETE FROM expenses_category_assigned_to_users WHERE user_id = :userId";

                    $db = static::getDB();
                    $stmt = $db->prepare($sql3);
                    $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);

                    if($stmt->execute()){

                        $sql4 ="DELETE FROM users WHERE id = :userId";

                        $db = static::getDB();
                        $stmt = $db->prepare($sql4);
                        $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);

                        if($stmt->execute()){
                            
                            return true;
                        }
                    }
                }
            }
            return false;	
		}
	}
}