<?php

    function contas_BuscarContasCompleto($pParamBusca,$pCompetenciaAtual){

        $ci = get_instance();
        $pParamBusca["HasInnerJoin"]      = true;

        $lcontaUsuario  = $ci->contas_model->Listar($pParamBusca);  
        $lcontaUsuario  = contas_saldo_GerarSaldoMes($pParamBusca,$lcontaUsuario,$pCompetenciaAtual);
          
        return $lcontaUsuario;  
    }