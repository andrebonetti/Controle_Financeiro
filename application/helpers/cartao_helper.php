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

    function cartao_buscarFaturasMes($pParamCartoesFatura){
        
        $ci = get_instance();
        
		$lCartoes_Fatura    = $ci->cartoes_fatura_model->Listar($pParamCartoesFatura);  

        if(empty($lCartoes_Fatura)){

            #GERAR FATURA
            //util_PrintR("GERA FATURA CARTAO MES");

            $lCartoesUsuario = $ci->cartoes_model->ListarCartoesAtivos($pParamCartoesFatura);
            //util_PrintR($lCartoesUsuario,'$lCartoesUsuario');

            foreach($lCartoesUsuario as $keyCartao => $valueCartao){
                $iData["IdCartao"]  = $valueCartao["Id"];
                $iData["Ano"]       = $pParamCartoesFatura["Ano"];
                $iData["Mes"]       = $pParamCartoesFatura["Mes"];
                $iData["Valor"]     = 0;

                #VALOR INICIAL
                $paramBusca["IdCartao"] = $valueCartao["Id"];
                $paramBusca["Ano"]      = $pParamCartoesFatura["Ano"];
                $paramBusca["Mes"]      = $pParamCartoesFatura["Mes"];

                $lTransacoesCartao      = $ci->transacoes_model->ListarPorRegraTipo($paramBusca);

                foreach($lTransacoesCartao as $valueTransacao){
                    $iData["Valor"]     += $valueTransacao["Valor"];
                }

                $ci->cartoes_fatura_model->Incluir($iData);
            }  

            $lCartoes_Fatura = $ci->cartoes_fatura_model->Listar($pParamCartoesFatura); 

            //util_PrintR($lCartoes_Fatura,'$lCartoes_Fatura GERADO');    

        }

        return $lCartoes_Fatura;
    }