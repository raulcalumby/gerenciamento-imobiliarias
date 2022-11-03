<?php


namespace App\Models;


include 'filters.php';
class IndicesApi extends \App\Models\CrudInit
{
    /**
     * Get all the users as an associative array
     *
     * @return array
     */

    //Listagem Índices
    public function list(string $filter_data = "",  string $tipo = "", int $offset = 0, int $limit = 15): array
    {
        //Config
        $conditions['return_type'] = 'all';
        $conditions['limit'] = $limit;
        $conditions['offset'] = $offset;
        $conditions['order_by'] = 'indices_id DESC';
        $filter_dataW = "";

        if (!empty($filter_data)) {
            $filter_dataW = "AND (data LIKE '%$filter_data-10%')";
        }

        //Mount Query
        $conditions['custom_where_query'] = " WHERE enabled = '1' $filter_dataW AND tipo = '$tipo'";
        $conditions['select'] = '*, DATE_FORMAT(created,"%d/%m/%Y ás %H:%i:%s") AS created,DATE_FORMAT(data,"%m/%Y") AS data,  DATE_FORMAT(modified,"%d/%m/%Y ás %H:%i:%s") AS modified';
        $response =  $this->getRows('indices', $conditions);
        return $response;
    }

    // listagem  indice para o selectTwo 
    public function simpleList(string $query,  int $offset = 0, int $limit = 999): array
    {
        //Config
        $conditions['return_type'] = 'all';
        $conditions['limit'] = 999;
        $conditions['offset'] = $offset;

        $query_W = '';
        if (!empty($query)) {
            $query_W = "AND (tipo LIKE '%$query%')";
        }

        $conditions['select'] = '*,  indices_id AS id, tipo AS text';
        $conditions['custom_where_query'] = "WHERE enabled = 1 $query_W";
        $response =  $this->getRows('indices', $conditions);
        return $response;
    }

    // Cadastro de Índices
    public function add(array $data): array
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Erro ao adicionar o Índice';

        if (empty($data)) {
            $response['status-message'] = 'Preencha todos os campos';
            return $response;
        }

        /* Verifico se existe um indice cadastrado naquele mês, se True retorno um erro*/
        if ($this->verificaIndicesData($data['data'])) {
            $response['status'] = 'warning';
            $response['status-message'] = 'Já existe um INDICE na data escolhida.';
            return $response;
        }

        $data['aliquota'] = toEnglishDecimal($data['aliquota']);

        $insert = $this->insert('indices', $data);

        if ($insert) {
            $response['status'] = 'success';
            $response['status-message'] = 'Cadastro feito com com sucesso!';
        }

        return $response;
    }

    // Edição de Índices
    public function edit(array $data, int $id): array
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Erro ao editar o Índice';

        if (empty($data)) {
            $response['status-message'] = 'Preencha todos os campos';
            return $response;
        }

        /* Verifico se existe um indice cadastrado naquele mês, se True retorno um erro*/
        if ($this->verificaIndicesData($data['data'])) {
            $response['status'] = 'warning';
            $response['status-message'] = 'Já existe um INDICE na data escolhida.';
            return $response;
        }
        
        $data['aliquota'] = toEnglishDecimal($data['aliquota']);
        $conditions['indices_id'] = $id;
        $update = $this->update('indices', $data, $conditions);

        if ($update) {
            $response['status'] = 'success';
            $response['status-message'] = 'Edição feita com com sucesso!';
        }

        return $response;
    }

    // Pega dados do Indice pelo id
    public function getIndicesById(int $indice_id): array
    {
        $conditions['custom_where_query'] = "WHERE enabled = '1' AND indices_id =  $indice_id";
        $conditions['select'] = '*, DATE_FORMAT(data,"%Y-%m") AS data';
        $response =  $this->getRows('indices', $conditions);

        return $response;
    }

    public function verificaIndicesData(string $data): bool
    {
        $dataYear = date('Y', strtotime($data));
        $dataMonth = date('m', strtotime($data));

        $conditions['custom_where_query'] = "WHERE enabled = '1' AND MONTH(data) = $dataMonth AND YEAR(data) = $dataYear";
        $conditions['select'] = '*, DATE_FORMAT(data,"%Y-%m") AS data';
        $response =  $this->getRows('indices', $conditions);

        return $response['gotData'];
    }

    // Desabilita o Índice
    public function disable(int $indice_id): array
    {
        $response['status'] = 'error';

        if (!$this->isIndiceIdValid($indice_id)) {
            $response['status-message'] = 'O Índice não existe, ou já foi deletado';
            return $response;
        }

        $where['indices_id'] = $indice_id;
        $data['enabled'] = '0';
        $update =  $this->update('indices', $data, $where);
        if ($update) {
            $response['status'] = 'success';
            $response['status-message'] = 'O Índice foi deletado com sucesso!';
            return $response;
        }

        return $response;
    }

    /* Verifica se o Índice existe pelo id */
    public function isIndiceIdValid(int $indice_id): bool
    {
        $conditions['where']['indices_id'] = $indice_id;
        $conditions['where']['enabled'] = 1;
        $response =  $this->getRows('indices', $conditions);
        return $response['gotData'];
    }
}
