<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

class Menu extends Authenticated
{
    protected function before(){
        parent::before();

        $this->user = Auth::getUser();
    }

	public function mainAction(){	
		View::renderTemplate('Menu/main.html');
	}

    public function incomeAction(){	
		View::renderTemplate('Menu/addIncome.html');
	}

}