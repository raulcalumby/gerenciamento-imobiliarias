<?php

namespace App\Controllers\Admin;

use \Core\View;
use App\Models\User;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Users extends \Core\Controller
{

    public $id;
    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $arg =  $this->route_params;

        $usersApi = new User();
        $users_list = $usersApi->list();
        $arg['users'] = $users_list;
        //var_dump($arg);
        View::renderTemplate('Admin/Users/index.html', $arg);
    }
    public function meAction()
    {
        $arg =  $this->route_params;
        View::renderTemplate('Admin/Users/me.html', $arg);
    
    }
    public function newAction()
    {
        $arg =  $this->route_params;
        View::renderTemplate('Admin/Users/add.html', $arg);
    
    }

    public function editAction()
    {
        $arg =  $this->route_params;
        $usersApi = new User();
        $user_data = $usersApi->getUserByID(intval($this->route_params['id']));
        $user_data['account_level_user'] = $user_data['account_level'];
        unset($user_data['account_level']);

        $user_data['account_name_user'] = $user_data['account_name'];
        unset($user_data['account_name']);

        $user_data['account_username_user'] = $user_data['account_username'];
        unset($user_data['account_username']);
        
        $arg = array_merge($arg, $user_data);
        //var_dump($arg);
        View::renderTemplate('Admin/Users/edit.html', $arg);
    
    }

    public function updateAction()
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Por favor preencha todos os campos';

        $usersApi = new User();

       ////var_dump($_POST);
        $response = $usersApi->edit($_POST, intval($this->route_params['id']));
        ////var_dump($response);

        //View
        header('Content-Type: application/json');
        echo json_encode($response);    
    }

    public function disableAction()
    {   
        $usersApi = new User();

        $response['status'] = 'error';
        $response['status-message'] = 'Missing Data';

        if(isset($_POST['account_id'])  && !empty($_POST['account_id'])){
            $account_id = intval($_POST['account_id']);
            $response = $usersApi->disable($account_id);
        }
    
        //View
        header('Content-Type: application/json');
        echo json_encode($response);    
    }
}
