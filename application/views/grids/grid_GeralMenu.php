<div class="calendario-geral">
        
    <div class="anterior">
        <?=anchor("content/month_content/".calendario("link","anterior",3,$ano,$mes)."","<img src='".base_url('img/Calendario_anterior.png')."'><h3>".calendario("nome","anterior",3,$ano,$mes)."</h3>")?>
        <?=anchor("content/month_content/".calendario("link","anterior",2,$ano,$mes)."","<img src='".base_url('img/Calendario_anterior.png')."'><h3>".calendario("nome","anterior",2,$ano,$mes)."</h3>")?>
        <?=anchor("content/month_content/".calendario("link","anterior",1,$ano,$mes)."","<img src='".base_url('img/Calendario_anterior.png')."'><h3>".calendario("nome","anterior",1,$ano,$mes)."</h3>")?>
    </div>

    <div class="geral">
        <?=anchor("content/geral","<img src='".base_url('img/Calendario_geral.png')."'><h3>Geral</h3>")?>
    </div>
    
    <div class="proximo">
        <?=anchor("content/month_content/".calendario("link","proximo",1,$ano,$mes)."","<img src='".base_url('img/Calendario_futuro.png')."'><h3>".calendario("nome","proximo",1,$ano,$mes)."</h3>")?>
        <?=anchor("content/month_content/".calendario("link","proximo",2,$ano,$mes)."","<img src='".base_url('img/Calendario_futuro.png')."'><h3>".calendario("nome","proximo",2,$ano,$mes)."</h3>")?>
        <?=anchor("content/month_content/".calendario("link","proximo",3,$ano,$mes)."","<img src='".base_url('img/Calendario_futuro.png')."'><h3>".calendario("nome","proximo",3,$ano,$mes)."</h3>")?>
    </div>
    
</div>
    