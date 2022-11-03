<?php 
namespace App\Models\Cadastro;

 abstract class Cadastro extends \App\Models\CrudInit {

    //Endereço
    private $endereco;
    private $endereco_n;
    private $endereco_complemento;
    private $endereco_bairro;
    private $cidade;
    private $uf;
    private $cep;

    //Dados de Contato
    private $telefone;
    private $celular;
    private $email;

    //Validação e Filtagem de Dados
    public function isEnderecoValid($endereco): bool
    {   
        $valid = false;

        if (!empty(trim($endereco))) {
            $valid = true;
        }

        return $valid;
    }

    public function isEnderecoNumeroValid($endereco_n): bool
    {   
        $valid = false;

        if (!empty(trim($endereco_n))) {
            $valid = true;
        }

        return $valid;
    }

    public function isEnderecoComplementoValid($endereco_complemento): bool
    {   
        $valid = false;

        if (!empty(trim($endereco_complemento))) {
            $valid = true;
        }

        return $valid;
    }

    public function isEnderecoBairroValid($endereco_bairro): bool
    {   
        $valid = false;

        if (!empty(trim($endereco_bairro))) {
            $valid = true;
        }

        return $valid;
    }


    public function isCidadeValid($cidade): bool
    {   
        $valid = false;

        if (!empty(trim($cidade))) {
            $valid = true;
        }

        return $valid;
    }

    public function isUFValid($uf): bool
    {   
        $valid = false;

        $estadosBrasileiros = array(
            'AC'=>'Acre',
            'AL'=>'Alagoas',
            'AP'=>'Amapá',
            'AM'=>'Amazonas',
            'BA'=>'Bahia',
            'CE'=>'Ceará',
            'DF'=>'Distrito Federal',
            'ES'=>'Espírito Santo',
            'GO'=>'Goiás',
            'MA'=>'Maranhão',
            'MT'=>'Mato Grosso',
            'MS'=>'Mato Grosso do Sul',
            'MG'=>'Minas Gerais',
            'PA'=>'Pará',
            'PB'=>'Paraíba',
            'PR'=>'Paraná',
            'PE'=>'Pernambuco',
            'PI'=>'Piauí',
            'RJ'=>'Rio de Janeiro',
            'RN'=>'Rio Grande do Norte',
            'RS'=>'Rio Grande do Sul',
            'RO'=>'Rondônia',
            'RR'=>'Roraima',
            'SC'=>'Santa Catarina',
            'SP'=>'São Paulo',
            'SE'=>'Sergipe',
            'TO'=>'Tocantins'
            );

        if (array_key_exists($uf, $estadosBrasileiros)) {
            $valid = true;
        }

        return $valid;
    }

    public function isCepValid($cep): bool
    {   

        $cep = preg_replace('/[^0-9]/', '', (string) $cep);
        $valid = false;

        if (!empty(trim($cep))) {
            $valid = true;
        }

        return $valid;
    }
    public function isCEPValidFilter($cep): array
    {   
       
        $cep = trim(preg_replace('/[^0-9]/', '', (string) $cep));
        $response['valid'] = false;

        if (!empty($cep)){
            $response['valid'] = true;
        }

        $response['cep'] = $cep;
        return $response;
    }


    public function isTelefoneValidFilter($telefone): array
    {   
       
        $telefone = trim(preg_replace('/[^0-9]/', '', (string) $telefone));
        $response['valid'] = false;

        if (!empty($telefone)){
            $response['valid'] = true;
        }

        $response['telefone'] = $telefone;
        return $response;
    }

    public function isCelularValidFilter($celular): array
    {   
        $celular = trim(preg_replace('/[^0-9]/', '', (string) $celular));
        $response['valid'] = false;

        if (!empty($celular)){
            $response['valid'] = true;
        }

        $response['celular'] = $celular;
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
}
