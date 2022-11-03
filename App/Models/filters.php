<?php

function cep_digits($cep)
{
    return str_replace("-", "", $cep);
}

function cfop($cfop)
{
    /*
    Tira o ponto do cfop
    */
    return str_replace(".", "", $cfop);
}

function nullableValueNotAccepted($val)
{
    /*
    Retorna -1 ao invés de null/none/!!
    */
    return ($val == "" || $val == NULL) ? -1 : $val;
}

function blankNotAccepted($val)
{
    /*
    Retorna null ao invés de ""
    */
    return $val == "" ? NULL : $val;
}

function zeroNullValue($val)
{
    return ($val == "" || $val == NULL) ? 0 : $val;;
}

function nullifyIfInvalid($val)
{
    return ($val == "" || $val == NULL) ? NULL : $val;
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
