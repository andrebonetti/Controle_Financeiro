<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
*/

$active_group = 'default';
$active_record = TRUE;

$db['default']['hostname'] = 'localhost';
$db['default']['username'] = 'root';
$db['default']['password'] = '';
$db['default']['database'] = 'controle-financeiro';
$db['default']['dbdriver'] = 'mysql';

/*$db['default']['hostname'] = 'mysql04.andreteste.hospedagemdesites.ws';
$db['default']['username'] = 'andreteste';
$db['default']['password'] = 'flatronW1943C';
$db['default']['database'] = 'andreteste';
$db['default']['dbdriver'] = 'mysql';*/

/*$db['default']['hostname'] = '191.232.242.255';
$db['default']['username'] = 'gerensys';
$db['default']['password'] = 'gerensys1366';
$db['default']['database'] = 'controle_financeiro';
$db['default']['dbdriver'] = 'sqlsrv';*/

$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;