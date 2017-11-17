<section class="content">
	<div class="myContainer">
        
        <!-- S / MSG - Session -->
        <?php include("sniplets/msg_Session.php");?>
		        
        <!-- T / Saldos -->        
        <?php include("templates/content_Saldos.php");?>

        <!-- T / Calendario -->
        <?php include("templates/content_Calendario.php");?>
		 
    </div>
        
    <!-- T / Resumo -->    
    <?php include("templates/content_ResumoCategorias.php");?>
    
    <!-- T / Geral/Menu -->
    <?php include("templates/content_GeralMenu.php");?>
          
	</div>
</section>

<!-- T / MODAIS -->
<?php include("templates/content_Modais.php");?>

<script src="<?=base_url("js/my_script-content201711131638.js")?>"></script>