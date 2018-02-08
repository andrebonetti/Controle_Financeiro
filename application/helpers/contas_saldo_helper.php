<?php

    function contas_saldo_GerarSaldoMes($pParamBusca,$plcontaUsuario,$pCompetenciaAtual){

        $plcontaUsuario["Geral"]["Saldo"]["SaldoAnterior"]      = 0;
        $plcontaUsuario["Geral"]["Saldo"]["SaldoMes"]           = 0;  
        $plcontaUsuario["Geral"]["Saldo"]["SaldoFinal"]         = 0;     

        foreach($plcontaUsuario["Contas_Banco"] as $keyConta => $itemConta){
            $plcontaUsuario[$keyConta]["Saldo"]                 = $itemConta["Saldo"];
            $plcontaUsuario["Geral"]["Saldo"]["SaldoAnterior"]  += $itemConta["Saldo"]["SaldoAnterior"];
            $plcontaUsuario["Geral"]["Saldo"]["SaldoMes"]       += $itemConta["Saldo"]["SaldoMes"];
            $plcontaUsuario["Geral"]["Saldo"]["SaldoFinal"]     += $itemConta["Saldo"]["SaldoFinal"];
        } 

        if(count($plcontaUsuario["Contas_Banco"]) < 1){
            $competenciaAnterior = geral_BuscarCompetenciaAnterior($pParamBusca);

            $plcontaUsuario["Geral"]["Saldo"]["SaldoAnterior"]  = $competenciaAnterior["SaldoFinal"];
            $plcontaUsuario["Geral"]["Saldo"]["SaldoMes"]       = $pCompetenciaAtual["SaldoMes"];  
            $plcontaUsuario["Geral"]["Saldo"]["SaldoFinal"]     = $pCompetenciaAtual["SaldoFinal"];  
        }


        return $plcontaUsuario;
    }