<section class="content">

	<div class="myContainer">

        <?php

        //-- S / MSG - Session -->
        $this->view('sniplets/msg_Session.php'); 
		        
        //-- G / Saldos -->        
        $this->view('grids/grid_Saldos.php'); 

        //-- G / Calendario -->
        $this->view('grids/grid_Calendario.php'); 
        
        ?>
		 
    </div>
        
    <?php    
    
    //-- G / Resumo -->    
    $this->view('grids/grid_ResumoCategorias.php');

    //-- G / Geral/Menu -->
    $this->view('grids/grid_GeralMenu.php'); 

    ?>
          
	</div>

</section>

<!-- G / MODAIS -->
<?php $this->view('grids/grid_Modais.php'); ?>

<script src="<?=base_url("js/my_script-content201711202014.js")?>"></script>