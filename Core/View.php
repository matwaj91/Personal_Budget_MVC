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
            $twig->addGlobal('individual_incomes',\App\Models\Incomes::getDataOfIndividualIncome());
            $twig->addGlobal('user_expenses',\App\Models\Expenses::getCategorySumExpenses());
            $twig->addGlobal('individual_expenses',\App\Models\Expenses::getDataOfIndividualExpense());
        }

        return $twig->render($template, $args);
    }
}
