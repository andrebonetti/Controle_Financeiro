<?php

	function active_class($id, $id_atual){
	   if($id == $id_atual){return "active";}
	}

    function calcula_semana($s){
        if($s <= 7){return "1";}
        if(($s>7)&&($s<=14)){return "2";}  
        if(($s>14)&&($s<=21)){return "3";}
        if(($s>21)&&($s<=28)){return "4";} 
        if(($s>28)&&($s<=35)){return "5";} 
        if($s>35){return "5";} 
	}

    function valida_sub($categoria,$sub_categoria){
        if($sub_categoria != NULL) return $sub_categoria;
        if($sub_categoria == NULL) return $categoria."-sub";     
	}

    function calendario($type,$periodo,$n,$ano,$mes){
        	
        if($periodo == "anterior"){
            
            if(($mes - $n) < 1){
                $ano = $ano-1;
                
                if($mes == "03"){
                	if($n == 3){$mes = 12;}
                }
                if($mes == "02"){
                	if($n == 2){$mes = 12;}
					if($n == 3){$mes = 11;}
				}
                if($mes == "01"){
                	if($n == 1){$mes = 12;}
					if($n == 2){$mes = 11;}
					if($n == 3){$mes = 10;}
				}
            }
            else{
                $mes = $mes - $n;
            }
        }
        if($periodo == "proximo"){
            
            if(($mes + $n) > 12){
                $ano = $ano+1;
                
                if($mes == "10"){
                	if($n == 3){$mes = 01;}
                }
                if($mes == "11"){
                	if($n == 2){$mes = 01;}
					if($n == 3){$mes = 02;}
				}
                if($mes == "12"){
                	if($n == 1){$mes = 01;}
					if($n == 2){$mes = 02;}
					if($n == 3){$mes = 03;}
				}
            }
            else{
                $mes = $mes + $n;
            }
        }
        
		if($type == "nome"){return nome_mes($mes) ." - ". $ano;}
		if($type == "link"){return $ano."/".$mes;}
	}
