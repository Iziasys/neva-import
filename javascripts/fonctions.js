function getAppPath(){return '';}

/*
 Copyright Robert Nyman, http://www.robertnyman.com
 Free to use if this text is included
 */
function getElementsByAttribute(oElm, strTagName, strAttributeName, strAttributeValue){
    var arrElements = (strTagName == "*" && oElm.all)? oElm.all : oElm.getElementsByTagName(strTagName);
    var arrReturnElements = new Array();
    var oAttributeValue = (typeof strAttributeValue != "undefined")? new RegExp("(^|\\s)" + strAttributeValue + "(\\s|$)") : null;
    var oCurrent;
    var oAttribute;
    for(var i=0; i<arrElements.length; i++){
        oCurrent = arrElements[i];
        oAttribute = oCurrent.getAttribute && oCurrent.getAttribute(strAttributeName);
        if(typeof oAttribute == "string" && oAttribute.length > 0){
            if(typeof strAttributeValue == "undefined" || (oAttributeValue && oAttributeValue.test(oAttribute))){
                arrReturnElements.push(oCurrent);
            }
        }
    }
    return arrReturnElements;
}

function number_format(number, decimals, dec_point, thousands_sep) {
    //  discuss at: http://phpjs.org/functions/number_format/
    // original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: davook
    // improved by: Brett Zamir (http://brett-zamir.me)
    // improved by: Brett Zamir (http://brett-zamir.me)
    // improved by: Theriault
    // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // bugfixed by: Michael White (http://getsprink.com)
    // bugfixed by: Benjamin Lupton
    // bugfixed by: Allan Jensen (http://www.winternet.no)
    // bugfixed by: Howard Yeend
    // bugfixed by: Diogo Resende
    // bugfixed by: Rival
    // bugfixed by: Brett Zamir (http://brett-zamir.me)
    //  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    //  revised by: Luke Smith (http://lucassmith.name)
    //    input by: Kheang Hok Chin (http://www.distantia.ca/)
    //    input by: Jay Klehr
    //    input by: Amir Habibi (http://www.residence-mixte.com/)
    //    input by: Amirouche
    //   example 1: number_format(1234.56);
    //   returns 1: '1,235'
    //   example 2: number_format(1234.56, 2, ',', ' ');
    //   returns 2: '1 234,56'
    //   example 3: number_format(1234.5678, 2, '.', '');
    //   returns 3: '1234.57'
    //   example 4: number_format(67, 2, ',', '.');
    //   returns 4: '67,00'
    //   example 5: number_format(1000);
    //   returns 5: '1,000'
    //   example 6: number_format(67.311, 2);
    //   returns 6: '67.31'
    //   example 7: number_format(1000.55, 1);
    //   returns 7: '1,000.6'
    //   example 8: number_format(67000, 5, ',', '.');
    //   returns 8: '67.000,00000'
    //   example 9: number_format(0.9, 0);
    //   returns 9: '1'
    //  example 10: number_format('1.20', 2);
    //  returns 10: '1.20'
    //  example 11: number_format('1.20', 4);
    //  returns 11: '1.2000'
    //  example 12: number_format('1.2000', 3);
    //  returns 12: '1.200'
    //  example 13: number_format('1 000,50', 2, '.', ' ');
    //  returns 13: '100 050.00'
    //  example 14: number_format(1e-8, 8, '.', '');
    //  returns 14: '0.00000001'

    number = (number + '')
        .replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
            var k = Math.pow(10, prec);
            return '' + (Math.round(n * k) / k)
                    .toFixed(prec);
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
        .split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '')
            .length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1)
            .join('0');
    }
    return s.join(dec);
}

$(function() {
    $('.loadWhenReady').css({
        display : 'block'
    });

    $('body').on('click', 'tr.clickable', function(){
        var link = $(this).attr('data-href');
        if(link != '' && link != undefined) {
            if($(this).hasClass('_blank')) {
                window.open(link);
            }
            else{
                document.location.href = link;
            }
        }
    });

    /**
     * A la validation du modal, deux choix.
     * Soit on a un client sélectionné, auquel cas on le stock dans la session
     * Soit on a un client renseigné dans le form, auquel cas on l'insère en base puis on stock dans la session
     */
    $('body').on('click', '#btnSelectClient', function(){
        var clientId = $('#inputSelectClient').val();

        //Si client sélectionné
        if(clientId != 0){
            $.ajax({
                url: getAppPath()+'/ressources/ajax/selectClient.ajax.php',
                type: 'POST',
                dataType: 'html',
                data: {
                    clientId: clientId
                },
                success: function(message){
                    if(message == 'ok') {
                        $('.select-client-modal-lg').modal('toggle');
                        $('#formCreateOffer').submit();
                    }
                }
            });
        }
        //Si pas de client sélectionné
        else{
            var isSociety = $('input[name=createClientSociety]:checked').val();
            var societyName = $('#createClient_societyName').val();
            var civility = $('input[name=createClientCivility]:checked').val();
            var lastName = $('#createClient_lastName').val();
            var firstName = $('#createClient_firstName').val();
            var email = $('#createClient_email').val();
            var acceptOffers = $('input[name=createClientAcceptOffers]:checked').val();

            //On va tenter de l'insérer en base
            $.ajax({
                url: getAppPath()+'/ressources/ajax/insertClient.ajax.php',
                type: 'POST',
                dataType: 'html',
                data: {
                    isSociety: isSociety,
                    societyName: societyName,
                    civility: civility,
                    lastName: lastName,
                    firstName: firstName,
                    email: email,
                    acceptOffers: acceptOffers
                },
                success: function(message){
                    if(message == 'ok'){
                        $('.select-client-modal-lg').modal('toggle');
                        $('#formCreateOffer').submit();
                    }
                }
            });
        }
    });
});

function convertToPostTaxes(price, vatAmount){
    return price * (1 + (vatAmount / 100));
}