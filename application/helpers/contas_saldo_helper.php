<?php

    function contas_saldo_GerarSaldoMes($pParamBusca,$plcontaUsuario,$pCompetenciaAtual){

        $lSaldoMes["Total"]["SaldoAnterior"] = 0;
        $lSaldoMes["Total"]["SaldoMes"] = 0;  
        $lSaldoMes["Total"]["SaldoFinal"] = 0;     
        $num_ContasComSaldo = 0;             
        foreach($plcontaUsuario as $itemConta){
            if(count($itemConta["Saldo"]) > 0){
                $lSaldoMes[$itemConta["Id"]]         =  $itemConta["Saldo"];
                $lSaldoMes["Total"]["SaldoAnterior"] += $itemConta["Saldo"]["SaldoAnterior"];
                $lSaldoMes["Total"]["SaldoMes"]      += $itemConta["Saldo"]["SaldoMes"];
                $lSaldoMes["Total"]["SaldoFinal"]    += $itemConta["Saldo"]["SaldoFinal"];
                $num_ContasComSaldo++;
            }
        } 
        if($num_ContasComSaldo < 1){
            $competenciaAnterior = geral_BuscarCompetenciaAnterior($pParamBusca);

            $lSaldoMes["Total"]["SaldoAnterior"] = $competenciaAnterior["SaldoFinal"];
            $lSaldoMes["Total"]["SaldoMes"] = $pCompetenciaAtual["SaldoMes"];  
            $lSaldoMes["Total"]["SaldoFinal"] = $pCompetenciaAtual["SaldoFinal"];  
        }

        return $lSaldoMes;
    }