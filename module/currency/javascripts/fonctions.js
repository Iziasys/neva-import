$(function(){
    /**************************GESTION DES DEVISES*********************/
    //Au changement de l'input de symbole, on actualise les receiver
    $('body').on('change', '#inputCurrencySymbol', function(){
        $('.receiverCurrency').html($(this).val());
    });

    //Taux de change : Quand on modifie une des input, l'autre prend sa valeure inverse
    $('body').on('focusout', '#inputCurrencyFromEuro', function(){
        var rate = parseFloat($(this).val());
        console.log(rate);

        var inverseRate = 1/rate;

        $('#inputCurrencyToEuro').val(inverseRate);
        $('#inputCurrencyToEuro').trigger('change');
    });
    $('body').on('focusout', '#inputCurrencyToEuro', function(){
        var rate = parseFloat($(this).val());

        var inverseRate = 1/rate;

        $('#inputCurrencyFromEuro').val(inverseRate);
        $('#inputCurrencyFromEuro').trigger('change');
    });
    /**************************GESTION DES DEVISES*********************/
});