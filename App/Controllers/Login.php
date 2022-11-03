<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Account;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Login extends \Core\Controller
{
    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {

        $arg =  $this->route_params;
        View::renderTemplate('Login/index.html', $arg);
    }

    public function authAction()
    {   
        $account = new Account();
        if(isset($_POST["username"]) && isset($_POST["passwd"])) 
        {
            $arg = $login = $account->login($_POST['username'], $_POST['passwd']);
        }else{

            $status['status'] = 'error';
            $status['status-message'] = "Por Favor, preencha todos os campos.";
            $arg = $status;
        }
        //View
        header('Content-Type: application/json');
        echo json_encode($arg);
    }

    public function signUpAction()
    {   
        $account = new Account();

        if(isset($_POST["name"]) && isset($_POST["username"]) && isset($_POST["passwd"]) && isset($_POST["cpasswd"])) 
        {
            $arg = $addUser = $account->addAccount($_POST['name'], $_POST['username'], $_POST['passwd'], $_POST['cpasswd'], 'colaborador');
        }else{

            $status['status'] = 'error';
            $status['status-message'] = "Por Favor, preencha todos os campos.";

            $arg = $status;
        }
        //View
        header('Content-Type: application/json');
        echo json_encode($arg);
    }
 
}
