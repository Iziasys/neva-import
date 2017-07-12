$(function(){
    /**
     * Au focus sur un objet de class typeAhead, on affiche le dropdown correspondant
     * Si le contenu du dropdown est généré via un appel AJAX, alors on fait l'appel nécessaire
     */
    $(document).on('focusin', '.typeAhead', function(){
        refreshTypeAhead($(this));
    });

    $(document).on('keyup', '.typeAhead', function(){
        refreshTypeAhead($(this));
    });

    /**
     * Rafraichit le contenu du dropdown correspondant au typeahead visité
     *
     * @param clickedItem
     */
    function refreshTypeAhead(clickedItem){
        //Si le contenu du dropdown est à afficher avec un appel ajax
        if(clickedItem.hasClass('typeAhead-ajax')){
            //Alors on execute cet appel ajax
            var domPath = clickedItem.attr('data-typeAhead-ajax');
            var splitedDomPath = domPath.split('/');
            var module = splitedDomPath[0];
            var file = splitedDomPath[1];
            var pathToAjax = getAppPath()+'/module/'+module+'/ajax/'+file+'.ajax.php';

            var targetedDropdown = clickedItem.attr('data-typeAhead-dropdown');
            $('#'+targetedDropdown).css({'display':'block'});

            var args = '';
            var hasArgs = clickedItem.attr('data-typeAhead-argument');
            if(typeof hasArgs !== typeof undefined && hasArgs !== false){
                var argsList = clickedItem.attr('data-typeAhead-argument').split(' ');
                for(var arg in argsList){
                    if(arg > 0){
                        args += '---';
                    }
                    var argDetails = argsList[arg].split(':');
                    var argName = argDetails[0];
                    var argValue = argDetails[1];
                    args += argName+':'+ $('#'+argValue).val()+'';
                }
            }

            $.ajax({
                url: pathToAjax,
                type: 'POST',
                dataType: 'html',
                data: {
                    args : args
                },
                success: function(response){
                    $('#'+targetedDropdown).html(response);
                    $('#'+targetedDropdown).dropdown('toggle');
                }
            })
        }
    }

    $(document).on('focusout', '.typeAhead', function(){
        var targetedDropdown = $(this).attr('data-typeAhead-dropdown');

        setTimeout(function(){
            $('#'+targetedDropdown).css({'display':'none'});
        }, 200);
    });

    /**
     * Au clic sur un bouton du dropdown pour le typeAhead
     * On va récupérer sa valeur et l'insérer dans l'input correspondante
     */
    $('.typeAhead-dropdown').on('click', '.typeAhead-dropdown-item', function(){
        var value = $(this).attr('data-typeAhead-value');
        var parentId = $(this).parent().attr('id');
        var matchingInput = getElementsByAttribute(document, 'input', 'data-typeAhead-dropdown', parentId);
        $(matchingInput).val(value);
    });













});