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
                this.showError(input);
            }
            else if (input.value.length === 0 && this.standards[standard].required) {
                this.showError(input);
            }
        }
        console.log(this.errors);
        
        if (this.errors.length === 0){
            return true;
        }
        
        return false;
    }
    
    showError(input) {
        input.classList.add('border-danger');
        $('#' + input.id + ' ~ .invalid-feedback').css('display', 'block');
        this.errors.push('champ requis : ' + input.name);
    }

}