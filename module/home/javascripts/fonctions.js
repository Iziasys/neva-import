$(function() {
    /**
     * Au clic sur un item du tableau de bord
     */
    $('body').on('click', '.board-item', function(){
        document.location.href = $(this).attr('data-href');
    });
});