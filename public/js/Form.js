class Form {
    constructor(formElt, prefixUrl){
        this.form = formElt;
        this.url = this.setUrl(prefixUrl);
        console.log(this.url);
        this.standards = {};
        this.errors = [];
    }
    
    setUrl(prefixUrl){
        let names=[];
        for (var i = 0; i < this.form.elements.length - 1 ; i++) {
            names[i] = this.form.elements[i].name;
        }

        names = names.toString();
        return prefixUrl + names;
    }
    
    isValid() {
        //console.log(this.form.elements);
        //console.log(this.standards);

        for (let standard in this.standards) {
            let input = this.form.elements.namedItem(standard);
            
            if (input.type === 'checkbox' && !input.checked && this.standards[standard].required) {
                this.showError(input, 'Champ requis');
            } else if (input.value.length === 0 && this.standards[standard].required) {
                this.showError(input, 'Champ requis');
            } 
            
            if (input.value.length > 0) {       
                switch (this.standards[standard].type) {
                    case 'text':
                        this.textControl(this.standards[standard], input);
                        break;
                    case 'int':
                        this.intControl(this.standards[standard], input);
                        break;
                    case 'email':
                    case 'phone':
                    case 'pass':
                        this.regexControl(this.standards[standard], input);
                        break;                  
                    case 'confirm':
                        this.confirmControl(input);
                        break;
                    case 'file':
                        this.fileControl(this.standards[standard], input);
                        break;
                }
            }
        }
        console.log(this.errors.length);
        return this.errors.length === 0;

    }
    
    showError(input, message) {
        input.classList.add('border-danger');
        $('#' + input.id + ' ~ .invalid-feedback').css('display', 'block').append(message);
        this.errors.push(input.id + ' : ' + message);
    }
    
    textControl(standard, input){
        if (input.value.length < standard.min || input.value.length > standard.max){
            this.showError(input, 'Ce champ doit comporter entre ' + standard.min + ' et ' + standard.max + ' caractères');
        }
    }
    
    intControl(standard, input){
        if (input.value < standard.min || input.value > standard.max){
            this.showError(input, 'La valeur doit être comprise entre ' + standard.min + ' et ' + standard.max);
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
            this.showError(input, message);
            $('#' + input.id + ' ~ .info-compl').addClass('text-danger').removeClass('text-muted');
        }
    }
    
    confirmControl (input) {
        if (input.value !== this.form.elements.namedItem('pass').value) {
            let message = 'Les deux mots de passe doivent être identiques';
            this.showError(input, message);
            this.form.elements.namedItem('pass').classList.add('border-danger');
            $('#' + this.form.elements.namedItem('pass').id + ' ~ .invalid-feedback').css('display', 'block').append('<br/>' + message);
        }
    }
    
    fileControl (standard, input){
        
    }

}