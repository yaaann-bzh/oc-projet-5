class Form {
    constructor(formElt){
        this.form = formElt;
        this.inputs = $('.form-validation [name]:not(:submit)');
        this.url = this.setUrl();
        this.standards = {};
    }
    
    setUrl(){
        let names=[];
        for (var i = 0; i < this.form.elements.length - 1 ; i++) {
            names[i] = this.form.elements[i].name;
        }
        names = names.toString();
        return '/form-validation/' + names;
    }
    
    submitControll() {
        for (let standard in this.standards) {
            let input = this.form.elements.namedItem(standard);
            if (input.type === 'textarea' && input.classList.contains('tinymce')){
                input.innerHTML = tinymce_getContent();
            } else if (input.type === 'checkbox' && !input.checked && this.standards[standard].required) {
                this.showError(input, 'Champ requis');
            } else if (input.value.length === 0 && this.standards[standard].required) {
                this.showError(input, 'Champ requis');
            }
        }
        
        return this.form.reportValidity();
    }
    
    showError(input, message) {
        $('#' + input.id + '~ .invalid-input').remove();
        $('#' + input.id).after('<div class="invalid-input">' + message + '</div>');
        input.setCustomValidity(message);
    }
    
    removeError(input) {
        $('#' + input.id + '~ .invalid-input').remove();
        input.setCustomValidity('');
    }
    
    contentControll(input) {
        let standard = this.standards[input.id];
        let errorMessage;
        
        switch (standard.type) {
            case 'text':
                errorMessage = this.textControl(standard, input);
                break;
            case 'int':
                errorMessage = this.intControl(standard, input);
                break;
            case 'email':
            case 'phone':
            case 'pass':
                errorMessage = this.regexControl(standard, input);
                break;                  
            case 'confirm':
                errorMessage = this.confirmControl(input);
                break;
            case 'file':
                errorMessage = this.fileControl(standard, input);
                break;
        }
        
        if (errorMessage === undefined && standard.uniq) {
            errorMessage = this.uniqInputControl(input);
            console.log(errorMessage);
        }
        
        if (errorMessage !== undefined) {
            this.showError(input, errorMessage);
        } else {
            this.removeError(input);
        }
        
    }
    
    textControl(standard, input){
        if (input.value.length < standard.min || input.value.length > standard.max){
            return 'Ce champ doit comporter entre ' + standard.min + ' et ' + standard.max + ' caractères';
        }
    }
    
    intControl(standard, input){
        if (input.value < standard.min || input.value > standard.max){
            return  'La valeur doit être comprise entre ' + standard.min + ' et ' + standard.max;
        }
    }
    
    regexControl(standard, input){
        let message;
        switch (input.name) {
            case 'phone':
                message = 'Merci de saisir un numéro de téléphone valide';
                break;
            case 'email':
                message = 'Merci de saisir une adresse mail valide';
                break;
            case 'pass':
                message = 'Votre mot de passe doit respecter les critères de sécurité';
                break;
            default:
                message = 'Merci de saisir une donnée valide';
                break;
        }
        
        let regex = new RegExp(standard.regex.substring(1, standard.regex.length-1));
        
        if (!regex.test(input.value)){
            return  message;
        }
    }
    
    confirmControl (input) {
        if (input.value !== this.form.elements.namedItem('pass').value) {
            return  'Les deux mots de passe doivent être identiques';
        }
    }
    
    fileControl (standard, input){
        let message;
        if (input.files[0].size < standard.min * 1024){
            message = 'La taille du fichier semble faible (<' + standard.min + ' ko), vérifier qu\'il n\'y a pas d\'erreur ';
        } else if (input.files[0].size > standard.max * 1024){
            message = 'La taille doit être inférieure à ' + standard.max + ' ko ';
        }
        
        let ext = input.files[0].name.substring(input.files[0].name.lastIndexOf('.')+1, input.files[0].name.length);
        
        if (standard.ext.indexOf(ext) < 0) {
            message +=  '- L\'extension du fichier n\'est pas valide';
        } 
        
        return message;
    }
    
    uniqInputControl(input) {
        let manager = this.form.id.replace('-add', '');
        let url = '/input-validation/' + manager + '-' + input.id + '-' + input.value;
        let req = new XMLHttpRequest();
        req.open("GET", url, false);
        req.addEventListener("error", function () {
            console.error("Erreur réseau avec l'URL " + url);
        });
        req.send(null);
        if (req.status === 204) {
            return 'Déjà utilisé(e), merci d\'en saisir un(e) autre';
        }
    }

}