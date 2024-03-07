function validarTipoContacto(Tipo,Valor){
    switch(Tipo) {
        case 1:
            return validarFijo(Valor);
        case 2:
            return validarMovil(Valor);
        case 3:
            return validarFijo(Valor);
        case 4:
            return validarMail(Valor);
        default:
            return false;
    }
}
function validarFijo(v){
    var patron =/^[9]\d{8}$/;
    return patron.test(v);
}
function validarMovil(v){
    var patron =/^[67]\d{8}$/;
    return patron.test(v);
}
function validarMail(v) {
    var patron=/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.([a-zA-Z]{2,4})+$/;
    return patron.test(v);
}
function validarMF(v){
    var patron = /^(MF|MP)[0-9]{3,4}_[1-3]{1}$/;
    return patron.test(v);
}
function validarMFTransversal(v){
    var patron = /^FCO[A-Z]{1,3}\d{2}$/;
    return patron.test(v);
}
function validarMP(v){
    var patron = /^MP\d{4}$/;
    return patron.test(v);
}
function validarCodigoUC(v){
    var patron = /^UC\d{4}_[1-3]{1}$/;
    return patron.test(v);
}
function validarUF(v){
    var patron = /^UF\d{4}$/;
    return patron.test(v);
}
function validarCodOcupa(v){
    var patron = /^[0-9]{8}/i;
    return patron.test(v);
}
function isEmpty(v) {
    return (!v || v.length === 0 );
}
function validarNIEDNI(v,m) {
    if(!v){
        return false;
    }else{
        var letras  = 'TRWAGMYFPDXBNJZSQVHLCKET';
        var nifRexp = /^[0-9]{8}[TRWAGMYFPDXBNJZSQVHLCKET]{1}$/i;
        var nieRexp = /^[XYZ]{1}[0-9]{7}[TRWAGMYFPDXBNJZSQVHLCKET]{1}$/i;
        var str     = v.toString().toUpperCase();

        if (!(nifRexp.test(str)&&!m) && !(nieRexp.test(str)&&m)) {
            $('#btnComprobarDNI').html('<i class="fas fa-times-circle text-danger"></i>');
            return false;
        }

        var nie = str.replace(/^[X]/, '0').replace(/^[Y]/, '1').replace(/^[Z]/, '2');
        var letra = str.substr(-1);
        var posicion = parseInt(nie.substr(0, 8)) % 23;

        if (letras.charAt(posicion) === letra){
            $('#btnComprobarDNI').html('<i class="fas fa-check text-success"></i>');
            return true;
        }else{
            return false;
        }
    }
}
function validarCodigoCertificado(v){
    var patron = /^[A-Z]{4}[0-9]{4}$/;
    patron.test(v);
}
function validarCodCertConArea(codigo,area){
    return codigo.substring(0, 3) === area.substring(0, 3);
}