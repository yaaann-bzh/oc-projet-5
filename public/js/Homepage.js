class Homepage {
    constructor(itemClass, viewClass){
        let itemsCollection = document.getElementsByClassName(itemClass);
        let viewsCollection = document.getElementsByClassName(viewClass);
        if (itemsCollection.length > 0 && viewsCollection.length > 0 ){
            this.items = this.setElts(itemsCollection);
            this.views = this.setElts(viewsCollection);
            this.process();
        }
    }
    
    setElts(collection){
        let items=[];
        for (let i = 0; i < collection.length; i++) {
            items.push(collection[i]);
        }
        return items;
    }
    
    activeItem(e) {
        homepage.items.forEach(elt => {
            elt.classList.remove('active');
        });
        let item = e.currentTarget;
        item.classList.add('active');
        let id = item.id.replace('item', 'view');
        homepage.showPost(id);
    }
    
    showPost(id) {
        this.views.forEach(view =>{
            view.style.display = 'none';
        });
        let active = document.getElementById(id);
        active.style.display = 'block';
    }
    
    process(){
        this.items.forEach(item => {
            item.addEventListener('click', this.activeItem);
        });
    }
}

