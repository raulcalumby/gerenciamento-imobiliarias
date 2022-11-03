<?php

namespace Core;
use App\Models\Account;
use App\Models\ACL;
//use App\Models\ProfessoresApi;


/**
 * Base controller
 *
 * PHP version 7.0   
 */
abstract class Controller
{

    /**
     * Parameters from the matched route
     * @var array
     */
    protected $route_params = [];
    protected $professor_data = false;
    protected $public_controllers = array("Login", "Cron", "Webhook", "Site");
    /**
     * Class constructor
     *
     * @param array $route_params  Parameters from the route
     *
     * @return void
     */
    public function __construct($route_params)
    {
        $this->route_params = $route_params;
    }

    /**
     * Magic method called when a non-existent or inaccessible method is
     * called on an object of this class. Used to execute before and after
     * filter methods on action methods. Action methods need to be named
     * with an "Action" suffix, e.g. indexAction, showAction etc.
     *
     * @param string $name  Method name
     * @param array $args Arguments passed to the method
     *
     * @return void
     */
    public function __call($name, $args)
    {
        $method = $name . 'Action';
        //var_dump($this->route_params);
        if (method_exists($this, $method)) {
                if ($this->before() !== false) {
                    call_user_func_array([$this, $method], $args);
                    $this->after();

            }else{
                //Lista de Controller Publicos E.g Login
                if (in_array($this->route_params['controller'], $this->public_controllers)) { 
                    call_user_func_array([$this, $method], $args);
                    $this->after();
                }else{
                    header("location: /login");
                    exit;
                }
            }
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    /**
     * Before filter - called before an action method.
     *
     * @return bool
     */

/*     public function checkSession():bool
    {   
        $valid = FALSE;
		if(isset($_SESSION["id"])) {
            $valid = TRUE;
        }
        return $valid;
    }
 */
    protected function before()
    {   
        //Verifica se esta autenticado
        $account = new Account();
        $ACL = new ACL();
        $logged = $account->checkSession();
        if($logged)
        {
            $this->route_params['account_name'] = $_SESSION['account_name'];
            $this->route_params['account_username'] = $_SESSION['account_username'];
            $this->route_params['account_id'] = $_SESSION['account_id'];
            $this->route_params['account_level'] = $_SESSION['account_level'];

            $this->route_params['acl_sidebar'] = $ACL->getPermissionsByRole($this->route_params['account_level']);
            //Configurações que podem ser utilizada para outros tipos de usuários
            //Professor Data
       /*      if($_SESSION['account_level'] == 'professor'){
                $professoresApi = new ProfessoresApi();
                $this->professor_data = $professoresApi->getProfessorByEmail($this->route_params['account_username'])['data'][0];
            } */
        }
        return $logged;

    }

    /**
     * After filter - called after an action method.
     *
     * @return void
     */
    protected function after()
    {

        //var_dump($this->route_params);

    }
}
