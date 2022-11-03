<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Account;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Me extends \Core\Controller
{
    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $arg =  $this->route_params;
        //$arg['id_user_session'] = $_SESSION['account_id'];
        View::renderTemplate('Me/index.html', $arg);
    }
    public function logoutAction()
    {
        $account = new Account();
        $account->logout();
        header("location: /login");
        exit;
    }
}
