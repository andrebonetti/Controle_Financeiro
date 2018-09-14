<?php

    function util_print($pConteudo,$pTitulo = ""){

        $titulo = "";

        echo "<br>------------------------------------------------------------------util_printArray----------------------------------------------------------------------------------<br>";
        if($pTitulo != ""){
            $titulo = "<h3>".$pTitulo."</h3>";
        }

        if(!is_array($pConteudo)){
            echo $titulo;
            echo "<b>String => </b>".$pConteudo."<br>";
        }

        if(is_array($pConteudo)){

            // $titulo .= " => ".count($pConteudo)."<br>";
            // echo $titulo;

            foreach($pConteudo as $keyArray => $itemArray){

                if(!is_array($itemArray)){
                    echo "<b>[".$keyArray."]</b> => ".$itemArray."<br>";
                }

                if(is_array($itemArray)){

                    echo "<br>---------------------------------------<br>";
                    echo "<b><i>Key/Index => ".$keyArray."</i></b> | (".count($itemArray).")<br><br>";

                    foreach($itemArray as $keyItemSub2Array=> $itemSub2Array){

                        echo "----<b>[".$keyItemSub2Array."]</b> => ";

                        if(!is_array($itemSub2Array)){
                            echo $itemSub2Array;
                        }

                        if(is_array($itemSub2Array)){

                            echo "| (".count($itemSub2Array).")";
                            
                            foreach($itemSub2Array as $keyItemSub3Array=> $itemSub3Array){

                                echo "<br>-------- <b>[".$keyItemSub3Array."]</b> => ";

                                if(!is_array($itemSub3Array)){
                                    echo $itemSub3Array;
                                }

                                if(is_array($itemSub3Array)){

                                    echo "| (".count($itemSub3Array).")";

                                    foreach($itemSub3Array as $keyItemSub4Array=> $itemSub4Array){

                                        echo "<br>------------ <b>[".$keyItemSub4Array."]</b> => ";

                                        if(!is_array($itemSub4Array)){
                                            echo $itemSub4Array;
                                        }

                                        if(is_array($itemSub4Array)){

                                            echo "| (".count($itemSub4Array).")";
                            
                                            foreach($itemSub4Array as $keyItemSub5Array=> $itemSub5Array){

                                                echo "<br>------------------------ <b>[".$keyItemSub5Array."]</b> => ";

                                                if(!is_array($itemSub5Array)){
                                                    echo $itemSub5Array;
                                                }

                                                if(is_array($itemSub5Array)){

                                                    echo "| (".count($itemSub5Array).")";

                                                    foreach($itemSub5Array as $keyItemSub6Array=> $itemSub6Array){

                                                        echo "<br>------------------------------------------------ <b>[".$keyItemSub6Array."]</b> => ";

                                                        if(!is_array($itemSub6Array)){
                                                            echo $itemSub6Array;
                                                        }

                                                        if(is_array($itemSub6Array)){

                                                            echo "| (".count($itemSub6Array).")";

                                                            foreach($itemSub6Array as $keyItemSub7Array=> $itemSub7Array){

                                                                echo "<br>------------------------------------------------------------------------------------------------ <b>[".$keyItemSub7Array."]</b> => ".$itemSub7Array;
                                                                
                                                            }
                                                        }
                                                        
                                                    }
                                                }
                                                
                                            }
                                        }

                                    }
                                }
                                
                            }
                        }

                        echo "<br>";

                    }

                    echo "<br>";
                }
                
            }
        }

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

    function util_AlterarMes($pParam,$pSinal,$pMudaParam = false){

        $ci = get_instance();
        $rData = array();

        if($pSinal < 0){
            if($pParam["Mes"] == 1){
                $rData["Mes"] = 12;
                $rData["Ano"] = $pParam["Ano"]-1;
            }
            else{
                $rData["Mes"] = $pParam["Mes"]-1;;
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