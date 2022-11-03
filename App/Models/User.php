<?php

namespace App\Models;

use PDO;
use App\Models\Account;
use App\Models\ProfessoresApi;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class User extends CrudInit
{

    /**
     * Get all the users as an associative array
     *
     * @return array
     */
    public static function getAll()
    {
        $db = static::getDB();
        $stmt = $db->query('SELECT * FROM accounts');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function list(){

        $conditions['custom_where_query'] = "WHERE account_enabled = 1";
        //$conditions['custom_where_query'] = "WHERE  account_level != 'admin' AND account_enabled = 1";
        $data = $this->getRows("accounts", $conditions);

/*         if($data['gotData']){
            for ($i=0; $i < sizeof($data['data']); $i++) { 
                if( $data['data'][$i]['account_level'] == 'professor'){
                    
                    $professor_data =  $professoresApi->getProfessorByEmail($data['data'][$i]['account_username'])['data'][0];
                    $data['data'][$i]['professor_id'] =  $professor_data['professor_id'];
                    $data['data'][$i]['account_name'] =  $professor_data['nome_professor'];
                }
            }
        } */
        return $data['data'];
    }

    public function getUserByUserName(string $account_username)
    {

        //Config
        //Mount Query
        $conditions['custom_where_query'] = "WHERE account_username = '$account_username' AND account_enabled = '1'";
        $response =  $this->getRows('accounts', $conditions);

        if($response['gotData']){
            return $response;
            
        }else{
            return $response['gotData'];

        }

    }

    public function getUserByID(string $account_id)
    {

        //Config
        //Mount Query
        $conditions['custom_where_query'] = "WHERE account_id = $account_id AND account_enabled = '1'";
        $response =  $this->getRows('accounts', $conditions);

        if($response['gotData']){
            return $response['data'][0];
            
        }else{
            return $response['gotData'];

        }

    }

    public function edit(array $data, int $account_id)
    {        
        $accountApi = new Account();

        $response['status'] = 'error';
        $insert = false;

        extract($data);

        if (empty($account_name)) {
            $response['status-message'] = 'Preencha todos os campos.';
            return $response;
        }
        if(!isset($account_username)){
            $response['status-message'] = 'Preencha todos os campos.';
            return $response;
        }

        if (!$this->isEmailValid($account_username))
		{
            $response['status-message'] = 'Digite um email válido.';
            return $response;
        }

        if(empty($account_level)) {
            $response['status-message'] = 'Preencha todos os campos.';
            return $response;
        }
      

       
        $idFromName = $accountApi->getIdFromName($account_username);
		
		if (!is_null($idFromName) && ($idFromName != $account_id))
		{
			$response['status-message'] = 'User name already used';
			return $response;

		}
		
        if( !empty($password) || !empty($cpassword) ){

            if (!$accountApi->isCPasswdValid($password, $cpassword))
            {
                $response['status-message'] = 'Senhas não coincidem';
                return $response;
            }
                    /* Check if the password is valid. If not, throw an exception */
            if (!$accountApi->isPasswdValid($password))
            {
                $response['status-message'] = 'Senha inválida, é necessário ter pelo menos 8 caracteres.';
                return $response;
            }
    
        }else{
            $password = false;
        }
	
        $data_insert_user['account_name'] = $account_name;
        $data_insert_user['account_username'] = $account_username;
        $data_insert_user['account_level'] = $account_level;
        
        if($password){
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $data_insert_user['account_passwd'] = $hash;
        }

        $where_update['account_id'] = $account_id;
        $update =  $this->update('accounts', $data_insert_user, $where_update);
 
        if($update){
            $response['status'] = 'success';
            $response['status-message'] = 'O Usuário foi atualizado com sucesso!';
        }else{
            $response['status-message'] = 'Occoreu um erro na inserção, tente novamente.!';
        }
        return $response;
    }

    /* A email check for the account username */
    public function isEmailValid(string $email): bool
    {
        /* Initialize the return variable */
        $valid = true;
        
        if(!filter_var($email, FILTER_VALIDATE_EMAIL )) 
        {
            $valid = false;
        }
        return $valid;
    }

    public function disable(int $account_id)
    {
        $response['status'] = 'error';
        $account = new Account();

        if (!$account->isIdValid($account_id))
		{
            $response['status-message'] = 'O usuário não existe, ou já foi deletado';
            return $response;
        }

        //users
        $where['account_id'] = $account_id;
        $data['account_enabled'] = '0';
        $update =  $this->update('accounts', $data, $where);
 

        if($update){
            $response['status'] = 'success';
            $response['status-message'] = 'O usuário foi deletado com sucesso!';
            return $response;
        }
    }
    
}
