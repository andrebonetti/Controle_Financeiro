<div class="saldos">
        	
    <div class="saldo-anterior" value="<?=$competenciaAnterior["SaldoFinal"]?>">
        Saldo Anterior: <span><?=numeroEmReais2($competenciaAnterior["SaldoFinal"])?></span> 
    </div>
    
    <div class="saldo-mes <?=sinal_valor($competenciaAtual["SaldoMes"])?>" value="<?=$competenciaAtual["SaldoMes"]?>">
        
        <span class="alteracao_manual" name="SaldoMes" title="Alterar" value="0">Saldo do Mes:</span>
        <span class="valor_span"><?=numeroEmReais2($competenciaAtual["SaldoMes"])?></span> 
    
        <?= form_open("adm_crud/alteracao_manual",array("class"=>"alteracao_manual alteracao-SaldoMes"))?>
            
            <input type="hidden" name="tipo_alteracao" value="SaldoMes">
            <input type="hidden" name="ano" value="<?=$ano?>">
            <input type="hidden" name="mes" value="<?=$mes?>">
            <input type="hidden" name="SaldoAnterior" value="<?=$competenciaAnterior["SaldoFinal"]?>">
        
            <input type="text" class="form-control input-SaldoMes" name="valor" value="<?=numeroEmReais2($competenciaAtual["SaldoMes"])?>">

            <input type="submit" class="btn btn-success" value="Ok"> 
            
        <?= form_close()?>
        
    </div>
    
    <div class="saldo-futuro" value="<?=$competenciaAtual["SaldoFinal"]?>">
        
        <span class="alteracao_manual" name="SaldoFinal" title="Alterar" value="0">Saldo Futuro:</span>
        <span class="valor_span"><?=numeroEmReais2($competenciaAtual["SaldoFinal"])?></span> 
            
        <?= form_open("adm_crud/alteracao_manual",array("class"=>"alteracao_manual alteracao-SaldoFinal"))?>
            
            <input type="hidden" name="tipo_alteracao" value="SaldoFinal">
            <input type="hidden" name="ano" value="<?=$ano?>">
            <input type="hidden" name="mes" value="<?=$mes?>">
            <input type="hidden" name="SaldoAnterior" value="<?=$competenciaAnterior["SaldoFinal"]?>">
        
            <input type="text" class="form-control input-SaldoFinal" name="valor" value="<?=numeroEmReais2($competenciaAtual["SaldoFinal"])?>">

            <input type="submit" class="btn btn-success" value="Ok"> 
            
        <?= form_close()?>
        
    </div>
    
</div>