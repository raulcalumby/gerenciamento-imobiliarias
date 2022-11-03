<?php
namespace App\Models;
/*
 * DB Class
 * This class is used for database related (connect, insert, update, and delete) operations
 * with PHP Data Objects (PDO)
 * @author    semicolonworld.com
 * @url       http://www.semicolonworld.com
 * @license   http://www.semicolonworld.com/license
 */
use PDO;

class CrudInit extends \Core\Model{

    /*
     * Returns rows from the database based on the conditions
     * @param string name of the table
     * @param array select, where, order_by, limit and return_type conditions
     */ 
    private $db;

    public function __construct(){
        // create an instance of OtherClass and associate it to $this->instanceOfOtherClass
        $this->db = static::getDB();
    }

    
    public function getRows($table, $conditions = array()){
        $data['gotData'] = true;
        $sql = 'SELECT ';
        $sql_info_records = "SELECT count(*) as count"; //query de paginação
        $sql .= array_key_exists("select",$conditions)?$conditions['select']:'*';
        $sql .= ' FROM '.$table;
        $sql_info_records .= ' FROM '.$table;
        if(array_key_exists("where",$conditions)){
            if(!empty($conditions['where'])){
                $sql .= ' WHERE ';
                $sql_info_records .= ' WHERE ';
                $i = 0;
                foreach($conditions['where'] as $key => $value){
                    $pre = ($i > 0)?' AND ':'';
                    $sql .= $pre.$key." LIKE '".$value."'";
                    $sql_info_records .= $pre.$key." LIKE '".$value."'";
                    $i++;
                }
            }
        }
        //Custom WHERE with OR and etc
        if(array_key_exists("custom_where_query",$conditions)){
            if(!empty($conditions['custom_where_query'])){
                $sql .= " " .$conditions['custom_where_query'];
                $sql_info_records .= " " .$conditions['custom_where_query'];
            }
        }
        
        if(array_key_exists("order_by",$conditions)){
            $sql .= ' ORDER BY '.$conditions['order_by']; 
        }
        
        if(array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){
            $sql .= ' LIMIT '.$conditions['start'].','.$conditions['limit']; 
            $limit = $conditions['limit'];
        }elseif(!array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){
            $sql .= ' LIMIT '.$conditions['limit']; 
            $limit = $conditions['limit'];
        }
        
        if(array_key_exists("offset",$conditions)){
            $sql .= ' OFFSET '.$conditions['offset']; 
            $data['draw'] = 1;
        }
        //var_dump($sql);
        $query = $this->db->prepare($sql);
        $query->execute();
        
        if(array_key_exists("return_type",$conditions) && $conditions['return_type'] != 'all'){
            switch($conditions['return_type']){
                case 'count':
                    $data['data'] = $query->rowCount();
                    break;
                case 'single':
                    $data['data'] = $query->fetch(PDO::FETCH_ASSOC);
                    break;
                default:
                    $data['data'] = '';
            }
        }else{
            if($query->rowCount() > 0){
                $data['data'] = $query->fetchAll();
                //$data['recordsFiltered'] = $query->rowCount();
            }
        }
            $stmt = $this->db->prepare($sql_info_records); 
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $total_records = $row['count'];
            $data['recordsTotal'] = intval($total_records);
            $data['recordsFiltered'] = intval($total_records);
            if(array_key_exists("limit",$conditions)){
                $total_pages = ceil($total_records / $limit);
                $data['total_pages'] = intval(ceil($total_records / $limit));
            }
        $data['sql_query'] = $sql;
        
        //return !empty($data['data'])?$data:false;
        if(empty($data['data'])){
            $data['data'] = [];
            $data['gotData'] = false;

        }
        return $data;
    }
    
    /*
     * Insert data into the database
     * @param string name of the table
     * @param array the data for inserting into the table
     */
    public function insert($table,$data){
        if(!empty($data) && is_array($data)){
            $columns = '';
            $values  = '';
            $i = 0;
            if(!array_key_exists('created',$data)){
                $data['created'] = date("Y-m-d H:i:s");
            }
            if(!array_key_exists('modified',$data)){
                $data['modified'] = date("Y-m-d H:i:s");
            }

            $columnString = implode(',', array_keys($data));
            $valueString = ":".implode(',:', array_keys($data));
            $sql = "INSERT INTO ".$table." (".$columnString.") VALUES (".$valueString.")";
            $query = $this->db->prepare($sql);
            foreach($data as $key=>$val){
                 $query->bindValue(':'.$key, $val);
            }
            $insert = $query->execute();
            return $insert?$this->db->lastInsertId():false;
        }else{
            return false;
        }
    }
    
    /*
     * Update data into the database
     * @param string name of the table
     * @param array the data for updating into the table
     * @param array where condition on updating data
     */
    public function update($table,$data,$conditions){
        if(!empty($data) && is_array($data)){
            $colvalSet = '';
            $whereSql = '';
            $i = 0;
            //Ativar Isso na criação de sistemas de log
            if(!array_key_exists('modified',$data)){
                $data['modified'] = date("Y-m-d H:i:s");
            }
            foreach($data as $key=>$val){
                $pre = ($i > 0)?', ':'';
                if($val == NULL){
                    $colvalSet .= $pre.$key." = NULL";

                }else{
                    $colvalSet .= $pre.$key."='".$val."'";
                }
                $i++;
            }
            if(!empty($conditions)&& is_array($conditions)){
                $whereSql .= ' WHERE ';
                $i = 0;
                foreach($conditions as $key => $value){
                    $pre = ($i > 0)?' AND ':'';
                    $whereSql .= $pre.$key." = '".$value."'";
                    $i++;
                }
            }

            $sql = "UPDATE ".$table." SET ".$colvalSet.$whereSql;
            //echo $sql;
            //return false;
            $query = $this->db->prepare($sql);
            $update = $query->execute();
            //return $update?$query->rowCount():false;
            return $update?true:false;
        }else{
            return false;
        }
    }
    
    /*
     * Delete data from the database
     * @param string name of the table
     * @param array where condition on deleting data
     */
    public function delete($table,$conditions){
        $whereSql = '';
        if(!empty($conditions)&& is_array($conditions)){
            $whereSql .= ' WHERE ';
            $i = 0;
            foreach($conditions as $key => $value){
                $pre = ($i > 0)?' AND ':'';
                $whereSql .= $pre.$key." = '".$value."'";
                $i++;
            }
        }
        $sql = "DELETE FROM ".$table.$whereSql;
        $delete = $this->db->exec($sql);
        return $delete?$delete:false;
    }

    public function getRowsForSite($table, $conditions = array()){
        $data['gotData'] = true;
        $sql = 'SELECT ';
        $sql .= $addOnSQLInfo = array_key_exists("select",$conditions)?$conditions['select']:'*';
        $addOnSQLInfo .= ',';
        $sql_info_records = "SELECT $addOnSQLInfo count(*) as count"; //query de paginação

        $sql .= ' FROM '.$table;
        $sql_info_records .= ' FROM '.$table;
        if(array_key_exists("where",$conditions)){
            if(!empty($conditions['where'])){
                $sql .= ' WHERE ';
                $sql_info_records .= ' WHERE ';
                $i = 0;
                foreach($conditions['where'] as $key => $value){
                    $pre = ($i > 0)?' AND ':'';
                    $sql .= $pre.$key." LIKE '".$value."'";
                    $sql_info_records .= $pre.$key." LIKE '".$value."'";
                    $i++;
                }
            }
        }
        //Custom WHERE with OR and etc
        if(array_key_exists("custom_where_query",$conditions)){
            if(!empty($conditions['custom_where_query'])){
                $sql .= " " .$conditions['custom_where_query'];
                $sql_info_records .= " " .$conditions['custom_where_query'];
            }
        }
        
        if(array_key_exists("order_by",$conditions)){
            $sql .= ' ORDER BY '.$conditions['order_by']; 
        }
        
        if(array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){
            $sql .= ' LIMIT '.$conditions['start'].','.$conditions['limit']; 
            $limit = $conditions['limit'];
        }elseif(!array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){
            $sql .= ' LIMIT '.$conditions['limit']; 
            $limit = $conditions['limit'];
        }
        
        if(array_key_exists("offset",$conditions)){
            $sql .= ' OFFSET '.$conditions['offset']; 
            $data['draw'] = 1;
        }
        //var_dump($sql);
        $query = $this->db->prepare($sql);
        $query->execute();
        
        if(array_key_exists("return_type",$conditions) && $conditions['return_type'] != 'all'){
            switch($conditions['return_type']){
                case 'count':
                    $data['data'] = $query->rowCount();
                    break;
                case 'single':
                    $data['data'] = $query->fetch(PDO::FETCH_ASSOC);
                    break;
                default:
                    $data['data'] = '';
            }
        }else{
            if($query->rowCount() > 0){
                $data['data'] = $query->fetchAll();
                //$data['recordsFiltered'] = $query->rowCount();
            }
        }
        
            $stmt = $this->db->prepare($sql_info_records); 
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $total_records = $row['count'];
            $data['recordsTotal'] = intval($total_records);
            $data['recordsFiltered'] = intval($total_records);
            if(array_key_exists("limit",$conditions)){
                $total_pages = ceil($total_records / $limit);
                $data['total_pages'] = intval(ceil($total_records / $limit));
            }
        $data['sql_query'] = $sql;
        
        //return !empty($data['data'])?$data:false;
        if(empty($data['data'])){
            $data['data'] = [];
            $data['gotData'] = false;

        }
        return $data;
    }
}