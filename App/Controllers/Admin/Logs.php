<?php

namespace App\Controllers\Admin;

use \Core\View;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Logs extends \Core\Controller
{
    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $arg =  $this->route_params;
        View::renderTemplate('Admin/Logs/index.html', $arg);
    }
 
}
