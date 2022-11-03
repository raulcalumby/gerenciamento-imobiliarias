<?php

namespace App\Controllers;

use \Core\View;
use App\Models\LocacaoApi;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Locacao extends \Core\Controller
{

    public $id;
    public $singular_name = "Locações";
    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $arg =  $this->route_params;
        $arg['singular_name'] = $this->singular_name;
        View::renderTemplate('Locacao/index.html', $arg);
    }

    //View Add 
    public function novoAction()
    {
        $arg =  $this->route_params;
        $arg['singular_name'] = $this->singular_name;
        View::renderTemplate('Locacao/add.html', $arg);
    }

    //View Edit
    public function editAction()
    {
        $arg =  $this->route_params;
        $arg['singular_name'] = $this->singular_name;
        $locacaoApi = new LocacaoApi();
        $locacao = $locacaoApi->getLocacaoById(intval($this->route_params['id']));

        if ($locacao['gotData']) {
            $locacao = $locacao['data'][0];
            $locacao['locacao_valor'] =   number_format($locacao['locacao_valor'], 2, ",", ".");
           
            $locacao['seg_fianca_valor'] =   number_format($locacao['seg_fianca_valor'], 2, ",", ".");
            $arg = array_merge($arg, $locacao);
        } else {
            header('Location: index');
        }
      

        View::renderTemplate('Locacao/edit.html', $arg);
    }

    // Add dados
    public function addAction()
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Por Favor, preencha todos os campos';

       
        if (!empty($_POST)) {
            $locacaoApi = new LocacaoApi();
            $response =  $locacaoApi->add($_POST);
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    //Add fotos
    public function addPhoto()
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Por Favor, preencha todos os campos';
        if (isset($_POST['locacao_id']) && isset($_FILES)) {

            $locacao_id = intval($_POST['locacao_id']);
            $locacaoApi = new LocacaoApi();
            $response = $locacaoApi->uploadPhoto($locacao_id, $_FILES['upload']);
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // Listar Locações
    public function listAction()
    {

        if (isset($_POST['columns'][0]['search']['value'])) {
            $buscaRapida = $_POST['columns'][0]['search']['value'];
        } else {
            $buscaRapida = "";
        }
        if (isset($_POST['columns'][1]['search']['value'])) {
            $locatorio = $_POST['columns'][1]['search']['value'];
        } else {
            $locatorio = "";
        }
        if (isset($_POST['columns'][2]['search']['value'])) {
            $endereco = $_POST['columns'][2]['search']['value'];
        } else {
            $endereco = "";
        }
        if (isset($_POST['columns'][3]['search']['value'])) {
            $tipo = $_POST['columns'][3]['search']['value'];
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

        $locacaoApi = new locacaoApi();

        $response = $locacaoApi->list($buscaRapida, $locatorio , $endereco, $tipo, $offset, $limit);

        $response['draw'] =  $draw;
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function updateAction()
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Por Favor, preencha todos os campos';
        if (!empty($_POST)) {
            $locacaoApi = new LocacaoApi();
            $locacao_id = intval($this->route_params['id']);
            $response =  $locacaoApi->edit($_POST,$locacao_id);
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function disable ()
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Missing Data';

        if (isset($_POST['locacao_id'])  && !empty($_POST['locacao_id'])) {
            $locacao_id = intval($_POST['locacao_id']);
            $locacaoApi = new LocacaoApi();
            $response = $locacaoApi->disable($locacao_id);
        }

        //View
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    
}
