$(function(){
    /**
     * Check et valide ou non les conditions d'un formulaire.
     * /!\ Ceci étant du check en Javascript, des check côté serveur sont toujours obligatoires
     * Les test ici ne renvoyant que du contenu visuel
     */
    $('body').on('change', 'input.formManager', function(){
        var error = false;
        $('input.formManager').each(function(){
            var controls = $(this).attr('data-formManager').split(' ');
            var content = $(this).val();

            //Pour chaque condition dans le tableau
            var brk = false;
            for (var i = 0; i < controls.length; i++) {
                var control = controls[i];
                switch (control) {
                    case 'optional' :
                        if(content == ''){
                            $(this).addClass('form-control-success').removeClass('form-control-danger');
                            $(this).parent().parent().addClass('has-success').removeClass('has-danger');
                            brk = true;
                        }
                        break;
                    //Si on a affaire à un required
                    case 'required' :
                        //Et que l'input est vide
                        if (content == '') {
                            $(this).removeClass('form-control-success').addClass('form-control-danger');
                            $(this).parent().parent().removeClass('has-success').addClass('has-danger');
                            //Break, car condition fausse
                            brk = true;
                            error = true;
                        }
                        else {
                            $(this).addClass('form-control-success').removeClass('form-control-danger');
                            $(this).parent().parent().addClass('has-success').removeClass('has-danger');
                        }
                        break;
                    case 'mail' :
                        //Si mail mal formaté (/!\ Verification relativement basique sur la forme seule de l'email
                        if (!validateEmail(content)) {
                            $(this).removeClass('form-control-success').addClass('form-control-danger');
                            $(this).parent().parent().removeClass('has-success').addClass('has-danger');
                            //Break, car condition fausse
                            brk = true;
                            error = true;
                        }
                        else {
                            $(this).addClass('form-control-success').removeClass('form-control-danger');
                            $(this).parent().parent().addClass('has-success').removeClass('has-danger');
                        }
                        break;
                    case 'integer' :
                        if(parseInt(content) == content){
                            $(this).addClass('form-control-success').removeClass('form-control-danger');
                            $(this).parent().parent().addClass('has-success').removeClass('has-danger');
                        }
                        else{
                            $(this).removeClass('form-control-success').addClass('form-control-danger');
                            $(this).parent().parent().removeClass('has-success').addClass('has-danger');
                            //Break, car condition fausse
                            brk = true;
                            error = true;
                        }
                        break;
                    case 'length' :
                        var length = parseInt($(this).attr('data-formManager-length'));
                        if(content.length == length){
                            $(this).addClass('form-control-success').removeClass('form-control-danger');
                            $(this).parent().parent().addClass('has-success').removeClass('has-danger');
                        }
                        else{
                            $(this).removeClass('form-control-success').addClass('form-control-danger');
                            $(this).parent().parent().removeClass('has-success').addClass('has-danger');
                            //Break, car condition fausse
                            brk = true;
                            error = true;
                        }
                        break;
                    case 'pureString' :
                        var regExp = new RegExp(/^[-a-zA-Zàáâãäåçèéêëìíîïðòóôõöùúûüýÿ ]*$/);
                        if(content.match(regExp)){
                            $(this).addClass('form-control-success').removeClass('form-control-danger');
                            $(this).parent().parent().addClass('has-success').removeClass('has-danger');
                        }
                        else{
                            $(this).removeClass('form-control-success').addClass('form-control-danger');
                            $(this).parent().parent().removeClass('has-success').addClass('has-danger');
                            //Break, car condition fausse
                            brk = true;
                            error = true;
                        }
                        break;
                    case 'sameAs' :
                        var contentToEqual = $('#'+$(this).attr('data-formManager-sameAs')).val();
                        console.log(content);
                        console.log(contentToEqual);
                        if(content == contentToEqual){
                            $(this).addClass('form-control-success').removeClass('form-control-danger');
                            $(this).parent().parent().addClass('has-success').removeClass('has-danger');
                        }
                        else{
                            $(this).removeClass('form-control-success').addClass('form-control-danger');
                            $(this).parent().parent().removeClass('has-success').addClass('has-danger');
                            //Break, car condition fausse
                            brk = true;
                            error = true;
                        }
                        break;
                    case 'float' :
                        if(parseFloat(content) == content){
                            $(this).addClass('form-control-success').removeClass('form-control-danger');
                            $(this).parent().parent().addClass('has-success').removeClass('has-danger');
                        }
                        else{
                            $(this).removeClass('form-control-success').addClass('form-control-danger');
                            $(this).parent().parent().removeClass('has-success').addClass('has-danger');
                            //Break, car condition fausse
                            brk = true;
                            error = true;
                        }
                        break;
                    default :
                        break;
                }
                if (brk)break;
            }
        });
        if(error){
            $('button[type=submit].formManager[data-formManager=submitInput]').attr('disabled', true);
        }
        else{
            $('button[type=submit].formManager[data-formManager=submitInput]').removeAttr('disabled');
        }
    });
});

function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}