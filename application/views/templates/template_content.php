<header class="content"> 
    <div class="myContainer">
        <?php /*anchor("Login/sair","Sair",array("class"=>"sair")) */?>
        <ul class="conta-opcoes nav navbar-nav">
            <li class="dropdown nome-usuario">
               
                <a href="#" class="dropdown-toggle " data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                        ANDRE BONETTI DE OLIVEIRA - DESENV<span class="caret">
                    </span>
                </a>

                <ul class="dropdown-menu"> 
                    <li>
                        <a href="#" data-toggle="modal" data-target="#modal-trocar-senha"><span class="glyphicon glyphicon-retweet" aria-hidden="true"></span>Alterar Senha</a>
                    </li>    
                </ul>   
            </li> 
            <li class="sair">
                <a href="/index.php/Login/sair"><span class="glyphicon glyphicon-off" aria-hidden="true"></span>Sair</a>	
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