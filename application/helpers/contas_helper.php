<?php

    function contas_BuscarContasCompleto($pParamBusca,$pCompetenciaAtual){

        $ci = get_instance();
        $pParamBusca["HasInnerJoin"]      = true;
        //util_print($pParamBusca,'$pParamBuscaContas');
        $lcontaUsuario  = $ci->contas_model->Listar($pParamBusca);  
        $lcontaUsuario  = contas_saldo_GerarSaldoMes($pParamBusca,$lcontaUsuario,$pCompetenciaAtual);
    
        //LEGADO (ANTES MULTIPLAS CONTAS) 
        if($pParamBusca["Mes"] <= 2 && $pParamBusca["Ano"] <= 2018){
            unset($pParamBusca["Ano"]);
            unset($pParamBusca["Mes"]);
            unset($pParamBusca["HasInnerJoin"]);
            $pParamBusca["Id"]                          = 1;
            $lcontaUsuario["Contas_Banco"][1]           = $ci->contas_model->Buscar($pParamBusca);
            $lcontaUsuario["Contas_Banco"][1]["Saldo"]  = $lcontaUsuario["Geral"]["Saldo"];
        }else{

            #GERAR COMPETENCIA
            //util_print($lcontaUsuario,"VERIFICA NULL");
            if(util_isNull($lcontaUsuario,"Contas_Banco")){

                //util_print("GERA CONTA SALDO");

                $pParamContas["Usuario"]        = $pParamBusca["Usuario"];
                $pParamContas                   = util_AlterarMes($pParamBusca,-1,true);
                $pParamContas["HasInnerJoin"]   = true;

                $lcontaUsuarioAnterior = $ci->contas_model->Listar($pParamContas);

                if(util_isNotNull($lcontaUsuarioAnterior,"Contas_Banco")){

                    foreach ($lcontaUsuarioAnterior["Contas_Banco"] as $keyConta => $iTemConta) {
                       $saldo   = $iTemConta["Saldo"];
                       $iSaldo  = $saldo;

                       $iSaldo["SaldoMes"]      = 0;;
                       $iSaldo["SaldoFinal"]    = 0;
                       $iSaldo["Receita"]       = 0;
                       $iSaldo["Despesas"]      = 0;
                       $iSaldo["Ano"]           = $pParamBusca["Ano"];
                       $iSaldo["Mes"]           = $pParamBusca["Mes"];

                       $iSaldo["Id"]            =  $ci->contas_saldo_model->Incluir($iSaldo);

                       $lcontaUsuario["Contas_Banco"][$iSaldo["IdConta"]]                           = $iTemConta;
                       $lcontaUsuario["Contas_Banco"][$iSaldo["IdConta"]]["Saldo"]                  = $iSaldo;
                       $lcontaUsuario["Contas_Banco"][$iSaldo["IdConta"]]["Saldo"]["SaldoAnterior"] = $saldo["SaldoFinal"];
                    }

                }

            }

        }

        return $lcontaUsuario;  
    }