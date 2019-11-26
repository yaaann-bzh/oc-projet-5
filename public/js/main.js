//Règles affichage liste et posts sur la page d'accueil - DEBUT ---------------------------------------------------------------------

let homepage = new Homepage('post-items', 'post-view');

//Règles affichage liste et posts sur la page d'accueil - FIN --------------------------------------------------------------------


// Validation des formulaires avant envoi - DEBUT ---------------------------------------------------------------------

let prefixUrl = 'http://projet-5/form-validation/';
let form;

if ($('form.needs-validation') !== null) {
    
    form = new Form($("input:not(:submit)"), prefixUrl);    
    
    ajaxGet(form.url, function (reponse){
        form.setStandards(reponse);
    });
}

// Validation des formulaires avant envoi - FIN ---------------------------------------------------------------------