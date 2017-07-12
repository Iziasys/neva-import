$(function() {
    /**
     * Définition de l'appel AJAX pour rafraichir la liste des offres dispo
     */
    var askedState = parseInt($('#askedState').val());
    var acceptNewVehicles = $('#acceptNewVehicles').is(':checked');
    var acceptUsedVehicles = $('#acceptUsedVehicles').is(':checked');
    var refreshVehiclesList = $.ajax({
        url: getAppPath()+'/module/offers/ajax/fetchOffers.ajax.php',
        type: 'POST',
        data: {
            askedState: askedState,
            acceptNewVehicles: acceptNewVehicles,
            acceptUsedVehicles: acceptUsedVehicles
        },
        success: function(message){
            $('#receiverOffersList').html(message);
        }
    });
    refreshVehiclesList.abort();
    refreshOffersList();

    /**
     * Au changement d'état d'une des CB de choix de type de véhicule
     */
    $('body').on('change', '#acceptNewVehicles', function(){
        refreshOffersList();
    });
    $('body').on('change', '#acceptUsedVehicles', function(){
        refreshOffersList();
    });

    function refreshOffersList(){
        refreshVehiclesList.abort();
        $('#receiverOffersList').html('<i class="fa fa-spinner fa-spin"></i>')
        askedState = parseInt($('#askedState').val());
        acceptNewVehicles = $('#acceptNewVehicles').is(':checked');
        acceptUsedVehicles = $('#acceptUsedVehicles').is(':checked');

        refreshVehiclesList = $.ajax({
            url: getAppPath()+'/module/offers/ajax/fetchOffers.ajax.php',
            type: 'POST',
            data: {
                askedState: askedState,
                acceptNewVehicles: acceptNewVehicles,
                acceptUsedVehicles: acceptUsedVehicles
            },
            success: function(message){
                $('#receiverOffersList').html(message);
            }
        });
    }

    /**
     * Au clic sur le bouton de suppression d'une offre de véhicule en commande
     */
    $('body').on('click', '.btn-cancel-command-offer', function(clickedLink){
        clickedLink.preventDefault();
        var offerReference = $(this).attr('data-offerReference');
        $.ajax({
            url: getAppPath()+'/module/offers/ajax/cancelCommandOffer.ajax.php',
            type: 'POST',
            data: {
                offerReference: offerReference
            },
            success: function(){
                location.reload(true);
            }
        })
    });

    /**
     * Au clic sur le bouton de validation d'un bon de commande de véhicule en commande
     */
    $('body').on('click', '.btn-validate-command-orderForm', function(clickedLink){
        clickedLink.preventDefault();
        var offerReference = $(this).attr('data-offerReference');
        $.ajax({
            url: getAppPath()+'/module/offers/ajax/validateCommandOrderForm.ajax.php',
            type: 'POST',
            data: {
                offerReference: offerReference
            },
            success: function(){
                window.location.href = '/bon-a-uploader';
            }
        })
    });

    /**
     * Au clic sur le bouton de validation d'un bon de commande de véhicule en stock
     */
    $('body').on('click', '.btn-validate-stock-orderForm', function(clickedLink){
        clickedLink.preventDefault();
        var offerReference = $(this).attr('data-offerReference');
        $.ajax({
            url: getAppPath()+'/module/offers/ajax/validateStockOrderForm.ajax.php',
            type: 'POST',
            data: {
                offerReference: offerReference
            },
            success: function(){
                window.location.href = '/bon-a-uploader';
            }
        })
    });
});