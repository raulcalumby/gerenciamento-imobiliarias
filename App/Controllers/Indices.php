<?php

namespace App\Controllers;

use \Core\View;
use App\Models\IndicesApi;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Indices extends \Core\Controller
{

    public $id;
    public $singular_name = "Índices";
    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $arg =  $this->route_params;
        $arg['singular_name'] = $this->singular_name;
        View::renderTemplate('Indices/index.html', $arg);
    }

    //View Add 
    public function novoAction()
    {
        $arg =  $this->route_params;
        $arg['singular_name'] = $this->singular_name;
        View::renderTemplate('Indices/add.html', $arg);
    }
    // View Edit 
    public function  editAction()
    {
        $arg =  $this->route_params;
        $arg['singular_name'] = $this->singular_name;
        $indicesApi = new IndicesApi();
        $indices = $indicesApi->getIndicesById(intval($this->route_params['id']));

        if ($indices['gotData']) {
            $indices = $indices['data'][0];
            $indices['aliquota'] =   number_format($indices['aliquota'], 2, ",", ".");
            $arg = array_merge($arg, $indices);
        } else {
            header('Location: index');
        }
        View::renderTemplate('Indices/edit.html', $arg);
    }

    //Add Action
    public function addAction()
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Preencha todos os campos!';
        if (!empty($_POST)) {
          
            $indicesApi = new IndicesApi();
            $data = $_POST;
            if (isset($data['data'])) {
                $data['data'] = $data['data'] . '-10';
            }
            $response = $indicesApi->add($data);
        }

        //View
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    // Atualiza dados
    public function updateAction()
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Preencha todos os campos!';
        if (!empty($_POST)) {
            $indicesApi = new IndicesApi();
            $data = $_POST;
            $indices_id = intval($this->route_params['id']);
            if (isset($data['data'])) {
                $data['data'] = $data['data'] . '-10';
            }
            $response = $indicesApi->edit($data, $indices_id);
        }

        //View
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // Listar Índices
    public function listAction()
    {

        if (isset($_POST['columns'][0]['search']['value'])) {
            $filter_data = $_POST['columns'][0]['search']['value'];
        } else {
            $filter_data = "";
        }
        if (isset($_POST['tipo'])) {
            $tipo = $_POST['tipo'];
        } else {
            $tipo = "";
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

        $indicesApi = new IndicesApi();

        $response = $indicesApi->list($filter_data, $tipo, $offset, $limit);

        $response['draw'] =  $draw;
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // Desativa indice
    public function disableAction()
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Missing Data';

        if (isset($_POST['indices_id'])  && !empty($_POST['indices_id'])) {
            $indices_id = intval($_POST['indices_id']);
            $indicesApi = new IndicesApi();
            $response = $indicesApi->disable($indices_id);
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
            $nome = addslashes($_GET['q']);
        } else {
            $nome = "";
        }

        $indicesApi = new IndicesApi();

        $response = $indicesApi->simpleList($nome,  $offset, $limit);

        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
