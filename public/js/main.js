//Règles affichage liste et posts sur la page d'accueil - DEBUT ---------------------------------------------------------------------

let homepage = new Homepage('post-items', 'post-view');

$('.close-view').on('click', function (e) {
    let id = e.target.id.replace('close-', '');
    $('#post-item-list').css('visibility', 'visible');
    $('#post-view-' + id).css('display', 'none');
});

//Règles affichage liste et posts sur la page d'accueil - FIN --------------------------------------------------------------------


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

// Evènements ponctuels sur éléments de page - DEBUT ---------------------------------------------------------------------

$('.hard-confirm').on('click', function () {
    if(window.confirm("Attention, cette action est irréversible !")){
        return;
    }
});

// Evènements ponctuels sur éléments de page - FIN ---------------------------------------------------------------------