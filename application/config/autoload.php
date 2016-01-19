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

$autoload['helper'] = array('url', 'form', 'text', 'date','active','filter','transform_name','adm_helper',"crud_helper","data_helper");

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

$autoload['model'] = array('crud_model','transacoes_model','usuarios_model','cartao_model',"geral_model");