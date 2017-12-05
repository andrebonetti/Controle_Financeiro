<div class="saldos">
        	
    <?=anchor("content/month_content/".calendario("link","anterior",1,$ano,$mes).""," ",array("class"=>"seta-esquerda competencia-anterior"))?>        

    <div class="saldo-anterior" value="<?=$competenciaAnterior["SaldoFinal"]?>">
        Saldo Anterior: <span><?=numeroEmReais2($competenciaAnterior["SaldoFinal"])?></span> 
    </div>
    
    <div class="saldo-mes <?=sinal_valor($competenciaAtual["SaldoMes"])?>" value="<?=$competenciaAtual["SaldoMes"]?>">      
        <span class="valor_span"><?=numeroEmReais2($competenciaAtual["SaldoMes"])?></span> 
    </div>
    
    <?=anchor("content/month_content/".calendario("link","proximo",1,$ano,$mes).""," ",array("class"=>"seta-direita competencia-proxima"))?>

    <div class="saldo-futuro" value="<?=$competenciaAtual["SaldoFinal"]?>">
       <span class="valor_span"><?=numeroEmReais2($competenciaAtual["SaldoFinal"])?></span>       
    </div>
     
</div>


