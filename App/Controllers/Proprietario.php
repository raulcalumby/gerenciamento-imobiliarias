<?php

namespace App\Controllers;

use App\Models\ProprietarioApi;
use \Core\View;
use App\Models\ProprietariosApi;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Proprietario extends \Core\Controller
{

    public $id;
    public $singular_name = "Proprietario";
    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $arg =  $this->route_params;
        $arg['singular_name'] = $this->singular_name;
        View::renderTemplate('Proprietarios/index.html', $arg);
    }
    //View Add Propietário
    public function novoAction()
    {
        $arg =  $this->route_params;
        $arg['singular_name'] = $this->singular_name;
        View::renderTemplate('Proprietarios/add.html', $arg);
    }
    //View Edit Propietário
    public function editAction()
    {
        $arg =  $this->route_params;
        $arg['singular_name'] = $this->singular_name;
        $propietariosApi = new ProprietarioApi();
        $propietariosData = $propietariosApi->getPropietarioById(intval($this->route_params['id']));
        if ($propietariosData['gotData']) {
            $propietariosData = $propietariosApi->getPropietarioById(intval($this->route_params['id']))['data'][0];
            $propietariosData['taxa'] =  number_format($propietariosData['taxa'], 2, ",", ".");
        } else {

            header('Location: index');
        }
        $arg =   array_merge($arg, $propietariosData);
        View::renderTemplate('Proprietarios/edit.html', $arg);
    }
    // Listar Propietário
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

        $propietariosApi = new ProprietarioApi();

        $response = $propietariosApi->list($buscaRapida,$nome, $cpf, $offset, $limit);

        $response['draw'] =  $draw;
        //View
        //var_dump($list);

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // Adicionar Propietário
    public function addAction()
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Por Favor, preencha todos os campos';
        if (!empty($_POST)) {
            $propietariosApi = new ProprietarioApi();
            $response =  $propietariosApi->add($_POST);
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // Atualização Propietário
    public function updateAction()
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Por Favor, preencha todos os campos';
      
        if (!empty($_POST)) {
            $propietariosApi = new ProprietarioApi();
            $proprietarios = intval($this->route_params['id']);
            $response =  $propietariosApi->edit($_POST, $proprietarios);
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }
    // Desabilitar Propietário
    public function disableAction()
    {

        $response['status'] = 'error';
        $response['status-message'] = 'Missing Data';

        if (isset($_POST['proprietarios_id'])  && !empty($_POST['proprietarios_id'])) {
            $proietariosId = intval($_POST['proprietarios_id']);
            $propietariosApi = new ProprietarioApi();
            $response = $propietariosApi->disable($proietariosId);
        }

        //View
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // Listagem para o SelectTwo
    public function simpleListAction()
    {   
        if(isset($_POST['start'])){
            $offset = $_POST['start'];
        }else{
            $offset = 0;
        }

        if(isset($_POST['length'])){
            $limit = $_POST['length'];
        }else{
            $limit = 15;
        }

       
        if(isset($_GET['q'])){
            $nomePropietario = addslashes($_GET['q']);
        }else{
            $nomePropietario = "";
        }
        
        $propietariosApi = new ProprietarioApi();

        $response = $propietariosApi->simpleList($nomePropietario,  $offset, $limit);

        header('Content-Type: application/json');
        echo json_encode($response);    
    }
}
