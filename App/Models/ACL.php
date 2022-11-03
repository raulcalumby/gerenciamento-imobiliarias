<?php

namespace App\Models;

use PDO;
/* use App\Models\ModalidadesApi;
use App\Models\CondominiosApi;
use App\Models\ProfessoresApi;
use App\Models\AlunosApi;
use App\Models\TurmasApi;
 */
/**
 * Example user model
 *
 * PHP version 7.0
 */
class ACL extends CrudInit
{
    /**
     * Get all the users as an associative array
     *
     * @return array
     */


    public function getPermissionsByRole(string $role)
    {
        //Config
         $response = false;
        //Mount Query
        $conditions['select'] = '*';
        $conditions['custom_where_query'] = "WHERE role = '$role'";
        $data =  $this->getRows('page_permissions', $conditions);
        $alunos_id = array();
        if($data['gotData']){

            foreach ($data['data'][0] as $key => $value) {

                switch ($value) {
                    case 1:
                        $response[$key] = 'd-block';
                        break;
                    case 0:
                        $response[$key] = 'd-none';
                        break;
                    default:
                        $response[$key] = $value;
                        break;
                }

            }
        }
        return $response;
    }
}
