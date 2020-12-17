<?php

    function geral_UpdateSaldo($pData,$pTipo = 0){
        
        //echo "-------------------------geral_UpdateSaldo----------------------------------<br>";

        $ci = get_instance();

        if($pData["IdTipoTransacao"] == 1){
            $pData["PeriodoDe"] = true;
        }
        if($pData["IdTipoTransacao"] == 2){
            //$pData["PeriodoAte"] = true;
        }

        $pData["Usuario"]["Id"] = $pData["IdUsuario"];
        
        unset($pData["Id"]);

        $lGeral = $ci->geral_model->Listar($pData);

        ////util_printR($lGeral,"lGeral");

        $cont = 1;
        foreach($lGeral as $itemGeral){		                    

            $paramMes                   = array();
            $paramMes["Mes"]            = $itemGeral["Mes"]; 
            $paramMes["Ano"]            = $itemGeral["Ano"];
            $paramMes["Valor"]          = $pData["Valor"];
            $paramMes["Usuario"]["Id"]  = $pData["IdUsuario"];

            if((($cont == 1)or($pData["IdTipoTransacao"] == 1))&&(!isset($pData["IsVerificacao"]))){
                geral_UpdateSaldoMes($paramMes);
            }
            
            $paramGeral["Valor"]         = $pData["Valor"];
            if($pData["IdTipoTransacao"] == 1){
                $paramGeral["Valor"] = $pData["Valor"] * $cont;
            }

            $paramGeral["Mes"]           = $itemGeral["Mes"]; 
            $paramGeral["Ano"]           = $itemGeral["Ano"];
            $paramGeral["Usuario"]["Id"] = $pData["IdUsuario"];
            $paramGeral["PeriodoDe"]     = true;

            if(isset($pData["IsVerificacao"])){
                $paramGeral["IsVerificacao"] = $pData["IsVerificacao"];
            }

            geral_UpdateSaldoFinal($paramGeral,$pTipo);
            $cont++;
        }
    }

    function geral_UpdateSaldoManual($pData){

        $ci = get_instance();

        $pData["PeriodoDe"] = true;
        $pData["Id"] = null;
        
        $lGeral = $ci->geral_model->Listar($pData);
        
        $saldoAnterior = 0;
        $cont = 0;
        foreach($lGeral as $itemGeral){		                    

            $dataGeral["Mes"]   = $itemGeral["Mes"]; 
            $dataGeral["Ano"]   = $itemGeral["Ano"];
            $dataGeral["Id"]    = $itemGeral["Id"];
            
            if($cont > 0){
                
                $dataGeral["SaldoFinal"] = $saldoAnterior + $itemGeral["SaldoMes"];

                //echo $dataGeral["SaldoFinal"];
                $ci->geral_model->Atualizar($dataGeral);
            }
            
            $saldoAnterior = $itemGeral["SaldoFinal"];
            $cont++;
        }
    }

    function geral_UpdateSaldoMes($pData){
        
		$ci = get_instance();
        
		// ------ MES ------	
        $dataGeralMes              = $ci->geral_model->Buscar($pData);

        // ------ SOMA -------
        $dataGeralMes["SaldoMes"]  += $pData["Valor"];
		
        // -- BD UPDATE --
		$ci->geral_model->Atualizar($dataGeralMes);
	}

    function geral_UpdateSaldoMesCartao($pData,$pTipo){
        
        $ci = get_instance();
        
		// ------ MES ------	
        
        $dataGeralMes              = $ci->geral_model->Buscar($pData);	

        if(!empty($dataGeralMes )){
        
            $dataGeralMes["Cartao"] += $pData["Valor"]*(-1);

            // -- BD UPDATE --
            $ci->geral_model->Atualizar($dataGeralMes);
        }
    }

    function geral_UpdateSaldoFinal($pParamGeral,$pTipo){
        
		$ci = get_instance();
            
        // ---- TOTAL GERAL ----		
        $lGeral = $ci->geral_model->Listar($pParamGeral);
		foreach($lGeral as $keyGeral => $itemGeral){	
            
            $dataGeralFinal                = $itemGeral;
			$dataGeralFinal["SaldoFinal"] += $pParamGeral["Valor"];

            if(isset($pParamGeral["IsVerificacao"])){
                if(isset($lGeral[$keyGeral-1]["SaldoFinal"])){
                    $dataGeralFinal["SaldoMes"] += $dataGeralFinal["SaldoFinal"] - $lGeral[$keyGeral-1]["SaldoFinal"];
                }
            }
            
            if($pTipo == 1){$dataGeralFinal["Receita"] += $pParamGeral["Valor"];}        
            if($pTipo == 2){$dataGeralFinal["Despesas"] += $pParamGeral["Valor"]*(-1);}

            // -- BD UPDATE --
            $ci->geral_model->Atualizar($dataGeralFinal);	
		}
    
	}

    function geral_UpdateCartaoMes($pData){
        
        $ci = get_instance();
        
        $dataBusca["Ano"] = $pData["Ano"];
        $dataBusca["Mes"] = $pData["Mes"];
        
		// ------ MES ------	
        $dataGeralMes           = $ci->geral_model->Buscar($dataBusca);	
        
		$dataGeralMes["Cartao"] += $pData["Valor"]*(-1);
        
        // -- BD UPDATE --
		$ci->geral_model->Atualizar($dataGeralMes);
    }

    function geral_CriarCompetencia($pDataContent){
        
        $ci = get_instance();      
        $paramGeral["Usuario"]  = $pDataContent["Usuario"];
        $lGeral                 = $ci->geral_model->Listar($paramGeral);
        
        $geral = $lGeral[count($lGeral) - 1];
        
        $ano = $geral["Ano"];
        $mes = $geral["Mes"];
    
        while(intval($ano.$mes) < intval($pDataContent["Ano"].$pDataContent["Mes"]))
        {     
            //util_print(intval($ano.$mes),intval($pDataContent["Ano"].$pDataContent["Mes"]));
      
            $referencia["Ano"] = $ano;
            $referencia["Mes"] = $mes;

            $dataGeral = util_AlterarMes($referencia,1,true);
            //$dataGeral = CalcularSaldo($dataGeral);        
            $dataGeral["IdUsuario"] = $pDataContent["Usuario"]["Id"];    
            $ci->geral_model->Incluir($dataGeral);
            
            $ano = $dataGeral["Ano"];
            $mes = str_pad($dataGeral["Mes"],2,"0",STR_PAD_LEFT);
        }
 
    }

    function CalcularSaldo($pDataGeral){
       
        $ci = get_instance();
        
        if($pDataGeral["Mes"] == 1){
            $dataMesAnterior["Ano"] = $pDataGeral["Ano"] - 1;
            $dataMesAnterior["Mes"] = 12;
        }
        else{
            $dataMesAnterior["Mes"] = $pDataGeral["Mes"] - 1;
            $dataMesAnterior["Ano"] = $pDataGeral["Ano"];
        }
        
        echo "Incluir -----> Ano: ".$pDataGeral["Ano"]."Mes: ".$pDataGeral["Mes"]."<br>";
        
        $saldoAnterior = $ci->geral_model->Buscar($dataMesAnterior);
        
        $pDataGeral["SaldoMes"] = 0;
        $pDataGeral["Receita"] = 0;
        $pDataGeral["Despesas"] = 0;
        $pDataGeral["Cartao"] = 0;
        $pDataGeral["SaldoFinal"] = $saldoAnterior["SaldoFinal"];

        $qtdeDiasMes = days_in_month($dataMesAnterior["Mes"]);
        
        $dataContent["Mes"]                      = $pDataGeral["Mes"];
        $dataContent["Ano"]                      = $pDataGeral["Ano"];  

        for ($n = 1; $n <= $qtdeDiasMes ;$n++){
            
            $dataContent["Dia"]      = $n;
            $dataContent["isListaPorTipo"] = true;
            
            // -- TRANSACOES SIMPLES --
            $dataContent["IdTipoTransacao"] = 3;
            $lDia_transacoesSimples = $ci->transacoes_model->Listar($dataContent);

            foreach($lDia_transacoesSimples as $itemContent){
                $pDataGeral["SaldoFinal"] += $itemContent["Valor"];
                $pDataGeral["SaldoMes"] += $itemContent["Valor"];
                
                echo $itemContent["Valor"]." -> ".$pDataGeral["SaldoFinal"]."<br>";
                
                if($itemContent["Valor"] < 0)
                {
                    $pDataGeral["Despesas"] += $itemContent["Valor"]*(-1);
                }
                else
                {
                    $pDataGeral["Receita"] += $itemContent["Valor"];
                }
            }
            
            // -- TRANSACOES PARCELADAS --
            $dataContent["IdTipoTransacao"]    = 2;
            $lDia_transacoesParcelada = $ci->transacoes_model->Listar($dataContent);

            foreach($lDia_transacoesParcelada as $itemContent){
                $pDataGeral["SaldoFinal"] += $itemContent["Valor"];
                $pDataGeral["SaldoMes"] += $itemContent["Valor"];
                
                if($itemContent["Valor"] < 0)
                {
                    $pDataGeral["Despesas"] += $itemContent["Valor"]*(-1);
                }
                else
                {
                    $pDataGeral["Receita"] += $itemContent["Valor"];
                }
            }
            
            // -- TRANSACOES RECORRENTES --
            $dataContent["IdTipoTransacao"]    = 1;
            $lDia_transacoesRecorrente = $ci->transacoes_model->Listar($dataContent);

            foreach($lDia_transacoesRecorrente as $itemContent){
                $pDataGeral["SaldoFinal"] += $itemContent["Valor"];
                $pDataGeral["SaldoMes"] += $itemContent["Valor"];
                            
                if($itemContent["Valor"] < 0)
                {
                    $pDataGeral["Despesas"] += $itemContent["Valor"]*(-1);
                }
                else
                {
                    $pDataGeral["Receita"] += $itemContent["Valor"];
                }
            }
            
        }
        
        /*-- CARTAO --*/
        $cartao_Recorrente       = $ci->cartao_de_credito_model->ListarFaturaRecorrente($dataContent);

        foreach($cartao_Recorrente as $itemContent){
            
            $pDataGeral["Cartao"] += $itemContent["Valor"]*(-1);
            $pDataGeral["SaldoMes"] += $itemContent["Valor"];
            $pDataGeral["SaldoFinal"] += $itemContent["Valor"];

            if($itemContent["Valor"] < 0)
            {
                $pDataGeral["Despesas"] += $itemContent["Valor"]*(-1);
            }
            else
            {
                $pDataGeral["Receita"] += $itemContent["Valor"];
            }
        }  

        $cartao_SimplesParcelado = $ci->cartao_model->ListarFaturaSimplesParcelada($dataContent);
        foreach($cartao_SimplesParcelado as $itemContent){
            
            $pDataGeral["Cartao"] += $itemContent["Valor"]*(-1);
            $pDataGeral["SaldoMes"] += $itemContent["Valor"];
            $pDataGeral["SaldoFinal"] += $itemContent["Valor"];

            if($itemContent["Valor"] < 0)
            {
                $pDataGeral["Despesas"] += $itemContent["Valor"]*(-1);
            }
            else
            {
                $pDataGeral["Receita"] += $itemContent["Valor"];
            }
            
        }
        
        return $pDataGeral;
    }

    function geral_BuscarCompetenciaAnterior($pParam){

        $ci = get_instance();

        if($pParam["Mes"] == 1){
            $competenciaAnterior["Mes"] = 12;
            $competenciaAnterior["Ano"] = $pParam["Ano"]-1;
        }
        else{
            $competenciaAnterior["Mes"] = $pParam["Mes"]-1;;
            $competenciaAnterior["Ano"] = $pParam["Ano"];
        }	

        return $ci->geral_model->Buscar($competenciaAnterior);

    }

    function geral_verificarConsistencia($competenciaAnterior,$competenciaAtual,$pTotalReceita,$pTotalDespesas,$saldoFinalDia){

        $ci = get_instance();

        $newDataCompetenciaAtual = $competenciaAtual;

        if( (($competenciaAnterior["SaldoFinal"] + $competenciaAtual["SaldoMes"]) != $competenciaAtual["SaldoFinal"]) || ( $competenciaAtual["SaldoFinal"] != (string) $saldoFinalDia) ){
            
            //echo $competenciaAnterior["SaldoFinal"] ."+". $competenciaAtual["SaldoMes"]."=".$competenciaAtual["SaldoFinal"]." / ".$competenciaAtual["SaldoFinal"]."!=".$saldoFinalDia.  " --- DIFERENTE";

            $newDataCompetenciaAtual["Ano"]         = $competenciaAtual["Ano"]; 
            $newDataCompetenciaAtual["Mes"]         = $competenciaAtual["Mes"];          
            $newDataCompetenciaAtual["SaldoFinal"]  = $saldoFinalDia;
            $newDataCompetenciaAtual["SaldoMes"]    = $saldoFinalDia - $competenciaAnterior["SaldoFinal"];

            if( ($competenciaAtual["Receita"] != (string)$pTotalReceita )||($competenciaAtual["Despesas"] != (string)$pTotalDespesas )  ){
                $newDataCompetenciaAtual["Receita"] = $pTotalReceita;
                $newDataCompetenciaAtual["Despesas"]= $pTotalDespesas;
            }

            $ci->geral_model->Atualizar_Manual($newDataCompetenciaAtual);

            $dataComptencia                         = calcularCompetencia($competenciaAtual["Ano"],$competenciaAtual["Mes"],1);     
            $data["Ano"]                            = $dataComptencia["Ano"];        
            $data["Mes"]                            = $dataComptencia["Mes"]; 

            $data["Valor"]                          = $newDataCompetenciaAtual["SaldoFinal"] - $competenciaAtual["SaldoFinal"];
            $data["IdTipoTransacao"]                = 3;

            geral_UpdateSaldo($data,1);

        }else{

            if( ($competenciaAtual["Receita"] != (string)$pTotalReceita )||($competenciaAtual["Despesas"] != (string)$pTotalDespesas )  ){

                $newDataCompetenciaAtual["Ano"]         = $competenciaAtual["Ano"]; 
                $newDataCompetenciaAtual["Mes"]         = $competenciaAtual["Mes"];  
                $newDataCompetenciaAtual["Receita"]     = $pTotalReceita;
                $newDataCompetenciaAtual["Despesas"]    = $pTotalDespesas;

                $ci->geral_model->Atualizar_Manual($newDataCompetenciaAtual);

            }

        }

        return $newDataCompetenciaAtual;
    }

    function geral_competenciaAtualTemplate($pParamBusca){//TEMP

        $ci = get_instance();

        $competenciaAtual   	         = $ci->geral_model->Buscar($pParamBusca);
        if(empty($competenciaAtual)){

            //util_print("CRIAÇÃO COMPETÊNCIA");

            geral_CriarCompetencia($pParamBusca);
            $competenciaAtual = $ci->geral_model->Buscar($pParamBusca);

            $pParamBusca            = util_AlterarMes($pParamBusca);
            $competenciaAnterior    = $ci->geral_model->Buscar($pParamBusca);

            if(count($competenciaAnterior)){
                $competenciaAtual["SaldoAnterior"] = $competenciaAnterior["SaldoFinal"];
            }
        }

        return $competenciaAtual;

    }
