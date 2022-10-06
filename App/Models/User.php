<?php

namespace App\Models;

use PDO;
use \App\Token;
use \Core\View;
use \App\Mail;

class User extends \Core\Model
{
    public $id;
 
    public $username;

    public $password;
 
    public $email;
 
    public $errors = [];

    public function __construct($data = []){
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    public function save(){

        $this->validate();

        if (empty($this->errors)) {

            $password_hash=password_hash($this->password, PASSWORD_DEFAULT);

            $token = new Token();
            $hashed_token = $token->getHash();
            $this->activation_token = $token->getValue();
            
            $sql='INSERT INTO users (username, email, password, activation_hash) 
            VALUES(:name,  :email, :password, :activation_hash)';
    
            $db=static::getDB();
            $stmt=$db->prepare($sql);
    
            $stmt->bindValue(':name', $this -> name, PDO::PARAM_STR);
            $stmt->bindValue(':password', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this -> email, PDO::PARAM_STR);
            $stmt->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);
            
            return $stmt->execute();
        }

        return false;
    }

    public function validate(){
        if ($this->name == '') {
            $this->errors[] = 'Name is required';
        }

        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'Invalid email';
        }
        if (static::emailExists($this->email, $this->id ?? null)) {
            $this->errors[] = 'email already taken';
        }

        if (isset($this->password)) {

            if (strlen($this->password) < 6) {
                $this->errors[] = 'Please enter at least 6 characters for the password';
            }

            if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
                $this->errors[] = 'Password needs at least one letter';
            }

            if (preg_match('/.*\d+.*/i', $this->password) == 0) {
                $this->errors[] = 'Password needs at least one number';
            }
        }
    }

    public static function emailExists($email, $ignore_id = null)
    {
        $user = static::findByEmail($email);

        if ($user) {
            if ($user->id != $ignore_id) {
                return true;
            }
        }
        return false;
    }

    public static function authenticate ($email, $password){
        $user = static::findByEmail($email);

        if ($user && $user->is_active) {
            if (password_verify($password, $user->password)) {
                return $user;
            }
        }

        return false;
    }

    public static function findByEmail ($email){
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public static function findByID($id){
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public function rememberLogin(){
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();

        $this->expiry_timestamp = time() + 60 * 60 * 24 * 30; 

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                    VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function sendActivationEmail(){
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . $this->activation_token;

        $text = View::getTemplate('Signup/activation_email.txt', ['url' => $url]);
        $html = View::getTemplate('Signup/activation_email.html', ['url' => $url]);
		
		Mail::send($this->email, 'Account activation', $html, $text);   
    }

    public static function findByToken($hashed_token)
    {
        $sql = 'SELECT * FROM users WHERE activation_hash = :hashed_token';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public static function activate($value){
        $token = new Token($value);
        $hashed_token = $token->getHash();
		$user = static::findByToken($hashed_token);

        $sql = 'UPDATE users
                SET is_active = 1,
                    activation_hash = null
                WHERE activation_hash = :hashed_token';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);

        $stmt->execute();

        $userId = $user->id;		 
		$sql = "INSERT INTO incomes_category_assigned_to_users SELECT 'NULL','$userId',name FROM incomes_category_default";
		$db = static::getDB();
        $stmt = $db->prepare($sql);
		$stmt->execute();
		
		$sql = "INSERT INTO expenses_category_assigned_to_users SELECT 'NULL','$userId',name FROM expenses_category_default";
		$db = static::getDB();
        $stmt = $db->prepare($sql);
		$stmt->execute();
		
		$sql = "INSERT INTO payment_methods_assigned_to_users SELECT 'NULL','$userId',name FROM payment_methods_default";
		$db = static::getDB();
        $stmt = $db->prepare($sql);
		$stmt->execute();
    }
}
