<?php

	function dataPtBrParaMysql($dataPtBr) {
    	$partes = explode("/", $dataPtBr);
		if(count($partes) > 1){
			return "{$partes[2]}-{$partes[1]}-{$partes[0]}";
		}else{
			return $dataPtBr;
		}
	}
	
	function dataMysqlParaPtBr($dataPtBr) {
    	$partes = explode("-", $dataPtBr);
		if(count($partes) > 1){
			return "{$partes[2]}/{$partes[1]}/{$partes[0]}";
		}else{
			return $dataPtBr;
		}
	}