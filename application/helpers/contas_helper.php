<?php

    function contas_BuscarContasCompleto($pParamBusca,$pCompetenciaAtual){

        $ci = get_instance();
        $pParamBusca["HasInnerJoin"]      = true;

        $lcontaUsuario  = $ci->contas_model->Listar($pParamBusca);  
        $lcontaUsuario  = contas_saldo_GerarSaldoMes($pParamBusca,$lcontaUsuario,$pCompetenciaAtual);
          
        //LEGADO (ANTES MULTIPLAS CONTAS) 
        if(empty($lcontaUsuario["Contas_Banco"])){
            util_print($pParamBusca,"ParamBusca");
            unset($pParamBusca["Ano"]);
            unset($pParamBusca["Mes"]);
            unset($pParamBusca["HasInnerJoin"]);
            $pParamBusca["Id"]                          = 1;
            $lcontaUsuario["Contas_Banco"][1]           = $ci->contas_model->Buscar($pParamBusca);
            $lcontaUsuario["Contas_Banco"][1]["Saldo"]  = $lcontaUsuario["Geral"]["Saldo"];
        }

        return $lcontaUsuario;  
    }