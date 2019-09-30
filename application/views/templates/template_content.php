<header class="content"> 
    <div class="myContainer">
        <ul class="conta-opcoes nav navbar-nav">
            <li class="dropdown nome-usuario">              
                <a href="#" class="dropdown-toggle " data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                        <?=$usuario["Nome"]?>
                    </span>
                </a>
            </li> 
            <li class="sair">
                <?=anchor("login/sair","<span class='glyphicon glyphicon-off' aria-hidden='true'></span>Sair")?>
            </li>
        </ul>
    </div>    
</header>

<section class="content">

	<div class="myContainer">

        <?php

        //-- S / MSG - Session -->
        $this->view('sniplets/msg_Session.php');

        //-- G / Filtros -->        
        $this->view('grids/grid_Filtros.php');  
		        
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