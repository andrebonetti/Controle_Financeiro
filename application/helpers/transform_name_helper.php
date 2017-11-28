<?php

function numeroEmReais($numero) {
	    return "R$ " . number_format($numero, 2, ",", ".");
}

function numeroEmReais2($numero) {
	    return number_format($numero, 2, ",", ".");
}

function no_acento_code($string){
			
		$string = preg_replace("/ /", "-", $string); 
		$string = preg_replace("/ã/", "a", $string);
		$string = preg_replace("/Ã/", "A", $string);
        $string = preg_replace("/á/", "a", $string);
        $string = preg_replace("/Á/", "A", $string);
        $string = preg_replace("/â/", "a", $string);
        $string = preg_replace("/Â/", "A", $string);
		$string = preg_replace("/é/", "e", $string);
        $string = preg_replace("/É/", "E", $string);
        $string = preg_replace("/ê/", "e", $string);
        $string = preg_replace("/Ê/", "E", $string);
		$string = preg_replace("/ç/", "c", $string);
        $string = preg_replace("/Ç/", "C", $string);	
        $string = preg_replace("/í/", "i", $string);
        $string = preg_replace("/Í/", "I", $string);
		$string = preg_replace("/,/", "-", $string);
        
		return $string;
}

function valor_decimal($string){
        $string = str_replace(".", "", $string);    
		$string = str_replace(",", ".", $string);     
		return $string;
}

function sinal_valor($valor){
    if($valor < 0){return "negativo";};
    if($valor > 0){return "positivo";};  
	if($valor == 0){return "none";};    
}

function nome_mes($mes){
    if($mes == 1){return "Janeiro";};
    if($mes == 2){return "Fevereiro";};    
    if($mes == 3){return "Março";};  
    if($mes == 4){return "Abril";};  
    if($mes == 5){return "Maio";};  
    if($mes == 6){return "Junho";};  
    if($mes == 7){return "Julho";};  
    if($mes == 8){return "Agosto";};  
    if($mes == 9){return "Setembro";}; 
    if($mes == 10){return "Outubro";};  
    if($mes == 11){return "Novembro";};
    if($mes == 12){return "Dezembro";};  
}

function calcularCompetencia($pAno,$pMes,$pNumParam){

    if($pNumParam > 0){

        for($n = 1;$n <= $pNumParam;$n ++){

            if($pMes == 12){
                $pMes = 1;
                $pAno ++;
            }
            else{
                $pMes ++;
            }

        }

    }
    if($pNumParam < 0){
    
        for($n = 1;$n <= $pNumParam;$n ++){

            if($pMes == 1){
                $pMes = 2;
                $pAno --;
            }
            else{
                $pMes --;
            }

        }

    }
    
    $data["Ano"] = $pAno;
    $data["Mes"] = $pMes;

    return $data;
}