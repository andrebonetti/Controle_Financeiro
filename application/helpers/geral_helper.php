<?php

    function geral_UpdateSaldo($pData,$pTipo){
        
        $ci = get_instance();
        
        $pData["PeriodoDe"] = true;
        $pData["Id"] = null;
        $lGeral = $ci->geral_model->Listar($pData);
   
        $cont = 1;
        foreach($lGeral as $itemGeral){		                    

            $dataGeral["Mes"]   = $itemGeral["Mes"]; 
            $dataGeral["Ano"]   = $itemGeral["Ano"];
            $dataGeral["Valor"] = $pData["Valor"];

            if(($cont == 1)or($pData["IdTipoTransacao"] == 1)){
                geral_UpdateSaldoMes($dataGeral);
            }
            
            if($pData["IdTipoTransacao"] == 1){
                $dataGeral["Valor"] = $pData["Valor"] * $cont;
            }
                        
            geral_UpdateSaldoFinal($dataGeral,$pTipo);
            $cont++;
        }
    }

    function geral_UpdateSaldoManual($pData){

        $ci = get_instance();

        $pData["PeriodoDe"] = true;
        $pData["Id"] = null;
        
        $lGeral = $ci->geral_model->Listar($pData);
        
        var_dump($lGeral);

        $saldoAnterior = 0;
        $cont = 0;
        foreach($lGeral as $itemGeral){		                    

            $dataGeral["Mes"]   = $itemGeral["Mes"]; 
            $dataGeral["Ano"]   = $itemGeral["Ano"];
            $dataGeral["Id"]   = $itemGeral["Id"];
            
            if($cont > 0){
                
                $dataGeral["SaldoFinal"] = $saldoAnterior + $itemGeral["SaldoMes"];

                echo $dataGeral["SaldoFinal"];
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

        $dataGeralMes["SaldoMes"] += $pData["Valor"];
		
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

    function geral_UpdateSaldoFinal($pData,$pTipo){
        
		$ci = get_instance();
              
        // ---- TOTAL GERAL ----		
        $lGeral = $ci->geral_model->Listar($pData);
		foreach($lGeral as $itemGeral){	
            
            $dataGeralFinal                = $itemGeral;
			$dataGeralFinal["SaldoFinal"] += $pData["Valor"];
            
            echo "var_dump = geral_UpdateSaldoMes Antes";

            if($pTipo == 1){$dataGeralFinal["Receita"] += $pData["Valor"];}        
            if($pTipo == 2){$dataGeralFinal["Despesas"] += $pData["Valor"]*(-1);}
            
            echo "var_dump = geral_UpdateSaldoMes";

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
        $lGeral = $ci->geral_model->Listar();
        
        $geral = $lGeral[count($lGeral) - 1];
        
        $ano = $geral["Ano"];
        $mes = $geral["Mes"];

        while(intval($ano.$mes) < intval($pDataContent["Ano"].$pDataContent["Mes"]))
        {
            if($mes == 12){
                $dataGeral["Ano"] = $ano + 1;
                $dataGeral["Mes"] = 1;
            }
            else{
                $dataGeral["Mes"] = $mes + 1;
                $dataGeral["Ano"] = $ano;
            }
            
            if($dataGeral["Mes"] < 10){
                $dataGeral["Mes"] = "0".$dataGeral["Mes"];
            }
            $dataGeral = CalcularSaldo($dataGeral); 
            
            $ci->geral_model->Incluir($dataGeral);
            
            $ano = $dataGeral["Ano"];
            $mes = $dataGeral["Mes"];
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
        $cartao_Recorrente       = $ci->cartao_model->ListarFaturaRecorrente($dataContent);

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
