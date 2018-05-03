<?php

    function contas_saldo_GerarSaldoMes($pParamBusca,$plcontaUsuario,$pCompetenciaAtual){

        $plcontaUsuario["Geral"]                                = $pCompetenciaAtual;
        $plcontaUsuario["Geral"]["Saldo"]["SaldoAnterior"]      = 0;
        $plcontaUsuario["Geral"]["Saldo"]["SaldoMes"]           = 0;  
        $plcontaUsuario["Geral"]["Saldo"]["SaldoFinal"]         = 0; 

        foreach($plcontaUsuario["Contas_Banco"] as $keyConta => $itemConta){

            $pCompetenciaAtual["IdConta"] = $keyConta;
            $competenciaAnterior_Conta = contas_saldo_BuscarCompetenciaAnterior($itemConta);

            $plcontaUsuario["Contas_Banco"][$keyConta]["Saldo"]                  =  $itemConta["Saldo"];
            $plcontaUsuario["Contas_Banco"][$keyConta]["Saldo"]["SaldoAnterior"] = $competenciaAnterior_Conta["SaldoFinal"];
            
            $plcontaUsuario["Geral"]["Saldo"]["SaldoAnterior"]  += $competenciaAnterior_Conta["SaldoFinal"];
            $plcontaUsuario["Geral"]["Saldo"]["SaldoMes"]       += $itemConta["Saldo"]["SaldoMes"];
            $plcontaUsuario["Geral"]["Saldo"]["SaldoFinal"]     += $itemConta["Saldo"]["SaldoFinal"];
        } 

        if(count($plcontaUsuario["Contas_Banco"]) < 1){
            $competenciaAnterior_Geral = geral_BuscarCompetenciaAnterior($pParamBusca);

            $plcontaUsuario["Geral"]["Saldo"]["SaldoAnterior"]  = $competenciaAnterior_Geral["SaldoFinal"];
            $plcontaUsuario["Geral"]["Saldo"]["SaldoMes"]       = $pCompetenciaAtual["SaldoMes"];  
            $plcontaUsuario["Geral"]["Saldo"]["SaldoFinal"]     = $pCompetenciaAtual["SaldoFinal"];  
        }

        return $plcontaUsuario;
    }

    function contas_saldo_criarContaSaldoMes($pParamBusca,$pConta){

        $ci = get_instance();
        $pParamBusca = util_AlterarMes($pParamBusca,-1,true);

        $paramContaSaldo            = $pParamBusca;
        $paramContaSaldo["IdConta"] = $pConta["Id"];

        $dContaSaldo =  $ci->contas_saldo_model->Buscar($paramContaSaldo);

        if(count($pConta)){

            $novaData = util_AlterarMes($pParamBusca,+1);

            $sContaSaldo["IdConta"]         = $dContaSaldo["IdConta"];
            $sContaSaldo["Ano"]             = $novaData["Ano"];
            $sContaSaldo["Mes"]             = $novaData["Mes"];
            $sContaSaldo["SaldoAnterior"]   = $dContaSaldo["SaldoFinal"];
            $sContaSaldo["SaldoMes"]        = 0;
            $sContaSaldo["SaldoFinal"]      = 0;

            $ci->contas_saldo_model->Incluir($sContaSaldo);

            return $sContaSaldo;
        }
        else{
            return null;
        }

        
    
    }

    function contas_saldo_validarConsistencia($plcontaUsuario,$pSaldUltimoDia){

        foreach($plcontaUsuario["Contas_Banco"] as $keyConta => $itemConta){
            
            $saldoFinalBanco    = $itemConta["Saldo"]["SaldoFinal"];
            $saldoFinalTela     = $pSaldUltimoDia["Contas_Banco"][$keyConta]["SaldoFinal"];

            $despesasFinalBanco = $itemConta["Saldo"]["Despesas"];
            $despesasFinalTela  = $pSaldUltimoDia["Contas_Banco"][$keyConta]["Despesas"];

            $receitaFinalBanco  = $itemConta["Saldo"]["Receita"];
            $receitaFinalTela   = $pSaldUltimoDia["Contas_Banco"][$keyConta]["Receita"];

            if( (util_diferenca($despesasFinalBanco,$despesasFinalTela,true))
            ||  (util_diferenca($receitaFinalBanco,$receitaFinalTela,true))
            ){
                echo "Despesas Final Diferente: ".$keyConta."<br>";
                echo "<b>Despesas Final: </b> ".$despesasFinalBanco." != ".$despesasFinalTela."<br>"; 

                echo "Receita Final Diferente: ".$keyConta."<br>";
                echo "<b>Receita Final: </b> ".$receitaFinalBanco." != ".$receitaFinalTela."<br>"; 

            }

            if(util_diferenca($saldoFinalBanco,$saldoFinalTela,true)){
                
                echo "Saldo Conta Final Diferente: ".$keyConta."<br>";
                echo "<b>Conta Final: </b> ".$saldoFinalBanco." != ".$saldoFinalTela."<br>"; 

                $dData["Ano"]               = $itemConta["Saldo"]["Ano"];
                $dData["Mes"]               = $itemConta["Saldo"]["Mes"];
                $dData["IdConta"]           = $keyConta;
                $dData["Valor"]             = util_diferenca($saldoFinalTela,$saldoFinalBanco);
                $dData["IdTipoTransacao"]   = 3;
                $dData["IsVerificacao"]     = true;

                contas_saldo_UpdateSaldo($dData);

                $plcontaUsuario["Contas_Banco"][$keyConta]["Saldo"]["SaldoFinal"]   = $saldoFinalTela;
                $plcontaUsuario["Contas_Banco"][$keyConta]["Saldo"]["SaldoMes"]     = $plcontaUsuario["Contas_Banco"][$keyConta]["Saldo"]["SaldoFinal"] - $itemConta["Saldo"]["SaldoAnterior"];
            }
            else{

                $saldoMesBanco    = $itemConta["Saldo"]["SaldoMes"];
                $saldoMesTela     = $pSaldUltimoDia["Contas_Banco"][$keyConta]["SaldoFinal"] - $itemConta["Saldo"]["SaldoAnterior"];

                if(util_diferenca($saldoMesBanco,$saldoMesTela,true)){

                    echo "Saldo Conta Mes Diferente: ".$keyConta."<br>";
                    echo "<b>Conta Mes:</b> ".$saldoMesBanco." = ".$saldoMesTela."<br>";

                    $dData["Ano"]               = $itemConta["Saldo"]["Ano"];
                    $dData["Mes"]               = $itemConta["Saldo"]["Mes"];
                    $dData["IdConta"]           = $keyConta;
                    $dData["IsVerificacao"]     = true;
                    $dData["Valor"]             = util_diferenca($saldoMesTela,$saldoMesBanco);

                    contas_saldo_UpdateSaldoMes($dData);
                    $plcontaUsuario["Contas_Banco"][$keyConta]["Saldo"]["SaldoMes"] = $saldoMesTela;
                }
            }
        }

         $saldoFinalGeralBanco    = $plcontaUsuario["Geral"]["SaldoFinal"];
         $saldoFinalGeralTela     = $pSaldUltimoDia["Geral"]["SaldoFinal"];

        if(util_diferenca($saldoFinalGeralBanco,$saldoFinalGeralTela,true)){

            echo "Saldo Geral Final Diferente: ".$keyConta."<br>";
            echo "<b>Geral Final: </b> ".$saldoFinalGeralBanco." != ".$saldoFinalGeralTela."<br>";   

            $dData["Ano"]               = $plcontaUsuario["Geral"]["Ano"];
            $dData["Mes"]               = $plcontaUsuario["Geral"]["Mes"];
            $dData["Valor"]             = util_diferenca($saldoFinalGeralTela,$saldoFinalGeralBanco);
            $dData["IdTipoTransacao"]   = 3;
            $dData["IsVerificacao"]     = true;          
            geral_UpdateSaldo($dData);

            $plcontaUsuario["Geral"]["Saldo"]["SaldoFinal"]   = $pSaldUltimoDia["Geral"]["SaldoFinal"];
            $plcontaUsuario["Geral"]["Saldo"]["SaldoMes"]     = $pSaldUltimoDia["Geral"]["SaldoFinal"] - $plcontaUsuario["Geral"]["Saldo"]["SaldoAnterior"];
        }
        else{

            $saldoMesGeralBanco    = floatval($plcontaUsuario["Geral"]["SaldoMes"]);
            $saldoMesGeralTela     = floatval($pSaldUltimoDia["Geral"]["SaldoFinal"] - $plcontaUsuario["Geral"]["Saldo"]["SaldoAnterior"]);

            if(util_diferenca($saldoMesGeralBanco,$saldoMesGeralTela,true)){

                echo "Saldo Geral Mes Diferente: ".$keyConta."<br>";    
                echo "<b>Geral Final: </b> ".$saldoMesGeralBanco." != ".$saldoMesGeralTela."<br>";              
                
                $dData["Ano"]               = $itemConta["Saldo"]["Ano"];
                $dData["Mes"]               = $itemConta["Saldo"]["Mes"];
                $dData["IsVerificacao"]     = true;
                $dData["Valor"]             = util_diferenca($saldoMesGeralTela,$saldoMesGeralBanco);

                geral_UpdateSaldoMes($dData);

                $plcontaUsuario["Geral"]["Saldo"]["SaldoMes"] = $saldoMesGeralTela;
            }
        }

        return $plcontaUsuario;
    }

    function contas_saldo_UpdateSaldo($pData){
        
        $ci = get_instance();

        if($pData["IdTipoTransacao"] == 1){
            $pData["PeriodoDe"] = true;
        }
        if($pData["IdTipoTransacao"] == 2){
            $pData["PeriodoAte"] = true;
        }
        unset($pData["Id"]);

        $lSaldo = $ci->contas_saldo_model->Listar($pData);

        $cont = 1;
        foreach($lSaldo as $itemSaldo){		                    

            $paramMes["Mes"]        = $itemSaldo["Mes"]; 
            $paramMes["Ano"]        = $itemSaldo["Ano"];
            $paramMes["IdConta"]    = $pData["IdConta"];
            $paramMes["Valor"]      = $pData["Valor"];

            if((($cont == 1)or($pData["IdTipoTransacao"] == 1))&&(!isset($pData["IsVerificacao"]))){
                contas_saldo_UpdateSaldoMes($paramMes);
            }
            
            $paramGeral["Valor"]         = $pData["Valor"];
            // if($pData["IdTipoTransacao"] == 1){
            //     $paramGeral["Valor"] = $pData["Valor"] * $cont;
            // }

            $paramGeral["Mes"]           = $itemSaldo["Mes"]; 
            $paramGeral["Ano"]           = $itemSaldo["Ano"];
            $paramGeral["IdConta"]       = $itemSaldo["IdConta"];
            $paramGeral["PeriodoDe"]     = true;

            if(isset($pData["IsVerificacao"])){
                $paramGeral["IsVerificacao"] = $pData["IsVerificacao"];
            }

            contas_saldo_UpdateSaldoFinal($paramGeral);
            $cont++;
        }
    }

    function contas_saldo_UpdateSaldoMes($pData){
        
		$ci = get_instance();
        
		// ------ MES ------	
        $dataContaSaldoMes         = $ci->contas_saldo_model->Buscar($pData);		

        // ------ SOMA -------
        $dataContaSaldoMes["SaldoMes"]  += $pData["Valor"];

        // -- BD UPDATE --
        $ci->contas_saldo_model->Atualizar($dataContaSaldoMes);
	}

    function contas_saldo_UpdateSaldoFinal($pParamGeral){
        
		$ci = get_instance();

        // ---- TOTAL SALDO ----		
        $lsaldoConta = $ci->contas_saldo_model->Listar($pParamGeral);
		foreach($lsaldoConta as $itemSaldoConta){	
            
            $dataSaldoFinal               = $itemSaldoConta;
			$dataSaldoFinal["SaldoFinal"] += $pParamGeral["Valor"];

            // if(isset($pParamGeral["IsVerificacao"])){
            //     $dataSaldoFinal["SaldoMes"] += $dataSaldoFinal["SaldoFinal"] - $dataSaldoFinal["SaldoAnterior"];
            // }

            // -- BD UPDATE --
            $ci->contas_saldo_model->Atualizar($dataSaldoFinal);	
		}
                
	}

    function contas_saldo_transferirValores($pData){

        $ci = get_instance();

        $param["Ano"]               = $pData["Ano"];
        $param["Mes"]               = $pData["Mes"];
        $param["IdTipoTransacao"]   = $pData["IdTipoTransacao"];

        $param["IdConta"] = $pData["origem"];
        $param["Valor"]   = $pData["Valor"]*(-1);

        contas_saldo_UpdateSaldo($param);

        $param["IdConta"] = $pData["destino"];
        $param["Valor"]   = $pData["Valor"];

        contas_saldo_UpdateSaldo($param);
    }

    function contas_saldo_BuscarCompetenciaAnterior($pContaSaldoMes){

        $ci = get_instance();

        $param["Ano"] = $pContaSaldoMes["Saldo"]["Ano"];
        $param["Mes"] = $pContaSaldoMes["Saldo"]["Mes"];

        $param = util_AlterarMes($param,-1,true);

        $param["IdConta"] = $pContaSaldoMes["Id"];

        $saldoAnterior = $ci->contas_saldo_model->Buscar($param);

        if(count($saldoAnterior) < 1){         
             $saldoAnterior["SaldoFinal"] = $pContaSaldoMes["ValorInicio"];
        }

        return $saldoAnterior;
        
    }

