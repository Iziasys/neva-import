$(function() {
    /**
     * Activation du multiselect pour le choix de la marque
     */
    $('#inputSelectBrandForCatalog').multiselect({
        buttonWidth:'100%',
        enableFiltering: true,
        nonSelectedText: 'Choisissez une marque',
        numberDisplayed: 2,
        enableCaseInsensitiveFiltering: true,
        maxHeight:300
    });

    /**
     * Activation du multiselect pour le choix du modèle
     */
    $('#inputSelectModelForCatalog').multiselect({
        buttonWidth:'100%',
        enableFiltering: true,
        nonSelectedText: 'Choisissez un modèle',
        numberDisplayed: 2,
        enableCaseInsensitiveFiltering: true,
        maxHeight:300
    });

    /**
     * Activation du multiselect pour le choix de la finition
     */
    $('#inputSelectFinishForCatalog').multiselect({
        buttonWidth:'100%',
        enableFiltering: true,
        nonSelectedText: 'Choisissez une finition',
        numberDisplayed: 2,
        enableCaseInsensitiveFiltering: true,
        maxHeight:300
    });

    /**
     * Activation du multiselect pour le choix de la carrosserie
     */
    $('#inputSelectBodyworkForCatalog').multiselect({
        buttonWidth:'100%',
        enableFiltering: true,
        nonSelectedText: 'Choisissez une carrosserie',
        numberDisplayed: 2,
        enableCaseInsensitiveFiltering: true,
        maxHeight:300
    });

    /**
     * Activation du multiselect pour le choix du carburant
     */
    $('#inputSelectFuelForCatalog').multiselect({
        buttonWidth:'100%',
        enableFiltering: true,
        nonSelectedText: 'Choisissez un carburant',
        numberDisplayed: 2,
        enableCaseInsensitiveFiltering: true,
        maxHeight:300
    });

    /**
     * Activation du multiselect pour le choix de la Boite de vitesse
     */
    $('#inputSelectGearboxForCatalog').multiselect({
        buttonWidth:'100%',
        enableFiltering: true,
        nonSelectedText: 'Choisissez une boite de vitesse',
        numberDisplayed: 2,
        enableCaseInsensitiveFiltering: true,
        maxHeight:300
    });

    /**
     * Au changement du sélect de la marque on va aller chercher la liste des modèles correspondants
     */
    $('body').on('change', '#inputSelectBrandForCatalog', function(){
        var brandIds = $(this).val();
        //Refresh de la liste de modèles
        $.ajax({
            url: getAppPath()+'/module/command/ajax/fetchModelListForCatalog.ajax.php',
            type: 'POST',
            dataType: 'html',
            data: {
                brandIds: brandIds
            },
            success: function(response){
                $('#inputSelectModelForCatalog').html(response);
                $('#inputSelectModelForCatalog').multiselect('rebuild');
            }
        });

        //Et on rafraichit le catalogue de véhicules
        refreshCatalog();
    });

    /**
     * Au changement du sélect du modèle, on va aller chercher la liste des finitions correspondantes
     */
    $('body').on('change', '#inputSelectModelForCatalog', function(){
        var modelIds = $(this).val();
        $.ajax({
            url: getAppPath()+'/module/command/ajax/fetchFinishListForCatalog.ajax.php',
            type: 'POST',
            dataType: 'html',
            data: {
                modelIds: modelIds
            },
            success: function(response){
                $('#inputSelectFinishForCatalog').html(response);
                $('#inputSelectFinishForCatalog').multiselect('rebuild');
            }
        });

        //Et on rafraichit le catalogue de véhicules
        refreshCatalog();
    });

    /**
     * Quand on change la finition
     */
    $('body').on('change', '#inputSelectFinishForCatalog', function(){
        refreshCatalog();
    });
    /**
     * Quand on change la carrosserie
     */
    $('body').on('change', '#inputSelectBodyworkForCatalog', function(){
        refreshCatalog();
    });
    /**
     * Quand on change le carburant
     */
    $('body').on('change', '#inputSelectFuelForCatalog', function(){
        refreshCatalog();
    });
    /**
     * Quand on change la boite de vitesse
     */
    $('body').on('change', '#inputSelectGearboxForCatalog', function(){
        refreshCatalog();
    });

    /**
     * Fonction pour rafraichir la liste de véhicules disponibles
     */
    var refreshVehiclesCatalog = $.ajax({
        url: getAppPath()+'/module/prices/ajax/fetchCatalogOfVehicles.ajax.php',
        type: 'POST',
        dataType: 'html',
        success: function(response){
            $('#vehicles-catalog').html(response);
        }
    });

    /**
     * Fonction pour rafraichir le catalogue de véhicules selon les critères entrés
     */
    function refreshCatalog(){
        refreshVehiclesCatalog.abort();

        var brandsIds = $('#inputSelectBrandForCatalog').val();
        var modelsIds = $('#inputSelectModelForCatalog').val();
        var finishesIds = $('#inputSelectFinishForCatalog').val();
        var bodyworkIds = $('#inputSelectBodyworkForCatalog').val();
        var fuelsIds = $('#inputSelectFuelForCatalog').val();
        var gearboxesIds = $('#inputSelectGearboxForCatalog').val();

        refreshVehiclesCatalog = $.ajax({
            url: getAppPath()+'/module/prices/ajax/fetchCatalogOfVehicles.ajax.php',
            type: 'POST',
            dataType: 'html',
            data: {
                brandsIds: brandsIds,
                modelsIds: modelsIds,
                finishesIds: finishesIds,
                bodyworkIds: bodyworkIds,
                fuelsIds: fuelsIds,
                gearboxesIds: gearboxesIds
            },
            success: function(response){
                $('#vehicles-catalog').html(response);
            }
        });
    }

    /**
     * Au clic sur une ligne pour afficher le détail du prix
     */
    $('body').on('click', '.tr-togle-price-details-modal', function(){
        var vehicleId = parseInt($(this).attr('data-vehicleId'));

        //On actualise d'abord le modal de prix en fct du véhicule cliqué
        $.ajax({
            url: getAppPath()+'/module/prices/ajax/fetchPriceDetails.ajax.php',
            type: 'POST',
            dataType: 'html',
            data: {
                vehicleId: vehicleId
            },
            success: function(response){
                $('.prices-details-modal-lg .modal-body').html(response);
            }
        });
    });

    /**
     * Quand on demande la modification des frais de gestion (commission) d'un véhicule
     */
    $('body').on('click', '#btnModifyManagementFees', function(){
        var vehicleId = parseInt($('input[type=hidden]#inputVehicleId').val());
        var managementFees = parseFloat($('#inputModifyManagementFees').val());

        $.ajax({
            url: getAppPath()+'/module/prices/ajax/modifyManagementFees.ajax.php',
            type: 'POST',
            dataType: 'html',
            data: {
                vehicleId: vehicleId,
                managementFees: managementFees
            },
            success: function(msgReturn){
                if(msgReturn == 'error'){
                    $('.prices-details-modal-lg .modal-body').html('Une erreur est survenue. Contactez votre administrateur.');
                }
                else {
                    $.ajax({
                        url: getAppPath() + '/module/prices/ajax/fetchPriceDetails.ajax.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {
                            vehicleId: vehicleId
                        },
                        success: function (response) {
                            $('.prices-details-modal-lg .modal-body').html(response);
                        }
                    });
                }
            }
        });
    });

    /**
     * Quand on demande la modification de la marge
     */
    $('body').on('click', '.btnModifyMargin', function(){
        //On va d'abord regarder quel bouton on a cliqué
        var clickedButton = $(this).attr('id');
        var modifyType;
        switch(clickedButton){
            case 'modifyMarginForVehicle' :
                modifyType = 'vehicle';
                break;
            case 'modifyMarginForFinish' :
                modifyType = 'finish';
                break;
            case 'modifyMarginForModel' :
                modifyType = 'model';
                break;
            default : break;
        }

        var vehicleId = parseInt($('input[type=hidden]#inputVehicleId').val());
        var margin = parseFloat($('#inputModifyMargin').val());

        $.ajax({
            url: getAppPath()+'/module/prices/ajax/modifyMargin.ajax.php',
            type: 'POST',
            dataType: 'html',
            data: {
                vehicleId: vehicleId,
                margin: margin,
                modifyType: modifyType
            },
            success: function(msgReturn){
                if(msgReturn == 'error'){
                    $('.prices-details-modal-lg .modal-body').html('Une erreur est survenue. Contactez votre administrateur.');
                }
                else {
                    $.ajax({
                        url: getAppPath() + '/module/prices/ajax/fetchPriceDetails.ajax.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {
                            vehicleId: vehicleId
                        },
                        success: function (response) {
                            $('.prices-details-modal-lg .modal-body').html(response);
                        }
                    });
                }
            }
        });
    });
});