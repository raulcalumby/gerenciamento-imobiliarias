<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Account;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class AccountActions extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */

    public function sessionAction(){

        $valid = FALSE;
		if(isset($_SESSION["id"])) {
            $valid = TRUE;
        }
        //return $valid;
        //var_dump($_SESSION);
        var_dump($valid);
    }
    public function loginAction()
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
    public function addUserAction()
    {   
        $account = new Account();

        if(isset($_POST["name"]) && isset($_POST["username"]) && isset($_POST["passwd"]) && isset($_POST["cpasswd"]) && isset($_POST["level"])) 
        {
            $arg = $addUser = $account->addAccount($_POST['name'], $_POST['username'], $_POST['passwd'], $_POST['cpasswd'], $_POST['level']);
        }else{

            $status['status'] = 'error';
            $status['status-message'] = "Por Favor, preencha todos os campos.";

            $arg = $status;
        }

        //View
        header('Content-Type: application/json');
        echo json_encode($arg);

    }

    public function editPassAccountAction()
    {   
        $account = new Account();

        if(isset($_POST["account_id"]) && isset($_POST["currentpasswd"]) && isset($_POST["passwd"]) && isset($_POST["cpasswd"])) 
        {
            $arg = $addUser = $account->editPassAccount($_POST['account_id'], $_POST['currentpasswd'], $_POST['passwd'], $_POST['cpasswd']);
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
