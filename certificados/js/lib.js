function checkUC(uc) {
    var existe=false;
    $.ajax({
        url: "ajax/checkUC.php",
        data: {IdUC:uc},
        type: "POST",
        async:false,
        dataType: "json",
        success: function (data) {
            existe = !!data.resultado;
        }
    }).fail(function () {
        alert("La Unidad de competencia no es válida. Código 4.");
        existe= false;
    });
    return existe;
}
function checkMF(CodMF) {
    var existe=false;
    $.ajax({
        url: "ajax/checkMF.php",
        data: {CodMF:CodMF},
        type: "POST",
        async:false,
        dataType: "json",
        success: function (data) {
            existe = !!data.resultado;
        }
    }).fail(function () {
        alert("Se ha producido un error al comprobar el Módulo. Código 5.");
        existe= false;
    });
    return existe;
}
function checkIdMF(IdMF) {
    var existe=false;
    $.ajax({
        url: "ajax/checkIdMF.php",
        data: {IdMF:IdMF},
        type: "POST",
        async:false,
        dataType: "json",
        success: function (data) {
            existe = !!data.resultado;
        }
    }).fail(function () {
        alert("Se ha producido un error al comprobar el Módulo. Código 5.");
        existe= false;
    });
    return existe;
}
function checkMFCert(IdCertificado,IdModulo){
    var existe=true;
    $.ajax({
        url: "ajax/checkMFCert.php",
        data: {IdCertificado:IdCertificado,IdModulo:IdModulo},
        type: "POST",
        async:false,
        dataType: "json",
        success: function (data) {
            existe = !!data.resultado;
        }
    }).fail(function () {
        alert("Se ha producido un error la asociación del Módulo con el Certificado. Código 5.");
        existe= true;
    });
    return existe;
}
function checkUFMF(IdModulo,IdUnidad){
    var existe=false;
    $.ajax({
        url: "ajax/checkUFMF.php",
        data: {IdModulo:IdModulo,IdUnidad:IdUnidad},
        type: "POST",
        async:false,
        dataType: "json",
        success: function (data) {
            existe = !!data.resultado;
        }
    }).fail(function () {
        alert("Se ha producido un error la asociación del Módulo con el Certificado. Código 5.");
        existe= false;
    });
    return existe;
}
function checkCertificado(IdCertificado){
    var existe=false;
    $.ajax({
        url: "ajax/checkCert.php",
        data: {IdCertificado:IdCertificado},
        type: "POST",
        async:false,
        dataType: "json",
        success: function (data) {
            existe = !!data.resultado;
        }
    }).fail(function () {
        alert("Se ha producido un error la asociación del Módulo con el Certificado. Código 5.");
        existe= false;
    });
    return existe;
}