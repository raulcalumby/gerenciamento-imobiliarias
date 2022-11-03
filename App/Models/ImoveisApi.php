<?php


namespace App\Models;

use App\Models\Integracoes\GoogleMapsApi;
use App\Models\RecibosApi;

class ImoveisApi extends \App\Models\CrudInit
{
    /**
     * Get all the users as an associative array
     *
     * @return array
     */


    //Listagem Imóvel
    public function list(string $buscaRapida = "", string $tipo = "", string $proprietario = "", string $endereco = "", int $offset = 0, int $limit = 15): array
    {
        //Config
        $conditions['return_type'] = 'all';
        $conditions['limit'] = $limit;
        $conditions['offset'] = $offset;
        $conditions['order_by'] = "imoveis.status_zap DESC";
        $buscaRapidaW = "";
        $tipoW = "";
        $proprietarioW = '';
        $enderecoW = '';
        if (!empty($endereco)) {
            $enderecoW = " AND imoveis.endereco LIKE '%$endereco%'";
        }
        if (!empty($proprietario)) {
            $proprietarioW = " AND proprietarios.nome_completo LIKE '%$proprietario%'";
        }
        if (!empty($tipo)) {
            $tipoW = "AND imoveis.tipo = '$tipo'";
        }
        if (!empty($buscaRapida)) {
            $buscaRapidaW = "AND (proprietarios.nome_completo LIKE '%$buscaRapida%' || imoveis.responsavel LIKE '%$buscaRapida%' || imoveis.codigo LIKE '%$buscaRapida%' || imoveis.estado LIKE '%$buscaRapida%' || imoveis.cidade LIKE '%$buscaRapida%' || imoveis.endereco LIKE '%$buscaRapida%' )";
        }

        //Mount Query
        $conditions['custom_where_query'] = " LEFT JOIN (proprietarios) ON imoveis.proprietarios_id = proprietarios.proprietarios_id  WHERE imoveis.enabled = '1' $buscaRapidaW $tipoW $proprietarioW $enderecoW";
        $conditions['select'] = '*,  CONCAT(imoveis.endereco, " ", imoveis.numero) as endereco_imovel,  DATE_FORMAT(imoveis.created,"%d/%m/%Y ás %H:%i:%s") AS created, DATE_FORMAT(imoveis.modified,"%d/%m/%Y ás %H:%i:%s") AS modified';
        $response =  $this->getRows('imoveis', $conditions);
        return $response;
    }

    public function listByProprietarioId(string $proprietario_id = "",  int $offset = 0, int $limit = 15): array
    {
        //Config
        $conditions['return_type'] = 'all';
        $conditions['limit'] = $limit;
        $conditions['offset'] = $offset;
        $conditions['order_by'] = 'imoveis.imoveis_id DESC';


        //Mount Query
        $conditions['custom_where_query'] = " INNER JOIN (proprietarios) ON imoveis.proprietarios_id = proprietarios.proprietarios_id  WHERE imoveis.enabled = '1' AND imoveis.proprietarios_id = $proprietario_id";
        $conditions['select'] = '*, CONCAT(imoveis.endereco, " ", imoveis.numero) as endereco , DATE_FORMAT(imoveis.created,"%d/%m/%Y ás %H:%i:%s") AS created, DATE_FORMAT(imoveis.modified,"%d/%m/%Y ás %H:%i:%s") AS modified';
        $response =  $this->getRows('imoveis', $conditions);
        return $response;
    }

    // Cadastro de Imóveis
    public function add(array $data): array
    {

        $response['status'] = 'error';
        $response['status-message'] = 'Erro ao adicionar o Imóvel';

        if (empty($data)) {
            $response['status-message'] = 'Preencha todos os campos';
            return $response;
        }



        if (!isset($data['taxa_iptu'])) {
            $data['taxa_iptu'] = 0;
        }

        if (!isset($data['condominio_responsabilidade'])) {
            $data['condominio_responsabilidade'] = 0;
        }

        if ($data['opcoes_iptu'] !== '1' && empty($data['qtd_parcelas_iptu']) || $data['opcoes_iptu'] !== '1'  && empty($data['iptu'])) {
            $response['status'] = 'warning';
            $response['status-message'] = 'Preencha os campos do IPTU corretamente';
            return $response;
        }

        if (isset($data['feature'])) {
            $data['feature'] =   json_encode($data['feature']);
        }



        /* Pegando LAT e LNG GOOGLE API*/
        // $endereco = $data['cep'] . " " . $data['estado'] . " " . $data['cidade'] . " " . $data['numero'] . " " . $data['bairro'];
        // $googleMapsApi = $this->googleMaps($endereco);
        // $data['lat'] =  $googleMapsApi['results'][0]['geometry']["location"]['lat'];
        // $data['lng'] =  $googleMapsApi['results'][0]['geometry']["location"]['lng'];


        $data['locacao'] = $this->toEnglishDecimal($data['locacao']);
        $data['condominio'] = $this->toEnglishDecimal($data['condominio']);
        $data['iptu'] = $this->toEnglishDecimal($data['iptu']);
        $data['preco_venda'] = $this->toEnglishDecimal($data['preco_venda']);
        $data['seg_incendio_valor'] = $this->toEnglishDecimal($data['seg_incendio_valor']);
        $data['seg_incendio_valor_total'] = $this->toEnglishDecimal($data['seg_incendio_valor_total']);

        $insert = $this->insert('imoveis', $data);
        if ($insert) {
            $response['status'] = 'success';
            $response['imoveis_id'] = $insert;
            $response['status-message'] = 'Imóvel cadastrado com sucesso!';
        }

        return $response;
    }

    //Editar IMOVEIS
    public function edit(array $data, int $id): array
    {

        $response['status'] = 'error';
        $response['status-message'] = 'Erro ao editar o Imovel';
        if (empty($data)) {
            $response['status-message'] = 'Preencha todos os campos';
            return $response;
        }

        if (!isset($data['taxa_iptu'])) {
            $data['taxa_iptu'] = 0;
        }
        if (!isset($data['condominio_responsabilidade'])) {
            $data['condominio_responsabilidade'] = 0;
        }

        if ($data['opcoes_iptu'] !== '1' && empty($data['qtd_parcelas_iptu']) || $data['opcoes_iptu'] !== '1'  && empty($data['iptu'])) {
            $response['status'] = 'warning';
            $response['status-message'] = 'Preencha os campos do IPTU corretamente';
            return $response;
        }

        // GERA REICBO PARA O CONDOMINIO
        $this->atualizaCondominio($data, $id);

        if (!isset($data['image_atualizada'])) {
            $conditions['imoveis_id'] = $id;
            $delete = $this->delete('imoveis_images', $conditions);
        } else {
            $result = array_diff($data['image_atual'], $data['image_atualizada']);
            if (!empty($result)) {
                $result  = array_values($result);
                for ($i = 0; $i < count($result); $i++) {
                    $conditions['imoveis_image_id'] = $result[$i];
                    $delete = $this->delete('imoveis_images', $conditions);
                }
            }
        }

        if (isset($data['feature'])) {
            $data['feature'] =   json_encode($data['feature']);
        }

        /* Pegando LAT e LNG GOOGLE API*/
        // $endereco = $data['cep'] . " " . $data['estado'] . " " . $data['cidade'] . " " . $data['numero'] . " " . $data['bairro'];
        // $googleMapsApi = $this->googleMaps($endereco);
        // $data['lat'] =  $googleMapsApi['results'][0]['geometry']["location"]['lat'];
        // $data['lng'] =  $googleMapsApi['results'][0]['geometry']["location"]['lng'];


        $data['iptu'] = $this->toEnglishDecimal($data['iptu']);
        unset($data['image_atualizada']);
        unset($data['image_atual']);
        $data['locacao'] = $this->toEnglishDecimal($data['locacao']);
        $data['condominio'] = $this->toEnglishDecimal($data['condominio']);
        $data['preco_venda'] = $this->toEnglishDecimal($data['preco_venda']);
        $data['seg_incendio_valor'] = $this->toEnglishDecimal($data['seg_incendio_valor']);
        $data['seg_incendio_valor_total'] = $this->toEnglishDecimal($data['seg_incendio_valor_total']);
        $where['imoveis_id'] = $id;

        $update = $this->update('imoveis', $data, $where);

        if ($update) {
            $response['status'] = 'success';
            $response['status-message'] = 'Imóvel  atualizado com sucesso!';
            $response['imoveis_id'] = $id;
        }

        return $response;
    }


    public function googleMaps($adress)
    {
        $googleMapsAPi = new GoogleMapsApi();

        $adressGoogle = $googleMapsAPi->getGeocode($adress);
        return $adressGoogle;
    }



    // Upload photo Imóvel 
    public function uploadPhoto(int $imoveis_id, array $file): array
    {
        if (!is_dir("uploads/imoveis")) {
            if (mkdir("uploads/imoveis")) {
            }
        }

        if (!is_dir("uploads/imoveis/$imoveis_id")) {
            if (mkdir("uploads/imoveis/$imoveis_id")) {
            }
        }
        $sizeof = sizeof($file['name']);
        for ($i = 0; $i < $sizeof; $i++) {
            $target_path = "uploads/imoveis/$imoveis_id/" . md5(uniqid(rand(), true)) . '_' . $file['name'][$i];
            $data['image_path'] = $target_path;

            if (move_uploaded_file($file['tmp_name'][$i], $target_path)) {
                $data['imoveis_id'] = $imoveis_id;

                $update = $this->insert('imoveis_images', $data);

                $response['status'] = 'success';
                $response['status-message'] = 'Os dados foram inseridos com sucesso';
            } else {
                $response['status'] = 'error';
                $response['status-message'] = 'Occoreu um erro, entre em contato com o administrador.';
            }
        }

        return $response;
    }

    //Seleciona Imóvel pelo ID e devolve seus dados
    public function getImoveisById(int $imoveis_id): array
    {
        $response['status'] = 'error';
        $response['status-message'] = 'O imóvel nãom foi encontrado';

        $conditions['custom_where_query'] = "LEFT JOIN (proprietarios) ON imoveis.proprietarios_id = proprietarios.proprietarios_id WHERE imoveis.enabled = '1' AND imoveis.imoveis_id =  $imoveis_id";
        $conditions['select'] = '*,imoveis.endereco  as endereco_imovel, imoveis.cidade as cidade_imovel,imoveis.cep as cep_imovel,imoveis.estado as estado_imovel,imoveis.bairro as bairro_imovel, DATE_FORMAT(imoveis.created,"%d/%m/%Y ás %H:%i:%s") AS created, DATE_FORMAT(imoveis.modified,"%d/%m/%Y ás %H:%i:%s") AS modified';
        $response =  $this->getRows('imoveis', $conditions);

        if ($response['gotData']) {
            $imoveisId = $response['data'][0]['imoveis_id'];
            $response['data'][0]['images'] =  $this->getPhotoByImovelId(intval($imoveisId));
            $grupoZapApi = new GrupoZapApi();
            $response['data'][0]['validateGrupoZap'] = $grupoZapApi->checkProperties($imoveisId);

            $response['status'] = 'success';
            $response['status-message'] = 'Imóvel encontrado';
        }

        return $response;
    }

    public function getAllImoveis(): array
    {
        $conditions['custom_where_query'] = "LEFT JOIN (proprietarios) ON imoveis.proprietarios_id = proprietarios.proprietarios_id WHERE imoveis.enabled = '1'";
        $conditions['select'] = '*, CONCAT(imoveis.endereco, " ", imoveis.numero)  as endereco_imovel, imoveis.cidade as cidade_imovel,imoveis.cep as cep_imovel,imoveis.estado as estado_imovel,imoveis.bairro as bairro_imovel, DATE_FORMAT(imoveis.created,"%d/%m/%Y ás %H:%i:%s") AS created, DATE_FORMAT(imoveis.modified,"%d/%m/%Y ás %H:%i:%s") AS modified';
        $response =  $this->getRows('imoveis', $conditions);
        if ($response['gotData']) {
            $imoveisId = $response['data'][0]['imoveis_id'];
            $response['data'][0]['images'] =  $this->getPhotoByImovelId(intval($imoveisId));
        }

        return $response;
    }

    public function getPhotoByImovelId(int $imoveisId): array
    {
        $conditions['custom_where_query'] = "WHERE imoveis_id =  $imoveisId";
        $conditions['select'] = '*';
        $response =  $this->getRows('imoveis_images', $conditions);
        return $response;
    }

    // Desabilita o imóvel
    public function disable(int $imoveis): array
    {
        $response['status'] = 'error';

        if (!$this->isImovelIdValid($imoveis)) {
            $response['status-message'] = 'O Imóvel não existe, ou já foi deletado';
            return $response;
        }

        $where['imoveis_id'] = $imoveis;
        $data['enabled'] = '0';
        $update =  $this->update('imoveis', $data, $where);
        if ($update) {
            $response['status'] = 'success';
            $response['status-message'] = 'O Imóvel foi deletado com sucesso!';
            return $response;
        }

        return $response;
    }

    /* Verifica se o Imóvel existe pelo id */
    public function isImovelIdValid(int $imoveis): bool
    {
        $conditions['where']['imoveis_id'] = $imoveis;
        $conditions['where']['enabled'] = 1;
        $response =  $this->getRows('imoveis', $conditions);
        return $response['gotData'];
    }

    /* Simple List normal */
    public function simpleList(string $query,  int $offset = 0, int $limit = 999): array
    {
        //Config
        $conditions['return_type'] = 'all';
        $conditions['limit'] = 999;
        $conditions['offset'] = $offset;

        $query_W = '';

        if (!empty($query)) {
            $query_W = "AND (responsavel LIKE '%$query%' || codigo LIKE '%$query%'  )";
        }


        $conditions['select'] = '*, imoveis_id AS id, CONCAT(codigo, "  -  ", endereco , " ", numero) AS text';
        $conditions['custom_where_query'] = "WHERE enabled = 1 $query_W ";
        $response =  $this->getRows('imoveis', $conditions);

        return $response;
    }


    /* Seleciona Imóveis Sem proprietário */

    public function listNoOwners(string $query,  int $offset = 0, int $limit = 999): array
    {
        //Config
        $conditions['return_type'] = 'all';
        $conditions['limit'] = 999;
        $conditions['offset'] = $offset;

        $query_W = '';
        if (!empty($query)) {
            $query_W = "AND (responsavel LIKE '%$query%' || codigo LIKE '%$query%'  )";
        }
        $conditions['select'] = '*, imoveis_id AS id, CONCAT(codigo, "  -  ", endereco , " ", numero) AS text';
        $conditions['custom_where_query'] = "WHERE enabled = 1 $query_W AND proprietarios_id is  NULL  ";
        $response =  $this->getRows('imoveis', $conditions);

        return $response;
    }

    /*  Imoveis disponiveis para locação    */
    public function availableProperties(string $query,  int $offset = 0, int $limit = 999): array
    {
        //Config
        $conditions['return_type'] = 'all';
        $conditions['limit'] = 999;
        $conditions['offset'] = $offset;

        $query_W = '';
        if (!empty($query)) {
            $query_W = "AND (responsavel LIKE '%$query%' || codigo LIKE '%$query%'  )";
        }

        $conditions['select'] = '*, imoveis_id AS id,  CONCAT(codigo, "  -  ", endereco , " ", numero) AS text';
        $conditions['custom_where_query'] = "WHERE enabled = 1 $query_W ";
        $response =  $this->getRows('imoveis', $conditions);


        if ($response['gotData']) {

            $locacaoApi = new LocacaoApi();
            for ($i = 0; $i < count($response['data']); $i++) {

                $locacaoData =  $locacaoApi->getLocacaoByImovelId($response['data'][$i]['id']);

                if ($locacaoData['gotData']) {

                    $response['data'][$i] = '';
                }
            }
        }

        $response['data'] = array_values($response['data']);
        return $response;
    }

    // Define /Atualiza o Proproetário 
    public function defineProprietario(array $data): array
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Erro ao adicionar o Imóvel';
        if (empty($data)) {
            return $response;
        }
        extract($data);
        $dataUpdate['proprietarios_id'] = $proprietario_id;
        $conditions['imoveis_id'] = $imoveis_id;

        $update = $this->update('imoveis', $dataUpdate, $conditions);
        if ($update) {
            $response['status'] = 'success';
            $response['status-message'] = 'Atualizado com sucesso';
        }

        return $response;
    }

    /* ATUALIZA O CONDOMINIO */
    public function atualizaCondominio(array $data, int $id): bool
    {
        $status = false;
        $locacaoApi = new LocacaoApi();
        if (!isset($data['condominio']) || empty($data['condominio'])) {
            return $status;
        }

        $imoveisData = $this->getImoveisById($id);
        if (!$imoveisData['gotData']) return $status;



        if ($imoveisData['data'][0]['condominio'] !== $this->toEnglishDecimal($data['condominio']) || intval($imoveisData['data'][0]['condominio_responsabilidade']) !== $data['condominio_responsabilidade']) {
            $valorCobranca = $imoveisData['data'][0]['condominio'];
            $hoje = date('Y-m-d');
            $dayCron = date('Y-m-10');

            if ($hoje > $dayCron) {

                if ($valorCobranca >= $data['condominio']) {

                    $locacaoData = $locacaoApi->getLocacaoByImovelId($id);
                    $valor = $this->toEnglishDecimal($data['condominio']) - $valorCobranca;

                    if ($locacaoData['gotData']) {

                        $data = date('Y-m-10', strtotime("+1 month"));
                        $descricao = 'Diferença do condominio';
                        $reciboApi =  new RecibosApi();
                        $geraRecibo = $reciboApi->geraRecibo($data, abs($valor), $descricao, $id);
                        $status = true;
                    }

                    $dataLivroCaixa['descricao'] = 'Diferença do condominio';
                    $dataLivroCaixa['status'] = '1';
                    $dataLivroCaixa['valor'] = abs($valor);
                    $dataLivroCaixa['data'] =  date('Y-m-10', strtotime("+1 month"));
                    $dataLivroCaixa['imovel_id'] = $id;
                    $insertLivroCaixa = $this->insert('livro_caixa', $dataLivroCaixa);
                }
            }
        }



        return $status;
    }

    public function toEnglishDecimal($val)
    {
        /* Converte $val para string e troca a virgula por ponto */
        $res = str_replace(".", "", $val);
        return str_replace(",", ".", $res);
    }


    public function getImoveisByProprietarioId($proprietarioId)
    {
        $conditions['custom_where_query'] = "WHERE enabled = 1 AND proprietarios_id =  $proprietarioId ";

        $conditions['select'] = '*';

        $response =  $this->getRows('imoveis', $conditions);

        return $response;
    }

    public function getImoveisByProprietarioIdAndResponsavel($proprietarioId, $responsavel)
    {
        $responsavelW  = "";
        if (!empty($responsavel) && $responsavel !== 'todos') {
            $responsavelW = "AND responsavel = '$responsavel'";
        }

        $conditions['custom_where_query'] = "WHERE enabled = 1 AND proprietarios_id =  $proprietarioId $responsavelW ";

        $conditions['select'] = '*';

        $response =  $this->getRows('imoveis', $conditions);

        return $response;
    }



    public function getImoveisByLocatarioId($locatarioId)
    {
        $conditions['custom_where_query'] = "INNER JOIN (locacao) ON imoveis.imoveis_id = locacao.imoveis_id WHERE locacao.enabled = 1 AND locacao.locatarios_id =  $locatarioId ";
        $conditions['select'] = '*';
        $response =  $this->getRows('imoveis', $conditions);
        return $response;
    }


    public function listImoveisBySite(string $adress = "", string $maxPrice = "", string $minPrice = "", string $typeSales = "", string $propertyType = "", string $lat, string $lng, string $zoom)
    {
        $adressW = "";
        $price  = "";
        $typeSalesW = "";
        $propertyTypeW = "";
        $radioSelect = '';
        $having = '';


        $queryPrice = $this->checkQueryByTypeSale($typeSales, $minPrice, $maxPrice);
        
        if (!empty($lat) & !empty($lng)) {

            $radioSelect = ",(6371 * acos(cos( radians($lat) )* cos( radians( lat ) )* cos( radians( lng ) - radians($lng) )+ sin( radians($lat) )* sin(radians(lat)))) AS distancia";
            if ($zoom <= 10) {
                $having = "";
                $radioSelect = "";
            } else if ($zoom <= 12) {
                $having = "HAVING distancia < 13.90";
            } else if ($zoom <= 14) {
                $having = "HAVING distancia < 10.15";
            } else {
                $having = "HAVING distancia < 0.95";
            }
        }



        if (!empty($typeSales) && $typeSales !== 'ambos') {

            $typeSalesW = "AND (tipo_venda LIKE '%$typeSales%' || tipo_venda LIKE 'ambos' )";
        }

        if (!empty($propertyType)) {
            $propertyTypeW = "AND tipo = '$propertyType'";
        }

        $conditions['oder_by'] = 'distancia DESC';
        $conditions['custom_where_query'] = "WHERE imoveis.enabled = '1' $queryPrice $adressW    $typeSalesW $propertyTypeW $having";
        $conditions['select'] = "* $radioSelect";

        $response =  $this->getRowsForSite("imoveis", $conditions);


        if ($response['gotData']) {

            foreach ($response['data'] as $key => $imoveis) {
                $photos = $this->getPhotoByImovelId($imoveis['imoveis_id']);
                if ($photos['gotData']) {
                    $response['data'][$key]['photos'] =  $photos['data'];
                } else {
                    $response['data'][$key]['photos'] =  "";
                }
            }
        }

        return $response;
    }


    public function getFeaturesById($featId)
    {
        $conditions['custom_where_query'] = "WHERE zap_features_id =  $featId   AND enabled = 1";
        $conditions['select'] = '*';
        $response =  $this->getRows('zap_features', $conditions);
        return $response;
    }


    public function checkQueryByTypeSale($typeSale, $min, $max)
    {
        $queryReturn = '';
        if (!empty($min)) {
         
            $min = $this->toEnglishDecimal($min);
        }
        
        if (!empty($max)) {
            $max = $this->toEnglishDecimal($max);
           
        }

        switch ($typeSale) {
            case 'venda':
                if (!empty($min) && !empty($max)) {
                    $queryReturn = "AND preco_venda BETWEEN  $min AND $max";
                } else if (!empty($max) && empty($min)) {
                    $queryReturn = "AND preco_venda <= $max";
                } else if (empty($max) && !empty($min)) {
                    $queryReturn = " AND preco_venda >= $min";
                }

                break;
            case 'alugar':
                if (!empty($min) && !empty($max)) {
                    $queryReturn = "AND locacao BETWEEN  $min AND $max ";
                } else if (!empty($max) && empty($min)) {
                    $queryReturn =    "AND locacao <= $max";
                } else if (empty($max) && !empty($min)) {
                    $queryReturn = "AND locacao >= $min";
                }
                break;
            case 'ambos':
                if (!empty($min) && !empty($max)) {
                    $queryReturn = "AND (preco_venda BETWEEN  $min AND $max &&  locacao BETWEEN  $min AND $max) ";
                } else if (!empty($max) && empty($min)) {
                   
                    $queryReturn = "AND (preco_venda <= $max &&  locacao <= $max)";
                } else if (!empty($min) && empty($max)) {
                   
                    $queryReturn = "AND (preco_venda >= $min && locacao >= '$min')";
                }
                break;
        }

        return $queryReturn;
    }
}
