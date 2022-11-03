<?php 
namespace App\Models\Cadastro;

class PessoaJuridica extends Cadastro{

    private $cnpj;
    private $nome_fantasia;
    private $razao_social;
/*  
    public function setCnpj($cnpj){
         $this->cnpj = $cnpj;
    }
    public function getCnpj(){
        return $this->cnpj;
    }

    public function setNomeFantasia($nome_fantasia){
        $this->nome_fantasia = $nome_fantasia;
    }

    public function getNomeFantasia(){
       return $this->nome_fantasia;
    }

    public function setRazaoSocial($razao_social){
        $this->razao_social = $razao_social;
    }

    public function getRazaoSocial(){
       return $this->razao_social;
    }*/

    //Validação e Filtagem de Dados
    public function isRazaoSocialValid($razao_social)
    {   
        $valid = false;

        if (!empty(trim($razao_social))) {
            $valid = true;
        }

        return $valid;
    }

    public function isNomeFantasiaValid($nome_fantasia)
    {   
        $valid = false;

        if (!empty(trim($nome_fantasia))) {
            $valid = true;
        }

        return $valid;
    }

    public function isCNPJValidFilter($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
        
        // Valida tamanho
        if (strlen($cnpj) != 14)
            return false;

        // Verifica se todos os digitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj))
            return false;	

        // Valida primeiro dígito verificador
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
            return false;

        // Valida segundo dígito verificador
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        $response['valid'] = $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
        $response['cnpj'] = $cnpj;
        return $response;
    }
}
