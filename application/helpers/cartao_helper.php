<?php

    function cartao_UpdateValorMes($pDataCartao,$pValor){
        
        $ci = get_instance();
        
		// ------ MES ------	
        $dataCartao              = $ci->cartoes_fatura_model->Buscar($pDataCartao);	

        if(!empty($dataCartao )){
        
            $dataCartao["Valor"] += $pValor;

            // -- BD UPDATE --
            $ci->cartoes_fatura_model->Atualizar($dataCartao);
        }
    }