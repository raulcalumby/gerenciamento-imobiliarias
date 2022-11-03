<?php

namespace App\Models;
//session_start();

use PDO;

class Account extends \Core\Model
{
	/* Class properties (variables) */
	
	/* The ID of the logged in account (or NULL if there is no logged in account) */
	private $id;
	
	/* The name of the logged in account (or NULL if there is no logged in account) */
	private $name;
	
	/* TRUE if the user is authenticated, FALSE otherwise */
	private $authenticated;
	
	
	/* Public class methods (functions) */
	
	/* Constructor */
	public function __construct()
	{
		/* Initialize the $id and $name variables to NULL */
		$this->id = NULL;
		$this->name = NULL;
		$this->authenticated = FALSE;
	}
	
	/* Destructor */
	public function __destruct()
	{
		
	}
	
	/* "Getter" function for the $id variable */
	public function getId(): ?int
	{
		return $this->id;
	}
	
	/* "Getter" function for the $name variable */
	public function getName(): ?string
	{
		return $this->name;
	}
    
    public function checkSession():bool
    {   
        $valid = FALSE;
		if(isset($_SESSION["account_id"])) {
            $valid = TRUE;
        }
        return $valid;
    }
	/* "Getter" function for the $authenticated variable */
	public function isAuthenticated(): bool
	{
		return $this->authenticated;
	}
	
	/* Add a new account to the system and return its ID (the account_id column of the accounts table) */
	public function addAccount(string $name, string $username, string $passwd, string $cpasswd, string $level): array
	{
		/* Create $db object from Model */
		//$db = static::getDB();
        $db = static::getDB();
		
		/* Trim the strings to remove extra spaces */
		$name = trim($name);
		$username = trim($username);
        $passwd = trim($passwd);
        $cpasswd = trim($cpasswd);
        
        $response['status'] = 'error';
        
             /* Check if the user name is valid. If not, throw an exception */
		if (!$this->isLevelValid($level))
		{
            $response['status-message'] = 'Nivel de usuário inválido';
            return $response;
        }

        /* Check if the user name is valid. If not, throw an exception */
		if (!$this->isCPasswdValid($passwd, $cpasswd))
		{
            $response['status-message'] = 'Senhas não coincidem';
            return $response;
        }

		/* Check if the user name is valid. If not, throw an exception */
		if (!$this->isNameValid($name))
		{
            $response['status-message'] = 'Nome Inválido';
            return $response;
        }
        
        if (!$this->isEmailValid($username))
		{
            $response['status-message'] = 'E-mail inválido';
            return $response;
        }
        
		/* Check if the password is valid. If not, throw an exception */
		if (!$this->isPasswdValid($passwd))
		{
            $response['status-message'] = 'Senha inválida, é necessário ter pelo menos 8 caracteres.';
            return $response;
        }
		
		/* Check if an account having the same name already exists. If it does, throw an exception */
		if (!is_null($this->getIdFromName($username)))
		{
            $response['status-message'] = 'Este login já esta em uso';
            return $response;
		}
		
		/* Finally, add the new account */
		
		/* Insert query template */
		$query = 'INSERT INTO accounts (account_name, account_username, account_passwd, account_level) VALUES (:name, :username, :passwd, :level)';
		
		/* Password hash */
		$hash = password_hash($passwd, PASSWORD_DEFAULT);
		
		/* Values array for PDO */
		$values = array(':username' => $username, ':name' => $name, ':passwd' => $hash, ':level' => $level);
		
		/* Execute the query */
		try
		{
			$res = $db->prepare($query);
			$res->execute($values);
		}
		catch (PDOException $e)
		{
		   /* If there is a PDO exception, throw a standard exception */
            $response['status-message'] = 'Database query error';        
            return $response;
		}
		
		/* Return the new ID */

        $response["account_id"] = $db->lastInsertId();
        $response['status'] = 'success';
        $response['status-message'] = "Cadastro realizado!";
        //$response['status-message'] = "Usuário salvo com sucesso! <br> Ele já pode fazer login com os dados abaixo";
        $response['result']['username'] = $username;
        $response['result']['passwd'] = $passwd;

        return $response;
	}
	
	/* Delete an account (selected by its ID) */
	public function deleteAccount(int $id)
	{
		/* Create $db object from Model */
		$db = static::getDB();
		
		/* Check if the ID is valid */
		if (!$this->isIdValid($id))
		{
			throw new \Exception('Invalid account ID');
		}
		
		/* Query template */
		$query = 'DELETE FROM accounts WHERE (account_id = :id)';
		
		/* Values array for PDO */
		$values = array(':id' => $id);
		
		/* Execute the query */
		try
		{
			$res = $db->prepare($query);
			$res->execute($values);
		}
		catch (PDOException $e)
		{
		   /* If there is a PDO exception, throw a standard exception */
		   throw new \Exception('Database query error');
		}
		
		/* Delete the Sessions related to the account */
		$query = 'DELETE FROM account_sessions WHERE (account_id = :id)';
		
		/* Values array for PDO */
		$values = array(':id' => $id);
		
		/* Execute the query */
		try
		{
			$res = $db->prepare($query);
			$res->execute($values);
		}
		catch (PDOException $e)
		{
		   /* If there is a PDO exception, throw a standard exception */
		   throw new \Exception('Database query error');
		}
	}
    
    	/* Edit an account (selected by its ID). The name, the password and the status (enabled/disabled) can be changed */
	public function editPassAccount(int $id, string $currentpasswd, string $passwd, string $cpasswd)
	{

        $response['status'] = 'error';
        $params = func_get_args();
        if (!$this->isArgsValid($params))
        {
            $response['status-message'] = 'Preencha todos os campos corretamente;';
            return $response;
        }

		/* Create $db object from Model */
        $db = static::getDB();
		
		/* Trim the strings to remove extra spaces */
		$passwd = trim($passwd);
		$cpasswd = trim($cpasswd);
		
		/* Check if the ID is valid */
		if (!$this->isIdValid($id))
		{
            $response['status-message'] = 'ID User inválido';
            return $response;
		}
        
        /* Check if the password and cpassword is equal. */
		if (!$this->isCPasswdValid($passwd, $cpasswd))
		{
            $response['status-message'] = 'As senhas não coincidem!';
            return $response;
        }
		
		/* Check if the password and cpassword is equal. */
		if (!$this->isCurrentPasswdValid($id, $currentpasswd))
		{
            $response['status-message'] = 'A senha atual está incorreta!';
            return $response;
        }

        /* Check if the password is valid. If not, throw an exception */
        if (!$this->isPasswdValid($passwd))
        {
            $response['status-message'] = 'Senha inválida, é necessário ter pelo menos 8 caracteres.';
            return $response;
        }
            

		/* Finally, edit the account */
		
		/* Edit query template */
		$query = 'UPDATE accounts SET account_passwd = :passwd WHERE account_id = :id';
		
		/* Password hash */
		$hash = password_hash($passwd, PASSWORD_DEFAULT);
		
		
		/* Values array for PDO */
		$values = array(':passwd' => $hash, ':id' => $id);
		
		/* Execute the query */
		try
		{
			$res = $db->prepare($query);
			$res->execute($values);
		}
		catch (PDOException $e)
		{
            /* If there is a PDO exception, throw a standard exception */
            $response['status-message'] = 'Database query error';        
            return $response;
        }
        
        /* Return the new ID */

        $response['status'] = 'success';
        $response['status-message'] = "Nova senha salva com sucesso!";
        return $response;
	}
	


	/* Edit an account (selected by its ID). The name, the password and the status (enabled/disabled) can be changed */
	public function editAccount(int $id, string $name, string $passwd, bool $enabled)
	{
        $response['status'] = 'error';

		/* Create $db object from Model */
		$db = static::getDB();
		
		/* Trim the strings to remove extra spaces */
		$name = trim($name);
		$passwd = trim($passwd);
		
		/* Check if the ID is valid */
		if (!$this->isIdValid($id))
		{
			$response['status-message'] = 'Invalid account ID';
			return $response;

		}
		
		/* Check if the user name is valid. */
		if (!$this->isNameValid($name))
		{
			$response['status-message'] = 'Invalid user name';
			return $response;

		}
		
		/* Check if the password is valid. */
		if (!$this->isPasswdValid($passwd))
		{
			$response['status-message'] = 'Senha inválida, é necessário ter pelo menos 8 caracteres.';
			return $response;

		}
		
		/* Check if an account having the same name already exists (except for this one). */
		$idFromName = $this->getIdFromName($name);
		
		if (!is_null($idFromName) && ($idFromName != $id))
		{
			$response['status-message'] = 'User name already used';
			return $response;

		}
		
		/* Finally, edit the account */
		
		/* Edit query template */
		$query = 'UPDATE accounts SET account_username = :name, account_passwd = :passwd, account_enabled = :enabled WHERE account_id = :id';
		
		/* Password hash */
		$hash = password_hash($passwd, PASSWORD_DEFAULT);
		
		/* Int value for the $enabled variable (0 = false, 1 = true) */
		$intEnabled = $enabled ? 1 : 0;
		
		/* Values array for PDO */
		$values = array(':name' => $name, ':passwd' => $hash, ':enabled' => $intEnabled, ':id' => $id);
		
		/* Execute the query */
		try
		{
			$res = $db->prepare($query);
			$res->execute($values);
		}
		catch (PDOException $e)
		{
		   /* If there is a PDO exception, throw a standard exception */
		   $response['status-message'] = 'Database query error';
		   return $response;

		}

		$response['status'] = 'success';
        $response['status-message'] = "Usuário atualizado com sucesso!";
        return $response;
	}
	
	/* Edit an account (selected by its ID). The name, the password and the status (enabled/disabled) can be changed */
	public function editAccountProfessor(int $id, string $name, bool $enabled, $passwd = false) 
	{
        $response['status'] = 'error';

		/* Create $db object from Model */
		$db = static::getDB();
		
		/* Trim the strings to remove extra spaces */
		$name = trim($name);
		if($passwd){
			$passwd = trim($passwd);
			/* Check if the password is valid. */
			if (!$this->isPasswdValid($passwd))
			{
				$response['status-message'] = 'Senha inválida, é necessário ter pelo menos 8 caracteres.';
				return $response;
			}
		}
		/* Check if the ID is valid */
		if (!$this->isIdValid($id))
		{
			$response['status-message'] = 'Invalid account ID';
			return $response;

		}
		
		/* Check if the user name is valid. */
		if (!$this->isNameValid($name))
		{
			$response['status-message'] = 'Invalid user name';
			return $response;

		}
		
	
		
		/* Check if an account having the same name already exists (except for this one). */
		$idFromName = $this->getIdFromName($name);
		
		if (!is_null($idFromName) && ($idFromName != $id))
		{
			$response['status-message'] = 'User name already used';
			return $response;

		}
		
		/* Finally, edit the account */
		
		/* Edit query template */
		if($passwd){

			$query = 'UPDATE accounts SET account_username = :name, account_passwd = :passwd, account_enabled = :enabled WHERE account_id = :id';
			
			/* Password hash */
			$hash = password_hash($passwd, PASSWORD_DEFAULT);
			
			/* Int value for the $enabled variable (0 = false, 1 = true) */
			$intEnabled = $enabled ? 1 : 0;
			
			/* Values array for PDO */
			$values = array(':name' => $name, ':passwd' => $hash, ':enabled' => $intEnabled, ':id' => $id);

		}else{

			$query = 'UPDATE accounts SET account_username = :name, account_enabled = :enabled WHERE account_id = :id';
			
			/* Password hash */
			
			/* Int value for the $enabled variable (0 = false, 1 = true) */
			$intEnabled = $enabled ? 1 : 0;
			
			/* Values array for PDO */
			$values = array(':name' => $name, ':enabled' => $intEnabled, ':id' => $id);
		}
		/* Execute the query */
		try
		{
			$res = $db->prepare($query);
			$res->execute($values);
		}
		catch (PDOException $e)
		{
		   /* If there is a PDO exception, throw a standard exception */
		   $response['status-message'] = 'Database query error';
		   return $response;

		}

		$response['status'] = 'success';
        $response['status-message'] = "Usuário atualizado com sucesso!";
        return $response;
	}

	/* Login with username and password */
	public function login(string $username, string $passwd): array
	{
        $response['status'] = 'error';
		$response['status-message'] = 'Dados de login incorretos, tente novamente.';

        $params = func_get_args();
        if (!$this->isArgsValid($params))
        {
            $response['status-message'] = 'Preencha todos os campos corretamente';
            return $response;
        }

		/* Create $db object from Model */
		$db = static::getDB();
		
		/* Trim the strings to remove extra spaces */
		$username = trim($username);
		$passwd = trim($passwd);
		
		/* Check if the user name is valid. If not, return FALSE meaning the authentication failed */
		if (!$this->isEmailValid($username))
		{
            $response['status-message'] = 'E-mail inválido!';
            return $response;
		}
		
		/* Check if the password is valid. If not, return FALSE meaning the authentication failed */
		if (!$this->isPasswdValid($passwd))
		{
            $response['status-message'] = 'Senha inválida!';
            return $response;
		}
		
		/* Look for the account in the db. Note: the account must be enabled (account_enabled = 1) */
		$query = 'SELECT * FROM accounts WHERE (account_username = :username) AND (account_enabled = 1)';
		
		/* Values array for PDO */
		$values = array(':username' => $username);
		
		/* Execute the query */
		try
		{
			$res = $db->prepare($query);
			$res->execute($values);
		}
		catch (PDOException $e)
		{
		   /* If there is a PDO exception, throw a standard exception */
            $response['status-message'] = 'Database query error';
            return $response;
		}
		
		$row = $res->fetch(PDO::FETCH_ASSOC);
		
		/* If there is a result, we must check if the password matches using password_verify() */
		if (is_array($row))
		{
			if (password_verify($passwd, $row['account_passwd']))
			{
				/* Authentication succeeded. Set the class properties (id and name) */
				$this->id = intval($row['account_id'], 10);
				$this->name = $row['account_name'];
				$this->username = $username;
                $this->authenticated = TRUE;

                $_SESSION["account_id"] = $row['account_id'];
                $_SESSION["account_name"] = $row['account_name'];
                $_SESSION["account_username"] = $row['account_username'];
				$_SESSION['account_level'] =  $row['account_level']; 
				
				/* Register the current Sessions on the database */
				$this->registerLoginSession();
				
                /* Finally, Return TRUE */
                $response['status'] = 'success';
                $response['account_id'] = $this->id;
                $response['account_level'] = $_SESSION['account_level'];
                return $response;
				//return TRUE;
			}
		}
		
		/* If we are here, it means the authentication failed: return FALSE */
		//return FALSE;
        return $response;
	}
    
    public function isArgsValid(array $args): bool
    {
		$valid = TRUE;

        foreach ($args as $key => $arg) {
            if(empty($arg)){
                $valid = FALSE;
            break;
            }
        }

        return $valid;
    }
    public function isCurrentPasswdValid(int $id, string $currentpasswd): bool
    {
		$valid = TRUE;

        /* Create $db object from Model */
		$db = static::getDB();
		/* Look for the account in the db. Note: the account must be enabled (account_enabled = 1) */
		$query = 'SELECT * FROM accounts WHERE (account_id = :id)';
		
		/* Values array for PDO */
		$values = array(':id' => $id);
		
		/* Execute the query */
		try
		{
			$res = $db->prepare($query);
			$res->execute($values);
		}
		catch (PDOException $e)
		{
		   /* If there is a PDO exception, throw a standard exception */
		   throw new \Exception('Database query error');
		}
		
		$row = $res->fetch(PDO::FETCH_ASSOC);
		
		/* If there is a result, we must check if the password matches using password_verify() */
		if (is_array($row))
		{
			if (!password_verify($currentpasswd, $row['account_passwd']))
			{
				/* Finally, Return FALSE */
				$valid =  FALSE;
			}
        }
        return $valid;
    }
	/* A sanitization check for the account username */
	public function isNameValid(string $name): bool
	{
		/* Initialize the return variable */
		$valid = TRUE;
		
		/* Example check: the length must be between 8 and 16 chars */
		$len = mb_strlen($name);
		
		if (($len < 1))
		{
			$valid = FALSE;
		}
		
		/* You can add more checks here */
		
		return $valid;
	}
    /* A email check for the account username */
    public function isEmailValid(string $email): bool
    {
        /* Initialize the return variable */
        $valid = TRUE;
        
        if(!filter_var($email, FILTER_VALIDATE_EMAIL )) 
        {
            $valid = FALSE;
        }
        return $valid;
    }
    /* A email check for the account username */
    public function isLevelValid(string $level): bool
    {
        /* Initialize the return variable */
        $valid = FALSE;
		
		switch ($level) {
			case 'admin':
				$valid = TRUE;
				break;
			case 'colaborador':
				$valid = TRUE;
				break;
		}
  
        return $valid;
    }
	/* A sanitization check for the account password */
	public function isPasswdValid(string $passwd): bool
	{
		/* Initialize the return variable */
		$valid = TRUE;
		
		/* Example check: the length must be between 8 and 16 chars */
		$len = mb_strlen($passwd);
		
		if (($len < 8))
		{
			$valid = FALSE;
		}
		
		/* You can add more checks here */
		
		return $valid;
	}
    public function isCPasswdValid(string $passwd, string $cpasswd): bool
	{
		/* Initialize the return variable */
		$valid = TRUE;
		
		if ($passwd !== $cpasswd)
		{
			$valid = FALSE;
		}
		
		/* You can add more checks here */
		
		return $valid;
	}
	/* A sanitization check for the account ID */
	public function isIdValid(int $id): bool
	{
		/* Initialize the return variable */
		$valid = TRUE;
		
		/* Example check: the ID must be between 1 and 1000000 */
		
		if (($id < 1) || ($id > 1000000))
		{
			$valid = FALSE;
		}
		
		/* You can add more checks here */
		
		return $valid;
	}
	
	/* Login using Sessions */
	public function sessionLogin(): bool
	{
		/* Create $db object from Model */
		$db = static::getDB();
		
		/* Check that the Session has been started */
		if (session_status() == PHP_SESSION_ACTIVE)
		{
			/* 
				Query template to look for the current session ID on the account_sessions table.
				The query also make sure the Session is not older than 7 days
			*/
			$query = 
			
			'SELECT * FROM account_sessions, accounts WHERE (account_sessions.session_id = :sid) ' . 
			'AND (account_sessions.login_time >= (NOW() - INTERVAL 7 DAY)) AND (account_sessions.account_id = accounts.account_id) ' . 
			'AND (accounts.account_enabled = 1)';
			
			/* Values array for PDO */
			$values = array(':sid' => session_id());
			
			/* Execute the query */
			try
			{
				$res = $db->prepare($query);
				$res->execute($values);
			}
			catch (PDOException $e)
			{
			   /* If there is a PDO exception, throw a standard exception */
			   throw new \Exception('Database query error');
			}
			
			$row = $res->fetch(PDO::FETCH_ASSOC);
			
			if (is_array($row))
			{
				/* Authentication succeeded. Set the class properties (id and name) and return TRUE*/
				$this->id = intval($row['account_id'], 10);
				$this->name = $row['account_username'];
				$this->authenticated = TRUE;
				
				return TRUE;
			}
		}
		
		/* If we are here, the authentication failed */
		return FALSE;
	}
	
	/* Logout the current user */
	public function logout()
	{
		/* Create $db object from Model */
		$db = static::getDB();
		
		/* If there is no logged in user, do nothing */
		if (is_null($this->id))
		{
			//return;
		}
		
		/* Reset the account-related properties */
		$this->id = NULL;
		$this->name = NULL;
		$this->authenticated = FALSE;
		
		/* If there is an open Session, remove it from the account_sessions table */
		if (session_status() == PHP_SESSION_ACTIVE)
		{
			/* Delete query */
			$query = 'DELETE FROM account_sessions WHERE (session_id = :sid)';
			
			/* Values array for PDO */
			$values = array(':sid' => session_id());
			
			/* Execute the query */
			try
			{
				$res = $db->prepare($query);
				$res->execute($values);
			}
			catch (PDOException $e)
			{
			   /* If there is a PDO exception, throw a standard exception */
			   throw new \Exception('Database query error');
            }
            

        }
        setcookie('account_id', '', time() - (86400 * 30), "/","");  // 86400 = 1 day
        session_destroy();
	}
	
	/* Close all account Sessions except for the current one (aka: "logout from other devices") */
	public function closeOtherSessions()
	{
		/* Create $db object from Model */
		$db = static::getDB();
		
		/* If there is no logged in user, do nothing */
		if (is_null($this->id))
		{
			return;
		}
		
		/* Check that a Session has been started */
		if (session_status() == PHP_SESSION_ACTIVE)
		{
			/* Delete all account Sessions with session_id different from the current one */
			$query = 'DELETE FROM account_sessions WHERE (session_id != :sid) AND (account_id = :account_id)';
			
			/* Values array for PDO */
			$values = array(':sid' => session_id(), ':account_id' => $this->id);
			
			/* Execute the query */
			try
			{
				$res = $db->prepare($query);
				$res->execute($values);
			}
			catch (PDOException $e)
			{
			   /* If there is a PDO exception, throw a standard exception */
			   throw new \Exception('Database query error');
			}
		}
	}
	
	/* Returns the account id having $name as name, or NULL if it's not found */
	public function getIdFromName(string $username): ?int
	{
		/* Create $db object from Model */
		$db = static::getDB();
		
		/* Since this method is public, we check $name again here */
		if (!$this->isNameValid($username))
		{
			throw new \Exception('Invalid user name');
		}
		
		/* Initialize the return value. If no account is found, return NULL */
		$id = NULL;
		
		/* Search the ID on the database */
		$query = 'SELECT account_id FROM accounts WHERE (account_username = :username)';
		$values = array(':username' => $username);
		
		try
		{
			$res = $db->prepare($query);
			$res->execute($values);
		}
		catch (PDOException $e)
		{
		   /* If there is a PDO exception, throw a standard exception */
		   throw new \Exception('Database query error');
		}
		
		$row = $res->fetch(PDO::FETCH_ASSOC);
		
		/* There is a result: get it's ID */
		if (is_array($row))
		{
			$id = intval($row['account_id'], 10);
		}
		
		return $id;
	}
	
	
	/* Private class methods */
	
	/* Saves the current Session ID with the account ID */
	private function registerLoginSession()
	{
		/* Create $db object from Model */
		$db = static::getDB();
		
		/* Check that a Session has been started */
		if (session_status() == PHP_SESSION_ACTIVE)
		{
			/* 	Use a REPLACE statement to:
				- insert a new row with the session id, if it doesn't exist, or...
				- update the row having the session id, if it does exist.
			*/
			$query = 'REPLACE INTO account_sessions (session_id, account_id, login_time) VALUES (:sid, :accountId, NOW())';
			$values = array(':sid' => session_id(), ':accountId' => $this->id);
			
			/* Execute the query */
			try
			{
				$res = $db->prepare($query);
				$res->execute($values);
			}
			catch (PDOException $e)
			{
			   /* If there is a PDO exception, throw a standard exception */
			   throw new \Exception('Database query error');
			}
		}
	}
}
