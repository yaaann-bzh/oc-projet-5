//Règles affichage liste et posts sur la page d'accueil - DEBUT ---------------------------------------------------------------------

let homepage = new Homepage('post-items', 'post-view');

$('.close-view').on('click', function (e) {
    let id = e.target.id.replace('close-', '');
    $('#post-item-list').css('visibility', 'visible');
    $('#post-view-' + id).css('display', 'none');
});

//Règles affichage liste et posts sur la page d'accueil - FIN -------------------------------------------------------------------

// Suggestion de localisation - DEBUT ---------------------------------------------------------------------

//API 
const urlSearch = "http://api.geonames.org/searchJSON?";//cf. https://www.geonames.org/export/geonames-search.html
const country = "&country=FR";// limite les résultats en France
const featureClass = "&featureClass=P"; // tout type de ville et villages
const format = "&style=FULL";//Quantité d'info retournée
const limit = "&maxRows=10";//Nombre de résultat
const isNameRequired = "&isNameRequired=true";
const username = "&username=yaaann";
const parameters = country + featureClass + format + limit + isNameRequired + username;

function suggest(response) {
    let results = JSON.parse(response).geonames;
    if (results === null) {
        console.error(JSON.parse(response).status.message);
    } else {
        results.forEach(result => {
            $('#suggests').append('<li class="list-group-item suggest"><p>' + result.asciiName + ' (' + result.adminCode2 + ')</p></li>');
        }); 
        console.log(results);
    }
}

$('#suggests').on('click', '.suggest', function (e) {
    let input = document.getElementById('location');
    input.value = e.target.textContent;
    form.contentControll(input);
});
        
$('#location').on('input', function (e) {
    let query = e.currentTarget.value;
    $('#suggests').children().remove();
    if (query.length > 2) {
        ajaxGet(urlSearch + "q=" + query + parameters, suggest);
    }});

$('html').on('click', function () {
    $('#suggests').children().remove();
});


// Suggestion de localisation - FIN ---------------------------------------------------------------------

// Validation des formulaires avant envoi - DEBUT ---------------------------------------------------------------------

let form;
                                        
function tinymce_getContent() {
    return tinymce.get(tinymce.activeEditor.id).contentDocument.body.innerHTML;
}

function progressBar(e) {
    e.stopPropagation();
    let textarea = e.currentTarget;
    let id, value, progress, color;

    if (textarea.id === 'tinymce') {
        id = textarea.getAttribute('data-id');
        value = textarea.innerHTML;
    } else {
        id = textarea.id;
        value = textarea.value;
    }

    if (value.length < form.standards[id].min) {
        progress = form.standards[id].min / form.standards[id].max * 100;
        color = 'danger';
    } else if (value.length >= form.standards[id].max) {
        progress = 100;
        color = 'danger';
    } else {
        progress = value.length / form.standards[id].max * 100;
        if (progress > 90) {
            color = 'warning';
        } else {
            color = 'success';
        }
    }
    $('#progress-bar-' + id).css('width', progress + '%').removeClass('bg-success bg-warning bg-danger').addClass('bg-' + color);
}

if ($('form.form-validation').length > 0 ) {
            
    form = new Form(document.querySelector('form'));

    form.inputs.on('change', function (e) {
        let input = e.currentTarget;
        form.contentControll(input);
        input.classList.remove('border-danger');
    });

    ajaxGet(form.url, function (reponse){
        Object.assign(form.standards, JSON.parse(reponse));
    });

    form.form.addEventListener('submit', function (e) {
        //e.preventDefault();
        console.log(form.form);
        if(!form.submitControll()){
            e.preventDefault();
        };
    });
    
    $('textarea').on('input', progressBar);
    
    setTimeout(function () {
        $('textarea').trigger('input');
    },500);
}

// Validation des formulaires avant envoi - FIN ---------------------------------------------------------------------

// Marquer une candidature Lue/non-lue - DEBUT ---------------------------------------------------------------------

$('.readSet').on('click', function (e) {
    let id = e.currentTarget.id.replace('readSet-', '');
    ajaxGet('/recruiter/candidacy/change-status-' + id, function (reponse) {
        $('#readSet-' + id).addClass('d-none');
        $('#unreadSet-' + id).removeClass('d-none');
        $('#item-' + id).addClass('text-muted bg-light');
    });
});

$('.unreadSet').on('click', function (e) {
    let id = e.currentTarget.id.replace('unreadSet-', '');
    ajaxGet('/recruiter/candidacy/change-status-' + id, function (reponse) {
        $('#unreadSet-' + id).addClass('d-none');
        $('#readSet-' + id).removeClass('d-none');
        $('#item-' + id).removeClass('text-muted bg-light');
    });
});

// Marquer une candidature Lue/non-lue - FIN ---------------------------------------------------------------------

// Image profil recruteur - DEBUT ---------------------------------------------------------------------------------------

function changeProfilePic(reponse) {   
    let responses = JSON.parse(reponse);
    
    if (responses[0] !== 'valid'){
        for (var response in responses) {
            console.error(responses[response]);
            $('#indications').removeClass('text-muted').addClass('text-danger font-weight-bold').text('Erreur !');
        }
    } else {
        $('#profile_pict').attr('src', responses[1] + '?' + Date());
        $('#indications').removeClass('text-danger font-weight-bold').addClass('text-muted').text('Changement effectué!');
    }  
}

$('#changepic').on('click', function () {
    $('#profilepic').trigger('click');
});

$('#profilepic').on('change', function (e) {
    if (e.currentTarget.validity.customError) {
        $('#indications').removeClass('text-muted').addClass('text-danger font-weight-bold');
    } else {
        $('#indications').removeClass('text-danger font-weight-bold').addClass('text-muted');
        let picture = new FormData(form.form);
        ajaxPost('/recruiter/newprofilepic', picture , changeProfilePic); 
    }
});

// Image profil recruteur - FIN ---------------------------------------------------------------------------------------

// Evènements ponctuels sur éléments de page - DEBUT ---------------------------------------------------------------------

$('.hard-confirm').on('click', function () {
    if(window.confirm("Attention, cette action est irréversible !")){
        return;
    }
});

// Evènements ponctuels sur éléments de page - FIN ---------------------------------------------------------------------