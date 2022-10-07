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
}