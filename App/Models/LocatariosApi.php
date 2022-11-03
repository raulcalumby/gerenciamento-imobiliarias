<?php


namespace App\Models;

use App\Models\Cadastro\PessoaFisica;
use App\Models\Integracoes\AsaasApi;


class LocatariosApi extends \App\Models\CrudInit
{
    /**
     * Get all the users as an associative array
     *
     * @return array
     */

    //Listagem Locatário
    public function list(string $buscaRapida = "", string $nome = "", string $cpf = "", int $offset = 0, int $limit = 15): array
    {
        //Config
        $conditions['return_type'] = 'all';
        $conditions['limit'] = $limit;
        $conditions['offset'] = $offset;
        $conditions['order_by'] = 'locatarios_id DESC';
        $buscaRapidaW = "";
        $nomeW = "";
        $cpfW = "";
        if (!empty($nome)) {
            $nomeW = "AND (nome_completo LIKE '%$nome%')";
        }

        if (!empty($cpf)) {
            $cpfW = "AND (cpf LIKE '%$cpf%')";
        }
        if (!empty($buscaRapida)) {
            $buscaRapidaW = "AND (nome_completo LIKE '%$buscaRapida%' || email LIKE '%$buscaRapida%' || email_alternativo LIKE '%$buscaRapida%' ||  cpf LIKE '%$buscaRapida%' ||  tel LIKE '%$buscaRapida%' || tel_2 LIKE '%$buscaRapida%' )";
        }

        //Mount Query
        $conditions['custom_where_query'] = "WHERE enabled = '1' $buscaRapidaW $nomeW $cpfW";
        $conditions['select'] = '*, DATE_FORMAT(created,"%d/%m/%Y ás %H:%i:%s") AS created, DATE_FORMAT(modified,"%d/%m/%Y ás %H:%i:%s") AS modified';
        $response =  $this->getRows('locatarios', $conditions);
        return $response;
    }

    // Cadastro Locatário
    public function add(array $data): array
    {
        $pessoaFisica = new PessoaFisica();
        $response['status'] = 'error';
        $response['status-message'] = 'Erro ao adicionar o Locatário';
        if (empty($data)) {
            $response['status-message'] = 'Preencha todos os campos';
            return $response;
        }

        if (empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $response['status-message'] = 'Email Inválido';
            return $response;
        }

        $data['tel'] = preg_replace('/[^0-9]/', '', (string) $data['tel']);
        $data['tel_2'] = preg_replace('/[^0-9]/', '', (string) $data['tel_2']);

        if (!empty($data['email_alternativo']) && !filter_var($data['email_alternativo'], FILTER_VALIDATE_EMAIL)) {
            $response['status-message'] = 'Email Alternativo Inválido';
            return $response;
        }
        if(!$pessoaFisica->isCPFValidFilter($data['cpf']))
        {
            $response['status-message'] = 'O CPF cadastrado está invalido.';
            return $response;
        }
        $data['cpf'] = $this->limpaCPF_CNPJ($data['cpf']);

        $insert = $this->insert('locatarios', $data);
        if ($insert) {
            $response['status'] = 'success';
            $response['status-message'] = 'Locatário cadastrado com sucesso!';
        }
        return $response;
    }

    //Editar Locatário
    public function edit(array $data, int $id): array
    {

        $response['status'] = 'error';
        $response['status-message'] = 'Erro ao adicionar o Locatário';

        $pessoaFisica = new PessoaFisica();
        if (empty($data)) {
            $response['status-message'] = 'Preencha todos os campos';
            return $response;
        }
        $data['tel'] = preg_replace('/[^0-9]/', '', (string) $data['tel']);
        $data['tel_2'] = preg_replace('/[^0-9]/', '', (string) $data['tel_2']);
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $response['status-message'] = 'Email Inválido';
            return $response;
        }
        if (!empty($data['email_alternativo']) && !filter_var($data['email_alternativo'], FILTER_VALIDATE_EMAIL)) {
            $response['status-message'] = 'Email Alternativo Inválido';
            return $response;
        }
        if(!$pessoaFisica->isCPFValidFilter($data['cpf']))
        {
            $response['status-message'] = 'O CPF cadastrado está invalido.';
            return $response;
        }

       
       
     


        $data['cpf'] = $this->limpaCPF_CNPJ($data['cpf']);


        $where['locatarios_id'] = $id;

        $update = $this->update('locatarios', $data, $where);
        if ($update) {
            $response['status'] = 'success';
            $response['status-message'] = 'Locatário atualizado com sucesso!';
        }
        
        return $response;
    }

    // Desabilita o propietário 
    public function disable(int $locatarios_id): array
    {
        $response['status'] = 'error';

        if (!$this->isLocatarioIdValid($locatarios_id)) {
            $response['status-message'] = 'O Locatário não existe, ou já foi deletado';
            return $response;
        }

        $where['locatarios_id'] = $locatarios_id;
        $data['enabled'] = '0';
        $update =  $this->update('locatarios', $data, $where);
        if ($update) {
            $response['status'] = 'success';
            $response['status-message'] = 'O Locatário foi deletado com sucesso!';
            return $response;
        }

        return $response;
    }

    /* Verifica se o Locatário existe pelo id */
    public function isLocatarioIdValid(int $locatarios_id): bool
    {
        $conditions['where']['locatarios_id'] = $locatarios_id;
        $conditions['where']['enabled'] = 1;
        $response =  $this->getRows('locatarios', $conditions);
        return $response['gotData'];
    }

    //Seleciona Locatário pelo ID e devolve seus dados
    public function getLocatarioById(int $locatarios_id): array
    {
        $conditions['where']['locatarios_id'] = $locatarios_id;
        $conditions['where']['enabled'] = 1;
        $response =  $this->getRows('locatarios', $conditions);
        return $response;
    }

    // Seleciona  Locatário para o SelectTWO
    public function simpleList(string $query,  int $offset = 0, int $limit = 999): array
    {
        //Config
        $conditions['return_type'] = 'all';
        $conditions['limit'] = 999;
        $conditions['offset'] = $offset;

        $conditions['order_by'] = 'locatarios_id DESC';
        $query_W = '';
        if (!empty($query)) {
            $query_W = "AND (nome_completo LIKE '%$query%')";
        }

        $conditions['select'] = '*, locatarios_id AS id, nome_completo AS text , DATE_FORMAT(created,"%d/%m/%Y ás %H:%i:%s") AS created, DATE_FORMAT(modified,"%d/%m/%Y ás %H:%i:%s") AS modified';
        $conditions['custom_where_query'] = "WHERE enabled = 1 $query_W   ";
        $response =  $this->getRows('locatarios', $conditions);
        return $response;
    }

    // Seleciona  Locatário para o SelectTWO
    public function criaLocatarioAsaas(int $locatarioId): bool
    {

        $status = false;
        $locatarioData = $this->getLocatarioById($locatarioId);
        if (!$locatarioData['gotData']) {
            return $status;
        }

        $locatarioData = $locatarioData['data'][0];
        $name = $locatarioData['nome_completo'];
        $doc = $locatarioData['cpf'];
        $email = $locatarioData['email'];
        $phone = $locatarioData['tel'];
       

        //Cria o cliente no Asaas e salva no costumerId no banco de dados
        $customerData = [
            "name" => $name,
            "doc" =>  $doc,
            "phone" => $phone,
            "email" => $email,
            "externalReference" => $locatarioId,
            "groupName" => "locatarios"
        ];
        $asaasApi =  new AsaasApi();
        $customerAsaasData = $asaasApi->createCustomer($customerData);
       
        if (isset($customerAsaasData['id']))
        {
            $customerIdByAsaas = $customerAsaasData['id'];
            $where['locatarios_id'] = $locatarioId;
         
            $costumerAsaasData['customer_id'] = $customerIdByAsaas;
            $update = $this->update("locatarios", $costumerAsaasData, $where);

            if ($update) {
                $status = true;
            }
        }

        return $status;
    }


    function limpaCPF_CNPJ($valor)
    {
        $valor = trim($valor);
        $valor = str_replace(".", "", $valor);
        $valor = str_replace(",", "", $valor);
        $valor = str_replace("-", "", $valor);
        $valor = str_replace("/", "", $valor);
        return $valor;
    }
}
