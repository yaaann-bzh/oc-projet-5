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
            console.log(input);
            if (input.value.length === 0 && this.standards[standard].required) {
                input.classList.add('border-danger');
                $('#' + input.id + ' + .invalid-feedback').css('display', 'block');
                this.errors.push('champ requis');
            }
            //console.log(input);
        }
        //console.log(this.errors);
        
        if (this.errors.length === 0){
            return true;
        }
        
        return false;
    }

}