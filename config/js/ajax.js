function peticionAjax(data,url,plantilla,Destino,CodigoError){
    $('.loader').removeClass('d-none');
    $.ajax({
        url: url,
        data: {},
        type: "POST",
        dataType: "json",
        success: function (data) {
            if(!data.resultado){
                Destino.html(Mustache.render(plantilla.html(),data));
            }else{
                alert("No se ha podido ejecutar la petición. Contacta con el CAU. Código "+ CodigoError+'.');
            }
            $('.loader').addClass('d-none');
        }
    }).fail(function () {
        alert("No se ha podido ejecutar la petición. Contacta con el CAU. Código "+ CodigoError+'.');
    });
}
//peticionAjax(data,"ajax/obterFamilias.php",$('#listFamiliasMTem'),$("#listFamiliasM"),1);