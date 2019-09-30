<?php

    function util_print($pConteudo,$pTitulo = ""){

        if($pTitulo != ""){
            echo "<h3>".$pTitulo."</h3>";
        }

        echo '<pre>';
        print_r( $pConteudo );
        echo '</pre>';

    }

    function util_printR($pConteudo,$pTitulo = ""){

        $ci             = get_instance();

        if($pTitulo != ""){
            echo "<h3>".$pTitulo."</h3>";
        }

        echo '<pre>';
        print_r( $pConteudo );
        echo '</pre>';

    }

    function util_AlterarMes($pParam,$pSinal = 1,$pMudaParam = false){

        $ci = get_instance();
        $rData = array();

        if($pSinal < 0){
            if($pParam["Mes"] == 1){
                $rData["Mes"] = 12;
                $rData["Ano"] = $pParam["Ano"]-1;
            }
            else{
                $rData["Mes"] = $pParam["Mes"]-1;
                $rData["Ano"] = $pParam["Ano"];
            }	
        }
        if($pSinal > 0){
            if($pParam["Mes"] == 12){
                $rData["Mes"] = 01;
                $rData["Ano"] = $pParam["Ano"]+1;
            }
            else{
                $rData["Mes"] = $pParam["Mes"]+1;;
                $rData["Ano"] = $pParam["Ano"];
            }
        }
        
        if($pMudaParam == true){
            $pParam["Mes"] = $rData["Mes"];
            $pParam["Ano"] = $rData["Ano"];

            return $pParam;
        }
        else{
            return $rData;
        }
       
    }

    function util_gerarIndiceArray($pArray,$pIndice){
        
        foreach($pArray as $keyItem => $itemArray){
            $rArray[$itemArray["$pIndice"]] = $itemArray;
        }

        if(!isset($rArray)){
            $rArray = $pArray;
        }

        return $rArray;

    }

    function util_diferenca($pValor1,$pValor2,$pIsValidacao = false){

        if($pIsValidacao){
            
            if( number_format($pValor1,2,",",".") != number_format($pValor2,2,",",".") ){
                return true;
            }
            else{
                return false;
            }

        }
        else{
            return (float)$pValor1 - (float)$pValor2;
        }

    }

    function util_transforamaIdEmChave($pArray,$pParametro,$resultado = null){

		$retorno = array();

		foreach($pArray as $keyIten => $item){

			if($resultado != null){
				$retorno[$item[$pParametro]] = $item[$resultado];
			}else{
				$retorno[$item[$pParametro]] = $item;
			}
		}

		return $retorno;

	}

    function util_isNull($pParam,$pKey = ""){
        
        if(is_array($pParam) && $pKey != ""){

            if(!array_key_exists($pKey,$pParam)){
                return true;
            }else{
                if(!is_array($pParam[$pKey])){  
                    if(is_string($pParam[$pKey])){
                        if(strlen($pParam[$pKey]) == 0){
                            return true;
                        }
                    }else{
                        if($pParam[$pKey] == ""){
                            return true;
                        }
                    } 
                }else{
                    if(count($pParam[$pKey]) == 0){
                        return true;
                    }
                }
            }
        }else{
            if(!isset($pParam)){
                return true;
            }else{
                if(!is_array($pParam)){  
                    if(is_string($pParam)){
                        if(strlen($pParam) == 0){
                            return true;
                        }
                    }else{
                        if($pParam == ""){
                            return true;
                        }
                    } 
                }else{
                    if(count($pParam) == 0){
                        return true;
                    }
                }
            }           
        }

        return false;

    }

    function util_isNotNull($pParam,$pKey = ""){

        if(is_array($pParam) && $pKey != ""){  

            if(array_key_exists($pKey,$pParam)){
                if(!is_array($pParam[$pKey])){
                    if(is_string($pParam[$pKey])){
                        if(strlen($pParam[$pKey]) > 0){
                            return true;
                        }
                    }else{ 
                        if($pParam[$pKey] != "" || $pParam[$pKey] > 0){
                            return true;
                        }
                    }  
                }else{
                    if(count($pParam[$pKey]) > 0){
                        return true;
                    }
                }
            }
        }else{         
            if(isset($pParam)){
                if(!is_array($pParam)){  
                    if(is_string($pParam)){
                        if(strlen($pParam) > 0){
                            return true;
                        }
                    }else{
                        if($pParam != "" || $pParam > 0){
                            return true;
                        }
                    }   
                }else{
                    if(count($pParam) > 0){
                        return true;
                    }
                }
            } 
        }

        return false;

    }