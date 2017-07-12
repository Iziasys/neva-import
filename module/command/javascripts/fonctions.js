$(function() {
    /**
     * Au changement du sélect de la finition pour la visualisation des couleurs dispo
     */
    $('body').on('change', '#selectFinishForColor', function(){
        var finishId = parseInt($(this).val());

        $.ajax({
            url: getAppPath()+'/module/command/ajax/fetchColorForFinish.ajax.php',
            type: 'POST',
            dataType: 'html',
            data: {
                finishId: finishId
            },
            success: function(response){
                $('#receiverColorInformation').html(response);
            }
        });
    });

    /**
     * Au changement du sélect de la finition pour la visualisation des jantes dispo
     */
    $('body').on('change', '#selectFinishForRim', function(){
        var finishId = parseInt($(this).val());

        $.ajax({
            url: getAppPath()+'/module/command/ajax/fetchRimForFinish.ajax.php',
            type: 'POST',
            dataType: 'html',
            data: {
                finishId: finishId
            },
            success: function(response){
                $('#receiverRimInformation').html(response);
            }
        });
    });

    /**
     * Au changement du sélect de la finition pour la visualisation des packs dispo
     */
    $('body').on('change', '#selectFinishForPack', function(){
        var finishId = parseInt($(this).val());

        $.ajax({
            url: getAppPath()+'/module/command/ajax/fetchPackForFinish.ajax.php',
            type: 'POST',
            dataType: 'html',
            data: {
                finishId: finishId
            },
            success: function(response){
                $('#receiverPackInformation').html(response);
            }
        });
    });

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
     * Activation du slider de prix
     */
    $("#inputSelectPriceRange").slider({
        range: true,
        min: 0,
        max: 100000,
        step: 500,
        values: [0, 100000],
        slide: function(event, ui){
            $("#amount").val(number_format(ui.values[0], 0, '.', ' ')+"€ - "+number_format(ui.values[1], 0, '.', ' ')+"€");
        }
    });
    $("#amount").val($("#inputSelectPriceRange").slider("values", 0)+"€ - "+number_format($("#inputSelectPriceRange").slider("values", 1), 0, '.', ' ')+"€");

    /**
     * Fonction pour rafraichir la liste de véhicules disponibles
     */
    var refreshVehiclesCatalog = $.ajax({
        url: getAppPath()+'/module/command/ajax/fetchCatalogOfVehicles.ajax.php',
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
            url: getAppPath()+'/module/command/ajax/fetchCatalogOfVehicles.ajax.php',
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
     * Au changement de l'input de véhicule pour uploader une image
     */
    $('body').on('change', '#inputVehicleImageVehicle', function(){
        //On récupère l'ID du véhicule
        var vehicleId = parseInt($(this).val());

        $.ajax({
            url: getAppPath()+'/module/command/ajax/fetchVehicleImage.ajax.php',
            type: 'POST',
            dataType: 'html',
            data: {
                vehicleId: vehicleId
            },
            success: function(message){
                $('#receiverActualVehicleImage').html(message);
            }
        });
    });
    $('#inputVehicleImageVehicle').trigger('change');


    /**************GESTION DU SLIDER DE PRIX************/
    var marginValue = $('#receiverDefaultMargin').html();
    $('#calcSlider').slider({
        min: 0,
        max: 2000,
        step: 50,
        values: [marginValue],
        slide: function(event, ui){
            $("#marginAmount").val(ui.values[0]+" € HT");

            var postTaxesVehiclePrice = getPostTaxesvehiclePrice();
            refreshPostTaxesVehiclePrice(postTaxesVehiclePrice);
            refreshOptionedVehiclePrice();
            refreshSellingPrice();
        }
    });
    $("#marginAmount").val($("#calcSlider").slider("values", 0)+" € HT");

    /**
     * Retourne le prix du véhicule TTC
     *
     * @return float
     */
    function getPostTaxesvehiclePrice(){
        var vat = parseFloat($('#marginAmount').attr('data-vat'));
        var pretaxMarginAmount = parseFloat($('#marginAmount').val());
        var pretaxBasicPrice = parseFloat($('#receiverBasicPrice').html());

        var pretaxVehiclePrice = pretaxMarginAmount + pretaxBasicPrice;
        return convertToPostTaxes(pretaxVehiclePrice, vat);
    }

    function refreshPostTaxesVehiclePrice(amount){
        $('.vehiclePostTaxesPrice').html(number_format(amount, 2, '.', ' '));
    }

    /**
     * Au clic sur le bouton d'activation
     */
    $('body').on('click', '#bntCalc', function(){
        if($('#block-calc').hasClass('not-displayed')){
            $('#block-calc').removeClass('not-displayed');
        }
        else{
            $('#block-calc').addClass('not-displayed');
        }
    });
    /**************GESTION DU SLIDER DE PRIX************/


    /*****************Gestion de l'affichage du prix du véhicule à titre informatif sur la page de visualisation dans le catalogue**************/
    /**
     * A l'affichage de la page, on génère le prix des options (utile dans le cas où on fait un page prec
     */
    var optionAmount = calculateOptionPrice();
    $('#receiverOptionPrice').html(number_format(optionAmount, 2, '.', ' '));
    refreshOptionedVehiclePrice();
    refreshSellingPrice();
    /**
     * Au changement d'état d'une CB d'équipement
     * On va regarder si elle vient d'être cochée ou décochée afin d'ajouter ou de retrancher le montant au total
     */
    $('body').on('change', '.cb-optional-equipments', function(){
        var optionAmount = calculateOptionPrice();
        $('#receiverOptionPrice').html(number_format(optionAmount, 2, '.', ' '));
        refreshOptionedVehiclePrice();
        refreshSellingPrice();
    });

    /**
     * Même fonctionnement qu'au dessus pour les packs
     */
    $('body').on('change', '.cb-packs', function(){
        var optionAmount = calculateOptionPrice();
        $('#receiverOptionPrice').html(number_format(optionAmount, 2, '.', ' '));
        refreshOptionedVehiclePrice();
        refreshSellingPrice();
    });
    /**
     * Idem pour la couleur
     */
    $('body').on('change', '#selectVehicleColor', function(){
        var optionAmount = calculateOptionPrice();
        $('#receiverOptionPrice').html(number_format(optionAmount, 2, '.', ' '));
        refreshOptionedVehiclePrice();
        refreshSellingPrice();
    });
    /**
     * Idem pour les jantes
     */
    $('body').on('change', '#selectVehicleRims', function(){
        var optionAmount = calculateOptionPrice();
        $('#receiverOptionPrice').html(number_format(optionAmount, 2, '.', ' '));
        refreshOptionedVehiclePrice();
        refreshSellingPrice();
    });

    function calculateOptionPrice(){
        var amount = 0;
        $('.cb-optional-equipments:checked').each(function(){
            amount += parseFloat($(this).attr('data-price'));
        });
        $('.cb-packs:checked').each(function(){
            amount += parseFloat($(this).attr('data-price'));
        });
        amount += parseFloat($('#selectVehicleColor > option:selected').attr('data-price'));
        amount += parseFloat($('#selectVehicleRims > option:selected').attr('data-price'));

        return amount;
    }

    function refreshOptionedVehiclePrice(){
        var vehiclePrice = getPostTaxesvehiclePrice();
        var optionPrice = calculateOptionPrice();
        $('.receiverOptionedVehiclePrice').html(number_format((vehiclePrice + optionPrice), 2, '.', ' '));
    }

    function refreshSellingPrice(){
        var vehiclePrice = getPostTaxesvehiclePrice();
        var optionPrice = calculateOptionPrice();
        var packageProvision = parseInt($('#packageProvision').html());
        //var freightCharges = parseInt($('#freightCharges').html());

        var sellingPrice = vehiclePrice + optionPrice + packageProvision;

        $('.receiverSellingPrice').html(number_format(sellingPrice, 2, '.', ' '));
    }
    /*****************Gestion de l'affichage du prix du véhicule à titre informatif sur la page de visualisation dans le catalogue**************/

    $('body').on('change', '.radio-for-isSociety', function(){
        var isSociety = parseInt($('.radio-for-isSociety:checked').val());
        if(isSociety){
            $('.rowForSociety').css({
                display: 'block'
            });
            $('#inputClientName').attr('required', true);
        }
        else{
            $('.rowForSociety').css({
                display: 'none'
            });
            $('#inputClientName').attr('required', false);
        }
    });

    $('.radio-for-isSociety').trigger('change');

    /**
     * Au choix d'un client, si celui-ci n'est pas null, desactivation du formulaire de création
     */
    $('body').on('change', '#inputSelectClient', function(){
        var clientId = $(this).val();
        if(clientId != 0){
            $('#formCreateClient input').attr('disabled', true);
        }
        else{
            $('#formCreateClient input').attr('disabled', false);
        }
    });

    $('body').on('click', '#btnDetails', function(){
        var $detailsRow = $('.detailsRow');
        if($detailsRow.hasClass('invisible')){
            $detailsRow.removeClass('invisible');
            $detailsRow.css({
                display: 'block'
            });
            $detailsRow.addClass('row');
            $detailsRow.addClass('striped');
        }
        else{
            $detailsRow.addClass('invisible');
            $detailsRow.css({
                display: 'none'
            });
            $detailsRow.removeClass('row');
            $detailsRow.removeClass('striped');
        }
    });

    /**
     * Au clic sur le bouton de suppression d'un véhicule
     */
    $('body').on('click', '.btnAskDeleteVehicle', function(){
        //On récupère l'ID de ce véhicule
        var finishId = parseInt($(this).attr('data-finishId'));

        //Puis l'écrit dans l'input du modal
        $('#inputFinishId').val(finishId);
    });

    /**
     * A la validation de suppression d'une finition
     */
    $('body').on('click', '#btnConfirmDeleteFinish', function(){
        var finishId = parseInt($('#inputFinishId').val());
        //Si on a bien une finition de renseignée
        if(finishId != 0){
            document.location.href = getAppPath()+'/commande-vehicules/vehicule-generique/supprimer/'+finishId;
        }
        //Sinon
        else{

        }
    });

    /**
     * A la validation de suppression d'un véhicule
     */
    $('body').on('click', '#btnConfirmDeleteVehicle', function(){
        var vehicleId = parseInt($('#inputFinishId').val());
        //Si on a bien une finition de renseignée
        if(vehicleId != 0){
            document.location.href = getAppPath()+'/commande-vehicules/vehicule/supprimer/'+vehicleId;
        }
        //Sinon
        else{

        }
    });
});