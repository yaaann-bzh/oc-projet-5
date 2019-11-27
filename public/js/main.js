//Règles affichage liste et posts sur la page d'accueil - DEBUT ---------------------------------------------------------------------

let homepage = new Homepage('post-items', 'post-view');

//Règles affichage liste et posts sur la page d'accueil - FIN --------------------------------------------------------------------


// Validation des formulaires avant envoi - DEBUT ---------------------------------------------------------------------

let prefixUrl = 'http://projet-5/form-validation/';
let form;

if ($('form.needs-validation').length > 0 ) {
            
    form = new Form(document.querySelector('form'), prefixUrl);

    $('input').on('change', function (e) {
        e.currentTarget.classList.remove('border-danger');
        $('#' + e.currentTarget.id + ' ~ .invalid-feedback').css('display', 'none');
    });

    ajaxGet(form.url, function (reponse){
        Object.assign(form.standards, JSON.parse(reponse));
    });
    
    form.form.addEventListener('submit', function (e) {
        form.errors = [];
        $('.invalid-feedback').css('display', 'none').empty();
        if (!form.isValid()){
            console.log('formulaire recalé');
            e.preventDefault();
        } else {
            console.log('formulaire envoyé');
            e.preventDefault();
        }
    });
}

// Validation des formulaires avant envoi - FIN ---------------------------------------------------------------------