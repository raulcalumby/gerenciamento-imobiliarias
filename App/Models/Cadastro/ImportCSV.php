<?php
namespace Cadastro; 

class ImportCSV{

    //Transformar isso aqui em classe 
    public function uploadToServer($file_name, $file_tmp_name, $file_type, $file_size){
        $target_path = "../../../public/uploads/csv/" . md5(uniqid(rand(), true)) . '_' . $file_name;
        $status = [];
        $status['file_path'] = $target_path;
        $status['name'] = $file_name;
        $status['mime'] = $file_type;
        $status['size'] = $file_size;
        $status['date'] = date('Y-m-d H:i:s');
        $status['md5'] = md5_file($file_tmp_name);
        if(move_uploaded_file($file_tmp_name, $target_path)){
            $status['status_upload'] = 'success';
        }else{
            $status['status_upload'] = 'error';
        }
        return $status;
    }
    public function FuncPlaceHolder($FILES){
        if( isset($_POST['id_admin'])){
            if(isset($_FILES)){
                for ($i=0; $i < sizeof($_FILES['upload']['name']); $i++) { 

                    $upload = uploadToServer($_FILES['upload']['name'][$i] , $_FILES['upload']['tmp_name'][$i] , $_FILES['upload']['type'][$i] , $_FILES['upload']['size'][$i]);
                    if($upload['status_upload'] == 'success'){
                        //echo json_encode($upload);

                        $status_final[$i] =  $upload;
                    // $status_final[$i] = insertCSV($upload["file_path"], $upload["date"], intval($_POST['id_admin']), $_POST['id_user'], $upload["name"], $upload["mime"], $upload["size"], $upload["md5"]);
                    }else{
                        $status_final[$i]['status'] = 'error';
                        $status_final[$i]['text_status'] = 'Não foi possivel realizar o upload do arquivo no servidor local, caso o erro persista entre em contato o desenvolvedor';
                        $status_final[$i]['file_name'] = $_FILES['upload']['name'][$i];
                    } 
                }
            }else{
                $status_final[]['status'] = 'error';
                $status_final[]['text_status'] = 'Por favor selecione os arquivos antes de enviar, caso o erro persista entre em contato o desenvolvedor';
            }
        }else{
            $status_final[]['status'] = 'error';
            $status_final[]['text_status'] = 'Por favor certifique que esta logado e se o formando foi selecionado, caso o erro persista entre em contato o desenvolvedor';
        }   
    }   
}   