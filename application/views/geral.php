<section class="geral">
	<div class="myContainer">
		        
		<table class="table">
			
			<tr>
				<th>Ano</th>
				<th>Mês</th>
				<th>Receita</th>
				<th>Despesas</th>
				<th>Saldo Mês</th>
				<th>Saldo Final</th>
				<th>Poupança</th>
				<th>Poupança Final</th>
			</tr>
			
			<?php foreach($lGeral as $content){ ?>
			
				<tr>
					<!--ANO-->
					<?php if($content["Mes"] == 1){?><td><?=$content["Ano"]?></td><?php } 
					else {?><td></td><?php } ?>
					
					<td><?=nome_mes($content["Mes"])?></td>
					<td><?=numeroEmReais2($content["Receita"])?></td>
					<td><?=numeroEmReais2($content["Despesas"])?></td>
					<td class="<?=sinal_valor($content["SaldoMes"])?>"><?=numeroEmReais2($content["SaldoMes"])?></td>
					<td><?=numeroEmReais2($content["SaldoFinal"])?></td>
					<td class="<?=sinal_valor($content["Poupanca"])?>"><?=numeroEmReais2($content["Poupanca"])?></td>
					<td><?=numeroEmReais2($content["PoupancaFinal"])?></td>
					
				</tr>
			
			<?php } ?>
			
		</table>    
        
	</div>
</section>