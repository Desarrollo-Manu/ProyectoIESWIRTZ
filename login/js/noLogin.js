(function() {
    $(document).ready( function () {
        $('#basic-passView').click(function() {
            if('password' == $('#pass').attr('type')){
                $('#pass').prop('type', 'text');
            }else{
                $('#pass').prop('type', 'password');
            }
        });
    });
})();