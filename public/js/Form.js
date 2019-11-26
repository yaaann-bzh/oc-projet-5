class Form {
    constructor(inputs, prefixUrl){
        this.inputs = inputs;
        this.url = this.setUrl(prefixUrl);
        console.log(this.url);
        this.standards = {}; 
    }
    
    setUrl(prefixUrl){
        let names=[];
        for (var i = 0; i < this.inputs.length; i++) {
            names[i] = this.inputs[i].name;
        }

        names = names.toString();
        return prefixUrl + names;
    }
    
    setStandards(reponse) {
        Object.assign(this.standards, JSON.parse(reponse));
    }

}