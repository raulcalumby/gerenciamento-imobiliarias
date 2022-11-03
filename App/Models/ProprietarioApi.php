<?php


namespace App\Models;

use PDO;
use PHPMailer\PHPMailer\PHPMailer;


class ProprietarioApi extends \App\Models\CrudInit
{
    /**
     * Get all the users as an associative array
     *
     * @return array
     */

    //Listagem Proprietários
    public function list(string $buscaRapida = "", string $nome = "", string $cpf = "", int $offset = 0, int $limit = 15): array
    {
        //Config
        $conditions['return_type'] = 'all';
        $conditions['limit'] = $limit;
        $conditions['offset'] = $offset;
        $conditions['order_by'] = 'proprietarios_id DESC';
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
            $buscaRapidaW = "AND (nome_completo LIKE '%$buscaRapida%' || email LIKE '%$buscaRapida%' || email_alternativo LIKE '%$buscaRapida%' ||  cpf LIKE '%$buscaRapida%' ||  cidade LIKE '%$buscaRapida%' || bairro LIKE '%$buscaRapida%')";
        }

        //Mount Query
        $conditions['custom_where_query'] = "WHERE enabled = '1' $buscaRapidaW $nomeW $cpfW ";
        $conditions['select'] = '*, DATE_FORMAT(created,"%d/%m/%Y ás %H:%i:%s") AS created, DATE_FORMAT(modified,"%d/%m/%Y ás %H:%i:%s") AS modified';
        $response =  $this->getRows('proprietarios', $conditions);
        return $response;
    }

    // Cadastro Proprietários
    public function add(array $data): array
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Erro ao adicionar o Proprietário';
        if (empty($data)) {
            $response['status-message'] = 'Preencha todos os campos';
            return $response;
        }
        if (isset($data['imoveis']) && !empty($data['imoveis'])) {
            $arrayImoveis = $data['imoveis'];
            unset($data['imoveis']);
        }
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $response['status-message'] = 'Email Inválido';
            return $response;
        }
        if (!empty($data['email_alternativo']) && !filter_var($data['email_alternativo'], FILTER_VALIDATE_EMAIL)) {
            $response['status-message'] = 'Email Alternativo Inválido';
            return $response;
        }
 


        $data['taxa'] = $this->toEnglishDecimal($data['taxa']);
        $data['cpf'] = $this->limpaCPF_CNPJ($data['cpf']);

        $insert = $this->insert('proprietarios', $data);

        if ($insert) {

            // Se ele selecionou imoveis irei atualizar eles com o id do propietário 
            if (isset($arrayImoveis)) {
                for ($i = 0; $i < sizeof($arrayImoveis); $i++) {
                    $dataUpdate['proprietarios_id'] = $insert;
                    $conditions['imoveis_id'] = $arrayImoveis[$i];
                    $update = $this->update('imoveis', $dataUpdate, $conditions);
                }
            }
            $response['status'] = 'success';
            $response['status-message'] = 'Proprietário cadastrado com sucesso!';
        }
        return $response;
    }

    //Editar Proprietários
    public function edit(array $data, int $id): array
    {

        $response['status'] = 'error';
        $response['status-message'] = 'Erro ao adicionar o Proprietário';
        if (empty($data)) {
            $response['status-message'] = 'Preencha todos os campos';
            return $response;
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $response['status-message'] = 'Email Inválido';
            return $response;
        }
        if (!empty($data['email_alternativo']) && !filter_var($data['email_alternativo'], FILTER_VALIDATE_EMAIL)) {
            $response['status-message'] = 'Email Alternativo Inválido';
            return $response;
        }

        if (!isset($data['tarifa_doc'])) {
            $data['tarifa_doc'] = 0;
        }

        $data['cpf'] = $this->limpaCPF_CNPJ($data['cpf']);

        $data['taxa'] = $this->toEnglishDecimal($data['taxa']);
        $where['proprietarios_id'] = $id;
        $update = $this->update('proprietarios', $data, $where);
        if ($update) {
            $response['status'] = 'success';
            $response['status-message'] = 'Proprietário atualizado com sucesso!';
        }
        return $response;
    }

    // Desabilita o propietário 
    public function disable(int $proprietarios_id): array
    {
        $response['status'] = 'error';


        if (!$this->isPropietarioIdValid($proprietarios_id)) {
            $response['status-message'] = 'O Proprietário não existe, ou já foi deletado';
            return $response;
        }

        $where['proprietarios_id'] = $proprietarios_id;
        $data['enabled'] = '0';
        $update =  $this->update('proprietarios', $data, $where);
        if ($update) {
            $response['status'] = 'success';
            $response['status-message'] = 'O Proprietário foi deletado com sucesso!';
            return $response;
        }

        return $response;
    }

    /* Verifica se o propietário existe pelo id */
    public function isPropietarioIdValid(int $proprietario_id): bool
    {
        $conditions['where']['proprietarios_id'] = $proprietario_id;
        $conditions['where']['enabled'] = 1;
        $response =  $this->getRows('proprietarios', $conditions);
        return $response['gotData'];
    }

    //Seleciona Propetário pelo ID e devolve seus dados
    public function getPropietarioById(int $proprietario_id): array
    {
        $conditions['where']['proprietarios_id'] = $proprietario_id;
        $conditions['where']['enabled'] = 1;
        $response =  $this->getRows('proprietarios', $conditions);
        return $response;
    }

    // Seleciona Proprietários para o SelectTWO
    public function simpleList(string $query,  int $offset = 0, int $limit = 999): array
    {
        //Config
        $conditions['return_type'] = 'all';
        $conditions['limit'] = 999;
        $conditions['offset'] = $offset;

        $conditions['order_by'] = 'proprietarios_id DESC';
        $query_W = '';
        if (!empty($query)) {
            $query_W = "AND (nome_completo LIKE '%$query%')";
        }

        $conditions['select'] = '*, proprietarios_id AS id, nome_completo AS text , DATE_FORMAT(created,"%d/%m/%Y ás %H:%i:%s") AS created, DATE_FORMAT(modified,"%d/%m/%Y ás %H:%i:%s") AS modified';
        $conditions['custom_where_query'] = "WHERE enabled = 1 $query_W   ";
        $response =  $this->getRows('proprietarios', $conditions);
        return $response;
    }

    function toEnglishDecimal($val)
    {
        /*
    Converte $val para string e troca a virgula por ponto
    */
        $res = str_replace(".", "", $val);
        return str_replace(",", ".", $res);
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


    public function envEmailProprietario(int $proprietario_id, string $data) : array
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Falha ao enviar o email. ';

        $proprietarioApi  =  new ProprietarioApi();
        $proprietarioData = $proprietarioApi->getPropietarioById($proprietario_id);

        if (!$proprietarioData['gotData']) {
            return $response;
        }

        $proprietarioData = $proprietarioData['data'][0];

        $email = strval($proprietarioData['email']);
        $nome = strval($proprietarioData['nome_completo']);

        /* Validações  */
        if (empty($email) && empty($proprietarioData['email_alternativo']))
        {
            $response['status-message']  = "O Proprietário não tem um email, insira um clicando <a href='../../../../../proprietario/$proprietario_id/edit' target='_blank'>Aqui</a>";
            return $response;
        } else if(empty($email) && !empty($proprietarioData['email_alternativo']) ) {
            $email  = strval($proprietarioData['email_alternativo']);
        }

        if (empty($nome)) {
            $response['status-message'] = "O Proprietário não tem um nome, insira um clicando <a href='../../../../../proprietario/$proprietario_id/edit' target='blank'>Aqui</a>";
            return $response;
        }
        
        if(empty($data))
        {
            $response['status-message'] = "Por favor selecione a data do extrato. :)";
            return $response;
        }
        
        /* Gerando link do pdf */
        $linkPdf = $_SERVER['HTTP_ORIGIN']. "/extrato/ver/$proprietario_id?data=$data";
        
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'polaris.prodns.com.br';
        $mail->SMTPAuth = true;
        $mail->Username = 'noreply@lnxweb.com.br';
        $mail->Password = '@lppz70';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->setFrom('noreply@lnxweb.com.br', 'Extrato do mês - Bellintani');
        $mail->addAddress($email, $nome);
        $mail->AddBCC('noreply@lnxweb.com.br', 'Extrato do mês - Bellintani');

        $corpo =  "<p> Olá, $nome tudo bem? aqui está o seu extrato gerado pela Imobiliária Bellintani </p>";
        $corpo .=  " Clique <a  href='bellintani.localhost' target='_blank'>Aqui</a> para acessar o extrato";
        $assunto = "Extrato do mês - Imobiliária Bellintani";

        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);
        $mail->Subject = $assunto;
        $mail->Body = $corpo;

        if ($mail->send()) {
            $response['status'] = 'success';
            $response['status-message'] = 'Email enviado com sucesso';
        }

        return $response;
    }
}
