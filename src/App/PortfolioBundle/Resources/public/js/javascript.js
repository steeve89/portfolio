!function ($) {

    $(function(){

        var $window = $(window);
        
        //function replace targetblank for valid w3c
        $('a.targetblank').on('click', function() {
             window.open($(this).attr('href'));
             return false;
        });
        // Datepicker
        $.datepicker.regional['en'] = {
            closeText: 'Close',
            prevText: '<',
            nextText: '>',
            currentText: 'Today',
            dateFormat: 'dd/mm/yy',
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['en']);
        
        $('.datepicker').datepicker({
            inline: true
        });
        
        // Dropdowns  via JavaScript
        if ($('.dropdown-toggle').size()) $('.dropdown-toggle').dropdown();
        
        // ScrollSpy 
        //if ($('#myNav').size()) $('#myNav').scrollspy();
        
        // Carousel
        $('.carousel').carousel();

        /* Formulaire imbriqués */
        // Récupère le div qui contient la collection de tags
        var collectionHolder = $('ul.collections');
        if (collectionHolder.size()){
            // ajoute un lien de suppression à tous les éléments li
            collectionHolder.find('li').each(function() {
                addItemFormDeleteLink($(this));
            });
            // ajoute un lien « add a link »
            var $addItemLink = $('<a href="#" class="btn btn-inverse">Ajouter</a>');
            var $newLinkLi = $('<li></li>').append($addItemLink);        
            // ajoute l'ancre « ajouter un tag » et li à la balise ul
            collectionHolder.append($newLinkLi);
            
            addItemForm(collectionHolder, $newLinkLi);

            $addItemLink.on('click', function(e) {
                // empêche le lien de créer un « # » dans l'URL
                e.preventDefault();

                // ajoute un nouveau formulaire tag (voir le prochain bloc de code)
                addItemForm(collectionHolder, $newLinkLi);
            });                    
        }
        
        function addItemForm(collectionHolder, $newLinkLi) {
            // Récupère l'élément ayant l'attribut data-prototype comme expliqué plus tôt
            var prototype = collectionHolder.attr('data-prototype');

            // Remplace '__name__' dans le HTML du prototype par un nombre basé sur
            // la longueur de la collection courante
            var newForm = prototype.replace(/__name__/g, collectionHolder.children().length);

            // Affiche le formulaire dans la page dans un li, avant le lien "ajouter un tag"
            var $newFormLi = $('<li></li>').append(newForm);
            $newLinkLi.before($newFormLi);
            
            // ajoute un lien de suppression au nouveau formulaire
            addItemFormDeleteLink($newFormLi);
        }
        
        function addItemFormDeleteLink($itemFormLi) {
            var $removeFormA = $('<a href="#" class="removeItem btn btn-danger">Supprimer</a>');
            $itemFormLi.find('.controls').append($removeFormA);

            $removeFormA.on('click', function(e) {
                // empêche le lien de créer un « # » dans l'URL
                e.preventDefault();

                // supprime l'élément li pour le formulaire de tag
                $itemFormLi.remove();
            });
        }
        
    });
}(window.jQuery);
