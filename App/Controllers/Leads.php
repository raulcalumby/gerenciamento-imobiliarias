<?php

namespace App\Controllers;

use App\Models\LeadsApi;
use \Core\View;
use App\Models\LivroCaixaApi;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Leads extends \Core\Controller
{

    public $id;
    public $singular_name = "Leads";
    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $arg =  $this->route_params;
        $arg['singular_name'] = $this->singular_name;
        View::renderTemplate('Leads/index.html', $arg);
    }

    public function addAction()
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Por Favor, preencha todos os campos';

        if (!empty($_POST) && isset($_POST['name'])) {
            $data = $_POST;
            $leadsApi = new LeadsApi();
            $response =  $leadsApi->add($data);
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function updateAction()
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Por Favor, preencha todos os campos';

        if (!empty($_POST) && isset($_POST['leads_id'])) {
            $data = $_POST;
            $leads_id = $_POST['leads_id'];
            $leadsApi = new LeadsApi();
            $response =  $leadsApi->edit($data, $leads_id);
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // Listar Locações
    public function listAction()
    {

        if (isset($_POST['columns'][0]['search']['value'])) {
            $nome = $_POST['columns'][0]['search']['value'];
        } else {
            $nome = "";
        }



        if (isset($_POST['start'])) {
            $offset = $_POST['start'];
        } else {
            $offset = 0;
        }

        if (isset($_POST['length'])) {
            $limit = $_POST['length'];
        } else {
            $limit = 15;
        }

        if (isset($_POST['draw'])) {
            $draw = $_POST['draw'];
        } else {
            $draw = 1;
        }

        $leadsApi = new LeadsApi();
        $response = $leadsApi->list($nome, $offset, $limit);

        $response['draw'] =  $draw;
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function disable()
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Missing Data';

        if (isset($_POST['leads_id'])  && !empty($_POST['leads_id']))
        {

            $leadsId = intval($_POST['leads_id']);
            $leadsApi = new LeadsApi();
            $response = $leadsApi->disable($leadsId);
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
