<?php

    function geral_UpdateSaldo($pData){
        
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
                        
            geral_UpdateSaldoFinal($dataGeral);
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

    function geral_UpdateSaldoFinal($pData){
        
		$ci = get_instance();
              
        // ---- TOTAL GERAL ----		
        $lGeral = $ci->geral_model->Listar($pData);
		foreach($lGeral as $itemGeral){	
            
            $dataGeralFinal                = $itemGeral;
			$dataGeralFinal["SaldoFinal"] += $pData["Valor"];
            
            // -- BD UPDATE --
            $ci->geral_model->Atualizar($dataGeralFinal);	
		}
         
	}