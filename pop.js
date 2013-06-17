jQuery(document).ready(function($){
    var l = parseInt(popup['image'][1]+32),
    h = parseInt(popup['image'][2]+32);
    var $chocopop = $('<div class="overlay" id="overlay"></div><a href="' + popup['lien'] + '" class="choco-lien" id="choco-lien" style="margin-left:-' + l/2 + 'px;margin-top:-' + h/2 + 'px;"><img src="' + popup['image'][0] + '" id="choco-image" class="choco-image"><button id="choco-close">x</button></a>');
    $('body').append($chocopop);

    $(document).on('click', '#choco-close, #overlay', function(e){
        e.preventDefault();
        $('#choco-lien, #overlay').remove();
    });

    //size change
    $(window).on('resize',testsize);
    function testsize(){
        if($(this).width() < l )
            $('#choco-lien').addClass('petit-large');
        else
            $('#choco-lien').removeClass('petit-large');
        if($(this).height() < h )
            $('#choco-lien').addClass('petit-haut');
        else
            $('#choco-lien').removeClass('petit-haut');
    }
    testsize();
});