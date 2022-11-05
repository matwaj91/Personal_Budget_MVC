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
}