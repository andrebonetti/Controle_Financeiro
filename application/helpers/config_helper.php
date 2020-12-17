<?php

    function config_info(){

        date_default_timezone_set('America/Sao_Paulo');

        $info["servidor"] = "LOCAL";
        $info["showTemplate"] = true;
        $info["rollback"] = false;
        $info["retorno"] = true;

        return $info;
    }

    function config_base($pConfig = null){

        $ci = get_instance();

        $info = config_info();

        if($info["servidor"] == "LOCAL"){

            $ci->output->enable_profiler(TRUE);

        }
        if($info["servidor"] == "PRODUCAO"){

            $ci->output->enable_profiler(FALSE);

        }

        if(isset($pConfig["showTemplate"])){$info["showTemplate"] = $pConfig["showTemplate"];}
        if(isset($pConfig["rollback"])){$info["rollback"] = $pConfig["rollback"];} 
        if(isset($pConfig["retorno"])){$info["retorno"] = $pConfig["retorno"];} 

        return $info;
	}   
    
    function config_finalTransaction($pInfo){

        $ci = get_instance();
	   
        if($pInfo["rollback"]){
            $ci->db->trans_rollback();
        }
        else{
            $ci->db->trans_commit();
        }
        
	}   
