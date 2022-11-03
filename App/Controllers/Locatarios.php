<?php

namespace App\Controllers;

use \Core\View;
use App\Models\LocatariosApi;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Locatarios extends \Core\Controller
{

    public $id;
    public $singular_name = "Locatários";
    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $arg =  $this->route_params;
        $arg['singular_name'] = $this->singular_name;
        View::renderTemplate('Locatarios/index.html', $arg);
    }
    //View Add Locatário
    public function novoAction()
    {
        $arg =  $this->route_params;
        $arg['singular_name'] = $this->singular_name;
        View::renderTemplate('Locatarios/add.html', $arg);
    }
    //View Edit Locatário
    public function editAction()
    {
        $arg =  $this->route_params;
        $arg['singular_name'] = $this->singular_name;
        $locatariosApi = new LocatariosApi();
        $locatariosData = $locatariosApi->getLocatarioById(intval($this->route_params['id']));
        if ($locatariosData['gotData']) {
            $locatariosData = $locatariosApi->getLocatarioById(intval($this->route_params['id']))['data'][0];
        } else {

            header('Location: index');
        }
        $arg =   array_merge($arg, $locatariosData);
        View::renderTemplate('Locatarios/edit.html', $arg);
    }
    // Listar Locatário
    public function listAction()
    {

        if (isset($_POST['columns'][0]['search']['value'])) {
            $buscaRapida = $_POST['columns'][0]['search']['value'];
        } else {
            $buscaRapida = "";
        }
        if (isset($_POST['columns'][1]['search']['value'])) {
            $nome = $_POST['columns'][1]['search']['value'];
        } else {
            $nome = "";
        }
        if (isset($_POST['columns'][2]['search']['value'])) {
            $cpf = $_POST['columns'][2]['search']['value'];
        } else {
            $cpf = "";
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

        $locatariosApi = new LocatariosApi();

        $response = $locatariosApi->list($buscaRapida,$nome, $cpf, $offset, $limit);

        $response['draw'] =  $draw;
        //View
        //var_dump($list);

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // Adicionar Locatário
    public function addAction()
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Por Favor, preencha todos os campos';
        if (!empty($_POST)) {
            $locatariosApi = new LocatariosApi();
            $response =  $locatariosApi->add($_POST);
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // Atualização Locatário
    public function updateAction()
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Por Favor, preencha todos os campos';

        if (!empty($_POST)) {
            $locatariosApi = new LocatariosApi();
            $locatarios_id = intval($this->route_params['id']);
            $response =  $locatariosApi->edit($_POST, $locatarios_id);
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }
    // Desabilitar Locatário
    public function disableAction()
    {

        $response['status'] = 'error';
        $response['status-message'] = 'Missing Data';

        if (isset($_POST['locatarios_id'])  && !empty($_POST['locatarios_id'])) {
            $proietariosId = intval($_POST['locatarios_id']);
            $locatariosApi = new LocatariosApi();
            $response = $locatariosApi->disable($proietariosId);
        }

        //View
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // Listagem para o SelectTwo
    public function simpleListAction()
    {
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


        if (isset($_GET['q'])) {
            $nomeLocatario = addslashes($_GET['q']);
        } else {
            $nomeLocatario = "";
        }

        $locatariosApi = new LocatariosApi();

        $response = $locatariosApi->simpleList($nomeLocatario,  $offset, $limit);

        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
