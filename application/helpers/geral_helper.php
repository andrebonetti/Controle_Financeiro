<?php

    function geral_UpdateSaldo($pData){
        
        $ci = get_instance();
        
        $pData["periodo_de"] = true;
        $pData["id"] = null;
        $lGeral = $ci->geral_model->Listar($pData);
                    
        $cont = 1;
        foreach($lGeral as $itemGeral){		                    

            $dataGeral["mes"]   = $itemGeral["mes"]; 
            $dataGeral["ano"]   = $itemGeral["ano"];
            $dataGeral["valor"] = $pData["valor"];

            if(($cont == 1)or($pData["type"] == 1)){
                geral_UpdateSaldoMes($dataGeral);
            }
            
            if($pData["type"] == 1){
                $dataGeral["valor"] = $pData["valor"] * $cont;
            }
                        
            geral_UpdateSaldoFinal($dataGeral);
            $cont++;
        }
    }

    function geral_UpdateSaldoMes($pData){
        
		$ci = get_instance();
        
		// ------ MES ------	
        $dataGeralMes              = $ci->geral_model->Buscar($pData);	
        
		$dataGeralMes["saldo_mes"] += $pData["valor"];
        
        // -- BD UPDATE --
		$ci->geral_model->Atualizar($dataGeralMes);
         
	}

    function geral_UpdateSaldoFinal($pData){
        
		$ci = get_instance();
              
        // ---- TOTAL GERAL ----		
        $lGeral = $ci->geral_model->Listar($pData);
		foreach($lGeral as $itemGeral){	
            
            $dataGeralFinal                = $itemGeral;
			$dataGeralFinal["saldo_final"] += $pData["valor"];
            
            // -- BD UPDATE --
            $ci->geral_model->Atualizar($dataGeralFinal);	
		}
         
	}