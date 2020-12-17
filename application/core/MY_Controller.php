<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class My_Controller extends CI_Controller {
 
    function __construct()
    {
        parent::__construct();
   
        #PHP
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
        ini_set('max_execution_time', 3000);

        #AMBIENTE
        $this->G_Config["Ambiente"]             = $_SERVER['CI_ENV'];

        #SERVIDOR
        if(util_in($this->G_Config["Ambiente"],array("producao","implantacao"))){$servidor = "PRODUCAO";}
        else{$servidor = "LOCALHOST";}    

        $this->G_Config["Servidor"]             = $servidor;
        $this->G_Config["CaminhoLocal"]         = "";
        $this->G_Config["BaseUrl"]              = base_url();

        if($servidor == "LOCALHOST"){
            $this->G_Config["BaseServidor"]     = $this->G_Config["BaseUrl"];
            $this->G_Config["CaminhoLocal"]     = str_replace("index.php","",$_SERVER["SCRIPT_FILENAME"]);
        }

        #DADOS USER
        if(isset($_SERVER["HTTP_USER_AGENT"])){
            $this->G_Config["USER_AGENT"]       = $_SERVER["HTTP_USER_AGENT"];
        }else{
            $this->G_Config["USER_AGENT"]       = "Node";
        }
        $this->benchmark->mark('my_mark_start');

        #PAGINA
        $this->G_Config["Prefixo_URL"]          = "index.php/";
        $this->G_Config["Base_Controller"]      = $this->G_Config["BaseUrl"].$this->G_Config["Prefixo_URL"];
        $this->G_Config["Controller"]           = $this->router->fetch_class();
        $this->G_Config["Method"]               = $this->router->fetch_method();
    }
}
 
class API_Controller extends My_Controller {
 
    function __construct()
    {
        parent::__construct();

        $this->output->enable_profiler(FALSE);

        $config    = config_base(array("rollback" => false,"retorno" => true));//array("rollback" => true,"retorno" => false));      
        $usuario   = valida_acessoUsuario();
    }
}
