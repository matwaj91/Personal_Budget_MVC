<?php

namespace Core;

class View
{

    public static function render($view, $args = []){
        extract($args, EXTR_SKIP);

        $file = "../App/Views/$view";  

        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("$file not found");
        }
    }

    public static function renderTemplate($template, $args = [])
    {
        echo static::getTemplate($template, $args);
    }

    public static function getTemplate($template, $args = [])
    {
        static $twig = null;

        if ($twig === null) {
            $loader = new \Twig\Loader\FilesystemLoader('../App/Views');
            $twig = new \Twig\Environment($loader);
            $twig->addGlobal('current_user', \App\Auth::getUser());
            $twig->addGlobal('flash_messages', \App\Flash::getMessages());
            $twig->addGlobal('user_incomes',\App\Models\Incomes::getCategorySumIncomes());
            $twig->addGlobal('individual_income',\App\Models\Incomes::getDataOfIndividualIncome());
            

			//$twig->addGlobal('expenses',\App\Models\Expenses::getAll());
            //$twig->addGlobal('payment_methods',\App\Models\PaymentMethods::getAll());
			//$twig->addGlobal('user_expenses',\App\Models\Expenses::getAllUserExpenses());
			//$twig->addGlobal('selected_incomes',\App\Models\ShowIncomes::getIncomeData());
			//$twig->addGlobal('selected_expenses',\App\Models\ShowExpenses::getExpenseData());
        }

        return $twig->render($template, $args);
    }
}
