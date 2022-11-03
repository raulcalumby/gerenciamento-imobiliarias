<?php


namespace App\Models;

class LeadsApi extends \App\Models\CrudInit
{
    /**
     * Get all the users as an associative array
     *
     * @return array
     */

    //Listagem Índices
    public function list(string $nome ="", int $offset = 0, int $limit = 15): array
    {
        //Config
        $conditions['return_type'] = 'all';
        $conditions['limit'] = $limit;
        $conditions['offset'] = $offset;
        $conditions['order_by'] = 'created DESC';
        $nomeW = "";

        if (!empty($nome)) {
            $nomeW = "AND (name LIKE '%$nome%' || phone LIKE '%$nome%'|| email LIKE '%$nome%') ";
        }

        $conditions['custom_where_query'] = " WHERE enabled = '1' $nomeW";
        $conditions['select'] = '*';
        $response =  $this->getRows('leads', $conditions);
        
        return $response;
    }

    public function edit($data, $id)
    {
        
        $response['status'] = 'error';
        $response['status-message'] = 'Por Favor, preencha todos os campos';

        if(empty($data))
        {
            return $response;
        }
     

        $conditions['leads_id'] = $id;
        $update = $this->update('leads', $data, $conditions);
    
        if($update)
        {
            $response['status'] = 'success';
            $response['status-message'] = 'Lead criado com sucesso! ';
        }

        return $response;
    }


    /* Adicionar Lead */
    public function add($data)
    {
        $response['status'] = 'error';
        $response['status-message'] = 'Por Favor, preencha todos os campos';

        if(empty($data))
        {
            return $response;
        }
        
   
        $insert = $this->insert('leads', $data);
    
        if($insert)
        {
            $response['status'] = 'success';
            $response['status-message'] = 'Lead criado com sucesso! ';
        }

        return $response;
    }



      // Desabilitando Locacao
      public function disable(int $leadsId): array
      {
          $response['status'] = 'error';
  
          if (!$this->isLocacaoIdValid($leadsId)) {
              $response['status-message'] = 'O Lead já foi deletado , ou não existe';
              return $response;
          }
  
          $where['leads_id'] = $leadsId;
          $data['enabled'] = '0';

          $update =  $this->update('leads', $data, $where);

          if ($update) {
              $response['status'] = 'success';
              $response['status-message'] = 'O Lead foi deletado com sucesso';
              return $response;
          }
  
          return $response;
      }
  
      /* Verifica se o Locacao  existe pelo id */
      public function isLocacaoIdValid(int $leadsId): bool
      {
          $conditions['where']['leads_id'] = $leadsId;
          $conditions['where']['enabled'] = 1;
          $response =  $this->getRows('leads', $conditions);
          return $response['gotData'];
      }

      
}
