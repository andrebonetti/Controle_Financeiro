<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
|  Auto-load Packges
| -------------------------------------------------------------------
*/

$autoload['packages'] = array();

/*
| -------------------------------------------------------------------
|  Auto-load Libraries
| -------------------------------------------------------------------
*/

$autoload['libraries'] = array('database', 'session','user_agent');

/*
| -------------------------------------------------------------------
|  Auto-load Helper Files
| -------------------------------------------------------------------
*/

$autoload['helper'] = array(
     'url'
    ,'form'
    ,'text'
    ,'date'
    ,'active'
    ,'filter'
    
    ,'transform_name'
    ,"crud_helper"
    ,"data_helper"
    ,"subCategorias_helper"
    ,"transacoes_helper"
    ,"geral_helper"
    ,"usuario_helper"
    ,"config_helper"
);

/*
| -------------------------------------------------------------------
|  Auto-load Config files
| -------------------------------------------------------------------
*/

$autoload['config'] = array();

/*
| -------------------------------------------------------------------
|  Auto-load Language files
| -------------------------------------------------------------------
*/

$autoload['language'] = array();

/*
| -------------------------------------------------------------------
|  Auto-load Models
| -------------------------------------------------------------------
*/

$autoload['model'] = array(
    'transacoes_model'
    ,'usuarios_model'
    ,'cartao_de_credito_model'
    ,'cartoes_model'
    ,"geral_model"
    ,"categoria_model"
    ,"subCategoria_model"
    ,"contas_model"
    ,"contas_saldo_model"
    ,"bancos_model"
);