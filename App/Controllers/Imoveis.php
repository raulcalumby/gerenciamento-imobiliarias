<?php

namespace App\Controllers;


use \Core\View;
use App\Models\ImoveisApi;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Imoveis extends \Core\Controller
{

    public $id;
    public $singular_name = "Imóveis";
    /**
     * Show the index page
     *
     * @return void
     */

    public function indexAction()
    {
        $arg =  $this->route_params;
        $arg['singular_name'] = $this->singular_name;
        View::renderTemplate('Imoveis/index.html', $arg);
    }

    //View Add Imóveis
    public function novoAction()
    {
        $arg =  $this->route_params;
        $arg['singular_name'] = $this->singular_name;


        
       
        View::renderTemplate('Imoveis/add.html', $arg);
    }

    //View Edit Imóvel
    public function editAction()
    {
        $arg =  $this->route_params;
        $arg['singular_name'] = $this->singular_name;
        $imoveisApi = new ImoveisApi();
        $imoveis = $imoveisApi->getImoveisById(intval($this->route_params['id']));

        

        if ($imoveis['gotData']) {

            
            $imoveis = $imoveis['data'][0];
            $imoveis['locacao'] =   number_format($imoveis['locacao'], 2, ",", ".");
            $imoveis['condominio'] =   number_format($imoveis['condominio'], 2, ",", ".");
            $imoveis['preco_venda'] =   number_format($imoveis['preco_venda'], 2, ",", ".");
            $imoveis['iptu'] =   number_format($imoveis['iptu'], 2, ",", ".");
            $imoveis['seg_incendio_valor'] =   number_format($imoveis['seg_incendio_valor'], 2, ",", ".");
            $imoveis['seg_incendio_valor_total'] =   number_format($imoveis['seg_incendio_valor_total'], 2, ",", ".");
           
        } else {

            header('Location: index');
        }
        if($imoveis['images']['gotData']){

            $imoveis['images'] = $imoveis['images']['data'];
        }else{
            $imoveis['images'] = [];
        }

        if(!empty($imoveis['feature']))
        {
            $imoveis['feature'] = get_object_vars(json_decode($imoveis['feature']));
            $imoveis['featureSelected'] = [];
           
            foreach ($imoveis['feature']as $key => $value) {
               
                
                $imoveis['featureSelected'][] = $key;
                
            }
             unset($imoveis['feature']);
        }

        $arg = array_merge($arg, $imoveis);
        View::renderTemplate('Imoveis/edit.html', $arg);
    }

    // Adicionar Imóvel no bd
    public function addAction()
    {

        $response['status'] = 'error';
        $response['status-message'] = 'Por Favor, preencha todos os campos';
        if (!empty($_POST)) {
            $imoveisApi = new ImoveisApi();
            $response =  $imoveisApi->add($_POST);
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // Atualização Imóveis
    public function updateAction()
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Por Favor, preencha todos os campos';

        if (!empty($_POST)) {
            $imoveisApi = new ImoveisApi();
            $imoveis_id = intval($this->route_params['id']);
            $response =  $imoveisApi->edit($_POST, $imoveis_id);
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    //Upload Photo
    public function uploadPhoto()
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Por Favor, preencha todos os campos';
        if (isset($_POST['imoveis_id']) && isset($_FILES)) {

            $imoveis_id = intval($_POST['imoveis_id']);
            $imoveisApi = new ImoveisApi();
            $response = $imoveisApi->uploadPhoto($imoveis_id, $_FILES['upload']);
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // Listar Imóveis
    public function listAction()
    {

        if (isset($_POST['columns'][0]['search']['value'])) {
            $buscaRapida = $_POST['columns'][0]['search']['value'];
        } else {
            $buscaRapida = "";
        }

        if (isset($_POST['columns'][1]['search']['value'])) {
            $tipo = $_POST['columns'][1]['search']['value'];
        } else {
            $tipo = "";
        }
        if (isset($_POST['columns'][2]['search']['value'])) {
            $proprietario = $_POST['columns'][2]['search']['value'];
        } else {
            $proprietario = "";
        }
        if (isset($_POST['columns'][3]['search']['value'])) {
            $endereco = $_POST['columns'][3]['search']['value'];
        } else {
            $endereco = "";
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

        $imoveisApi = new ImoveisApi();

        $response = $imoveisApi->list($buscaRapida,$tipo,$proprietario, $endereco,$offset, $limit);

        $response['draw'] =  $draw;
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    //listagem de imoveis Pelo Id do proprietário
    public function listImoveisByProprietarioId()
    {
        if (isset($_POST['proprietario_id'])) {
            $proprietarioId = $_POST['proprietario_id'];
        } else {
            $proprietarioId = 0;
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

        $imoveisApi = new ImoveisApi();

        $response = $imoveisApi->listByProprietarioId($proprietarioId, $offset, $limit);

        $response['draw'] =  $draw;
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    // Desabilitar Propietário
    public function disableAction()
    {

        $response['status'] = 'error';
        $response['status-message'] = 'Missing Data';

        if (isset($_POST['imoveis_id'])  && !empty($_POST['imoveis_id'])) {
            $imoveis_id = intval($_POST['imoveis_id']);
            $imoveisApi = new ImoveisApi();
            $response = $imoveisApi->disable($imoveis_id);
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

        $imoveisApi = new ImoveisApi();

        $response = $imoveisApi->simpleList($nome,  $offset, $limit);

        header('Content-Type: application/json');
        echo json_encode($response);
    }

     // Listagem para o SelectTwo com todos imoveis
     public function availablePropertiesAction()
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
 
         $imoveisApi = new ImoveisApi();
 
         $response = $imoveisApi->availableProperties($nome,  $offset, $limit);
 
         header('Content-Type: application/json');
         echo json_encode($response);
     }

    
    // Define o propietário
    public function defineProprietarioAction()
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Por Favor, preencha todos os campos';
        if (isset($_POST['imoveis_id']) && isset($_POST['proprietario_id'])) {
            $imoveisApi = new ImoveisApi();
            $response = $imoveisApi->defineProprietario($_POST);
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }


    public function getPropertiesById()
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Por Favor, preencha todos os campos';
        $imoveisApi = new ImoveisApi();
        if (isset($_POST['properties_id'])) {
            $propertiesId = $_POST['properties_id'];
            $response = $imoveisApi->getImoveisById($propertiesId);
        }

        $featuresTranslate = [];

        if(isset($response['data'][0]['feature'])  &&  !empty($response['data'][0]['feature']) )
        {
            $features =  get_object_vars(json_decode($response['data'][0]['feature']));
            foreach ($features as $key => $value) {
                 $featureData = $imoveisApi->getFeaturesById($key);
                 $featureData['gotData'] == 'true' ?  $featuresTranslate[] = $featureData['data'][0]['title'] : '';               
            }
            $response['data'][0]['feature'] = $featuresTranslate;
        }

      

        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
