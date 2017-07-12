$(function() {
    /**
     * Au clic sur le bouton de validation de changement de montant fixe pour la CG
     */
    $('body').on('click', '#btnModifyFixAmountForRC', function(){
        var newAmount = parseFloat($('#inputFixAmountForRC').val());

        $.ajax({
            url: getAppPath()+'/module/horsepower/ajax/modifyFixAmountForRC.ajax.php',
            type: 'POST',
            data: {
                newAmount: newAmount
            },
            success: function(message){
                if(message == 'ok'){
                    $('#alertsReceiver').html('' +
                        '<div class="alert alert-success alert-dismissible fade in" role="alert">'+
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Fermer">'+
                        '<span aria-hidden="true">&times;</span>'+
                    '</button>' +
                        'Modification du montant effectuée avec succès</div>');
                }
                else{
                    $('#alertsReceiver').html('' +
                        '<div class="alert alert-danger alert-dismissible fade in" role="alert">'+
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Fermer">'+
                        '<span aria-hidden="true">&times;</span>'+
                        '</button>' +
                        message+'</div>');
                }
            }
        });
    });
});