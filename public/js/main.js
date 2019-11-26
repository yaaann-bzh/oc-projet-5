//Règles affichage liste et posts sur la page d'accueil - DEBUT ---------------------------------------------------------------------

let homepage = new Homepage('post-items', 'post-view');

//Règles affichage liste et posts sur la page d'accueil - FIN --------------------------------------------------------------------


// Validation des formulaires avant envoi - DEBUT ---------------------------------------------------------------------

let prefixUrl = 'http://projet-5/form-validation/';
let form;

if ($('form.needs-validation') !== null) {
        
    form = new Form(document.querySelector('form'), prefixUrl);    

    ajaxGet(form.url, function (reponse){
        Object.assign(form.standards, JSON.parse(reponse));
    });
    
    form.form.addEventListener('submit', function (e) {
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