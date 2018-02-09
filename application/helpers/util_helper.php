<?php

    function util_printArray($pArray,$pNomeArray = ""){

        echo "<br>-----------------------------------------------------------util_printArray--------------------------------------------------------------------------<br>";
        if($pNomeArray != ""){
            echo "<h3>".$pNomeArray."</h3>";
        }

        foreach($pArray as $keyArray => $itemArray){

            if(is_array($itemArray)){

                 echo "<br>---------------------------------------<br>";
                 echo "<b><i>Key/Index => ".$keyArray."</i></b><br><br>";

                foreach($itemArray as $keyItemArray => $itemArray){

                    echo "----<b>[".$keyItemArray."]</b> => ";

                    if(!is_array($itemArray)){
                        echo $itemArray;
                    }

                    if(is_array($itemArray)){
                        
                        foreach($itemArray as $keyItemSubArray=> $itemSubArray){

                            echo "<br>-------- <b>[".$keyItemSubArray."]</b> => ";

                            if(!is_array($itemSubArray)){
                                echo $itemSubArray;
                            }

                            if(is_array($itemSubArray)){
                                foreach($itemSubArray as $keyItemSub2Array=> $itemSub2Array){

                                    echo "<br>------------ <b>[".$keyItemSub2Array."]</b> => ".$itemSub2Array;
                                }
                            }
                            
                        }
                    }

                    echo "<br>";

                }

                 echo "<br>";
            }
            else{
                 echo "<b>[".$keyArray."]</b> => ".$itemArray."<br>";
            }

        }

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