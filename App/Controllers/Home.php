<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

class Home extends \Core\Controller
{
    protected function before()
	{	
	}
	
	protected function after()
	{
	}
    
    public function indexAction(){
        View::renderTemplate('Home/index.twig');
    }
}
