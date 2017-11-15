var base_url = $(".base_url").attr("href");

// --------------------- VIEW ------------------- 
var ultimaSemana = $(".dia-calendario").last().data("semana");
var semanaAltura=1;
   
while(semanaAltura<=ultimaSemana){

    var altura = 0;
    var altura_nova = 0;

    $(".semana-mes-"+semanaAltura).each(function() {

        altura_nova = $(this).height();

        if(altura_nova > altura){
            altura = altura_nova;
        }      

    });

    $(".semana-mes-"+semanaAltura).css("height",(altura + 60)+"px");  

    semanaAltura++;  
}

setTimeout(function(){

    $(".data-total_saldoDia").css("position","absolute");
    $(".data-total_saldoDia").css("bottom","18px");
    $(".data-total_saldoDia").show();
    $(".data-total_saldoFinal").css("position","absolute");
    $(".data-total_saldoFinal").css("bottom","0");
    $(".data-total_saldoFinal").show();

},300);

// ----- Exibe Categorias
var mostra_categorias = function(){
    $(".categorias").slideDown(600);
    $(".mostra-categoria").addClass("view");
    $(".mostra-categoria").hide();
    $(".oculta-categoria").show();
};

// ----- Oculta Categorias
var oculta_categorias = function(){
    $(".categorias").slideUp(600);
    $(".mostra-categoria").removeClass("view");
    $(".mostra-categoria").show();
    $(".oculta-categoria").hide();
};

// --------------------- PREENCHE E CALCULA CATEGORIAS E SUB-CATEGORIAS --------------------- 
$(".categoria-resumo").each(function() {

    var total_categoria = 0;

    $(this).find(".sub_categoria-resumo").each(function() {
        
        var dom_subcategoria = $(this);
        var id_subcategoria  = $(this).data("id-subcategoria");
        var count_subcategoria = 0;
        var total_sub_categoria = 0;

        $(".IdSubCategoria-"+id_subcategoria).each(function(){ 

            var info_content = $(this).find(".info-content");

            var dia          = info_content.data("dia");
            var valor        = info_content.data("valor");
            var descricao    = info_content.data("descricao");
            
            var conteudo     = 
                "<tr>"
                +   "<td class='dia'>" + dia + " - </td>"
                +   "<td class='descricao'>" + descricao + "</td>"
                +   "<td class='valor' value="+valor+" >" + formatarReal(valor) + "</td>"
                +"</tr>";
            
            $(conteudo).insertAfter(dom_subcategoria);
            
            count_subcategoria++;
            total_sub_categoria += valor;
        });

        if(count_subcategoria > 0){
            total_categoria += total_sub_categoria;
        }
        else{
            dom_subcategoria.remove();
        }
        
    });
    
});

// ------------------------- MODAL TRANSACAO -------------------
$("tr.data-day").click(function(){
    var diaMes = $(this).closest(".dia").data("dia-mes");

    $(".modal-adiciona").find(".dia-add").attr("value",diaMes);
});

var AlteraCategoria = function(){
    
    var categoria_selecionada = $(this).val();
    
    if(categoria_selecionada == "nova-categoria"){

        $(".adiciona-categoria").slideDown();
        
        var nova_categoria = $(".adiciona-categoria").find("input").val();
    
        if(nova_categoria != ""){
            $("select.categoria-sub").append("<option value='categoria-nova'>"+nova_categoria+"</option>");
        }

    }
    else
    {
        $(".option_SubCategoria").each(function() {
    
            var id_categoria = $(this).attr("name");

            if(id_categoria != categoria_selecionada){
                $(this).hide();
            }
            else{
                $(this).show();
            }
        })
    }
    
};

var CancelarNovaCategoria = function(){
    
    $(".adiciona-categoria").slideUp();
    $("input_adiciona-categoria").val("");
    
}

var AlteraSubCategoria = function(){

    var subCategoria_selecionada = $(this).val();

    if(subCategoria_selecionada == "nova-sub_categoria"){
        
        $(".adiciona-sub_categoria").slideDown();
        
        var nova_sub_categoria = $(".adiciona-categoria").find("input").val(); 
    }
};

// -------------------------------- PREENCHE MODAL - EDICAO TRANSACAO -----------------------------
$(".content-day").click(function(){

    //Busca Dados
    var info_content = $(this).find(".info-content");

    var id                      = info_content.data("id");
    var type                    = info_content.data("type");
    var dia                     = info_content.data("dia");
    var categoria_descricao     = info_content.data("categoria-descricao");
    var categoria_id            = info_content.data("categoria-id");
    var sub_categoria_descricao = info_content.data("subcategoria-descricao");
    var sub_categoria_id        = info_content.data("subcategoria-id");
    var descricao               = info_content.data("descricao");
    var valor                   = info_content.data("valor");
    var totalParcelas           = info_content.data("p-total-atual");

    //Preenche Inputs

    var_DomModalEdita = $(".modal-edita");

    var ano = var_DomModalEdita.find(".ano-edit").attr("value");
    var mes = var_DomModalEdita.find(".mes-edit").attr("value");
    
    var_DomModalEdita.find(".id-edit").attr("value",id);
    var_DomModalEdita.find(".dia-edit").attr("value",dia);
    var_DomModalEdita.find(".categoria-atual").val(categoria_id).change();
    var_DomModalEdita.find(".sub_categoria-atual").val(sub_categoria_id).change();
    var_DomModalEdita.find(".descricao-atual").attr("value",descricao);
    var_DomModalEdita.find(".valor-atual").attr("value",valor);
    var_DomModalEdita.find(".p-total-atual").attr("value",totalParcelas);
    var_DomModalEdita.find(".link-update").attr("action",base_url+"adm_crud/transacao_update/"+ano+"/"+mes+"/"+id);
    
	$('.p-total-atual').attr("disabled", true);
    $('.p-total-atual').css("background-color", "#cccccc"); 
    
    if(type == "1")
    {
        var_DomModalEdita.find(".mes-edit").attr("disabled", true);
        var_DomModalEdita.find(".ano-edit").attr("disabled", true);
    }
    else{
        var_DomModalEdita.find(".mes-edit").attr("disabled", false);
        var_DomModalEdita.find(".ano-edit").attr("disabled", false);
    }
    
});

// -------------------------------- PREENCHE MODAL - EDICAO CARTAO -----------------------------
var edita_fatura = function(){
    var id              = $(this).find(".id").text();
    var type            = $(this).find(".type").text();
    var categoria       = $(this).find(".categoria").text();
    var categoria_id    = $(this).find(".categoria").attr("value");
    var sub_categoria   = $(this).find(".sub_categoria").text();
    var sub_categoria_id= $(this).find(".sub_categoria").attr("value");
    var descricao       = $(this).find(".descricao").attr("value");
    var valor           = $(this).find(".valor").attr("value");
    var totalParcelas   = $(this).find(".p_total-atual").text();
    var dataCompra      = $(this).find(".dataCompra-atual").text();
    var IdTipoTransacao = $(this).find(".IdTipoTransacaoCartao-atual").text();
    
    var ano = $(".modal-cartao-edit").find(".ano").attr("value");
    var mes = $(".modal-cartao-edit").find(".mes").attr("value");
    
    $(".modal-cartao-edit").find(".id-edit").attr("value",id);
    $(".modal-cartao-edit").find(".categoria-atual").val(categoria_id).change();
    $(".modal-cartao-edit").find(".sub_categoria-atual").val(sub_categoria_id).change();
    $(".modal-cartao-edit").find(".descricao-atual").attr("value",descricao);
    $(".modal-cartao-edit").find(".valor-atual").attr("value",valor);
    $(".modal-cartao-edit").find(".p-total-atual").attr("value",totalParcelas);
    $(".modal-cartao-edit").find(".p-total-atual-hidden").attr("value",totalParcelas);
    $(".modal-cartao-edit").find(".link-update").attr("action",base_url+"adm_crud/cartao_update/"+ano+"/"+mes+"/"+id);
    $(".modal-cartao-edit").find(".dataCompra-edit").attr("value",dataCompra);
  
    if(IdTipoTransacao == 1){
        $(".modal-cartao-edit").find(".isRecorrente-edit").attr("value","1");
    }
    
    $(".modal-cartao-edit").find('.p-total-atual').attr("disabled", true);
    $(".modal-cartao-edit").find('.p-total-atual').css("background-color", "#cccccc"); 
};

$(".link-delete").on("click",function(){
    
    action = $(".modal-edita").find(".link-update").attr("action");
    
    $(".modal-edita").find(".link-update").attr("action",action +"/1");
});

// ---------------- ALTERACAO MANUAL ----------------------- //
var alteracao_manual = function(){
    
    status = $(this).attr("value");
    tipo = $(this).attr("name");
    
    if(status == 0){       
        $(this).attr("value",1);
        $(".alteracao-"+tipo).show();       
    }
    
    if(status == 1){       
        $(this).attr("value",0);
        $(".alteracao-"+tipo).hide();       
    }
     
}

// -------------- EVENTOS ----------------------
$(".categoria_modal").on("change",AlteraCategoria);
$(".subcategoria_modal").on("change",AlteraSubCategoria);
$(".cancelar_nova_categoria").on("click",CancelarNovaCategoria);

$(".mostra-categoria").on("click",mostra_categorias);
$(".oculta-categoria").on("click",oculta_categorias);
$(".content-fatura").on("click",edita_fatura);

// -- ALTERACAO MANUAL -- 
$(".alteracao_manual").on("click",alteracao_manual);

// -------------- FORMATACAO REAL ----------------------
function formatarReal(mixed) {

    var int = parseInt(parseFloat(mixed).toFixed(2).toString().replace(/[^\d]+/g, ''));
    var tmp = int + '';
    tmp = tmp.replace(/([0-9]{2})$/g, ",$1");
    if (tmp.length > 6)
        tmp = tmp.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
    
    if(parseFloat(mixed) < 0){
        tmp = "- "+tmp;    
    }
    
    return tmp;
}

