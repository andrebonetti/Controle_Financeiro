/* --- START --- */
$(".insert-produto").hide();
$("#cancel_add-produto").hide();

/* --- FUNCTIONS --- */

//PRODUTO_INFO
var content_info = function(id){
    var obj = {
        "img": $("tr#"+id).find("img").attr("src"),
        "nome": $("tr#"+id).find(".nome").text(),
        "categoria": $("tr#"+id).find(".categoria").text(),
        "sub_categoria": $("tr#"+id).find(".sub_categoria").text(),
        "marca": $("tr#"+id).find(".marca").text(),
        "cor": $("tr#"+id).find(".cor").text(),
        "preco": $("tr#"+id).find(".preco").text()
    };
    return obj;
};

//INSERT
var add_produto = function(){
    $(".insert-produto").slideDown(400);
    $("#add-produto").hide();
    $("#cancel_add-produto").fadeIn(200);
};

var cancel_add_produto = function(){
    $(".insert-produto").slideUp(400);
    $("#add-produto").fadeIn(200);
    $("#cancel_add-produto").hide();
};

//EDIT
var edit_produto = function(){
    var id = $(this).attr("name");
    var content = content_info(id);
    
    $.each(content,function( key, value ) {
        if(key == "img"){$("#update").find(key).attr("src",value);}
        if((key == "nome")||(key == "preco")){$("#update").find(".modal_"+key).attr("value",value);}
        else{$("#update").find(".modal_"+key).text(value);}
    });
    
    $(".form_update").attr("action","produto_update/"+id);
};

//DELETE
var delete_produto = function(){
    var id = $(this).attr("name");
    var content = content_info(id);
    
    $.each(content,function( key, value ) {
        if(key == "img"){$("#delete").find(key).attr("src",value);}
        else{$("#delete").find(".modal_"+key).text(value);}
    });
    
    $(".form_delete").attr("action","produto_delete/"+id);
};

/* --- EVENTS --- */
$("#add-produto").on("click",add_produto);
$("#cancel_add-produto").on("click",cancel_add_produto);
$("#cancel_add-produto").on("click",cancel_add_produto);
$("button.edit").on("click",edit_produto);
$("button.delete").on("click",delete_produto);

