<section class="geral">
	<div class="myContainer">
		        
		<table class="table">
			
			<tr>
				<th>Ano</th>
				<th>Mês</th>
				<th>Salário</th>
				<th>Saldo Mês</th>
				<th>Saldo Final</th>
				<th>Poupança</th>
				<th>Poupança Final</th>
			</tr>
			
			<?php foreach($data as $content){ ?>
			
				<tr>
					<!--ANO-->
					<?php if($content["mes"] == 1){?><td><?=$content["ano"]?></td><?php } 
					else {?><td></td><?php } ?>
					
					<td><?=nome_mes($content["mes"])?></td>
					<td><?=numeroEmReais2($content["salario"])?></td>
					<td class="<?=sinal_valor($content["saldo_mes"])?>"><?=numeroEmReais2($content["saldo_mes"])?></td>
					<td><?=numeroEmReais2($content["saldo_final"])?></td>
					<td class="<?=sinal_valor($content["poupanca"])?>"><?=numeroEmReais2($content["poupanca"])?></td>
					<td><?=numeroEmReais2($content["poupanca_final"])?></td>
					
				</tr>
			
			<?php } ?>
			
		</table>    
        
	</div>
</section>