$(function() {

    /**
     * Quand on appuis sur une touche du clavier en étant focus sur l'input des équipements
     */
    $('body').on('keyup', '#inputVehicleEquipments', function(event){
        var inputContent = $(this).val();
        //On regarde si il y a un ';' dans la chaine entrée
        if(inputContent.indexOf(';') > -1){
            var splitContent = inputContent.split(';');
            var formName = $(this).attr('data-formName');

            for(var item in splitContent){
                if(splitContent[item] != '' && splitContent[item] != ' ') {
                    $('#receiverVehicleEquipments').append(
                        '<div class="equipment-item">' +
                        '<input type="hidden" name="' + formName + '[equipments][]" value="' + splitContent[item] + '">' +
                        '<div class="equipment-item-itemName">' +
                        '' + splitContent[item] + '' +
                        '</div>' +
                        '<div class="equipment-item-delete text-danger">X</div>' +
                        '</div>'
                    );
                }
            }
            $(this).val('');
        }
    });

    /**
     * Au clic sur un des bouton de suppression d'un item
     */
    $('body').on('click', '.equipment-item-delete', function(){
        $(this).parent().remove();
    });
    $('#inputVehicleEquipments').trigger('keyup');

    $('.carousel').carousel({
        interval: 10000,
        pause: 'hover'
    });

    $('body').on('click', '#btnUnreserveVehicle', function(event){
        event.preventDefault();
        var vehicleId = parseInt($(this).parent().parent().attr('data-vehicleId'));

        $.ajax({
            url: getAppPath()+'/module/stockVehicle/ajax/unreserveVehicle.ajax.php',
            type: 'POST',
            data: {
                vehicleId: vehicleId
            },
            success: function(response){
                console.log(response);
                if(response == 'success') {
                    window.location = $('a#btnUnreserveVehicle').attr('href');
                }
                else{

                }
            }
        });
    });

    $('body').on('click', '.btnSellVehicle', function(){
        event.preventDefault();
        var vehicleId = parseInt($(this).parent().parent().attr('data-vehicleId'));

        $.ajax({
            url: getAppPath()+'/module/stockVehicle/ajax/sellVehicle.ajax.php',
            type: 'POST',
            data: {
                vehicleId: vehicleId
            },
            success: function(response){
                console.log(response);
                if(response == 'success') {
                    window.location = $('a.btnSellVehicle').attr('href');
                }
                else{

                }
            }
        });
    });

    /**
     * Au clic sur le bouton d'activation
     */
    $('body').on('click', '#bntCalc', function(){
        var vehicleId = parseInt($('#marginAmount').attr('data-vehicleId'));
        var margin = parseFloat($('#marginAmount').val());
        $.ajax({
            url: getAppPath() + '/module/stockVehicle/ajax/defineStructureMargin.ajax.php',
            type: 'POST',
            data: {
                vehicleId: vehicleId,
                margin: margin
            }
        });
        if($('#block-calc').hasClass('not-displayed')){
            $('#block-calc').removeClass('not-displayed');
        }
        else{
            $('#block-calc').addClass('not-displayed');
        }
    });
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

            var vehicleId = parseInt($('#marginAmount').attr('data-vehicleId'));

            var hydrateDatabase = $.ajax({
                url: getAppPath()+'/module/stockVehicle/ajax/defineStructureMargin.ajax.php',
                type: 'POST',
                data:{
                    vehicleId: vehicleId,
                    margin: ui.values[0]
                }
            });
        }
    });
    $("#marginAmount").val($("#calcSlider").slider("values", 0)+" € HT");

    $('body').on('mouseleave', '#calcSlider', function(){
        var vehicleId = parseInt($('#marginAmount').attr('data-vehicleId'));
        var margin = parseFloat($('#marginAmount').val());
        $.ajax({
            url: getAppPath() + '/module/stockVehicle/ajax/defineStructureMargin.ajax.php',
            type: 'POST',
            data: {
                vehicleId: vehicleId,
                margin: margin
            }
        });
    });

    /**
     * Retourne le prix du véhicule TTC
     *
     * @return float
     */
    function getPostTaxesvehiclePrice(){
        var vat = parseFloat($('#marginAmount').attr('data-vat'));
        var pretaxMarginAmount = parseFloat($('#marginAmount').val());
        var pretaxBasicPrice = parseFloat($('#receiverBasicPrice').html());
        var packageProvision = parseFloat($('#receiverPackageProvision').html());

        var pretaxVehiclePrice = pretaxMarginAmount + pretaxBasicPrice + packageProvision;
        return convertToPostTaxes(pretaxVehiclePrice, vat);
    }

    function refreshPostTaxesVehiclePrice(amount){
        $('.vehiclePostTaxesPrice').html(number_format(amount, 2, '.', ' '));
    }

    /***********************DEFINITION DE LA RECHERCHE DE VEHICULES*********************/
    /**
     * Au clic sur une des deux CB de choix VN/VO
     */
    $('body').on('click', '#acceptNewVehicles', function(){
        refreshCatalog('#receiverStockCatalog');
    });
    $('body').on('click', '#acceptUsedVehicles', function(){
        refreshCatalog('#receiverStockCatalog');
    });

    function refreshCatalog(receiver){
        var acceptNewVehicles = $('#acceptNewVehicles').is(':checked');
        var acceptUsedVehicles = $('#acceptUsedVehicles').is(':checked');

        $.ajax({
            url: getAppPath()+'/module/stockVehicle/ajax/fetchCatalogOfVehicles.ajax.php',
            type: 'POST',
            data:{
                acceptNewVehicles: acceptNewVehicles,
                acceptUsedVehicles: acceptUsedVehicles
            },
            success: function(message){
                $(receiver).html(message);
            }
        });
    }
    /***********************DEFINITION DE LA RECHERCHE DE VEHICULES*********************/
});