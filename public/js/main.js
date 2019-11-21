//Règles affichage liste et posts sur la page d'accueil - DEBUT ---------------------------------------------------------------------

let postItemsCollection = document.getElementsByClassName('post-items');
let postViewsCollection = document.getElementsByClassName('post-view');

function setElts(htmlCollection) {
    let items=[];
    for (let i = 0; i < htmlCollection.length; i++) {
        items.push(htmlCollection[i]);
    }
    return items;
}

let postItems = setElts(postItemsCollection);
let postViews = setElts(postViewsCollection);

function showPost(id) {
    postViews.forEach(postView =>{
        postView.style.display = 'none';
    });
    let activePost = document.getElementById('post-view-' + id);
    console.log(activePost);
    activePost.style.display = 'block';
}

function activeItem(e) {
    postItems.forEach(postItem => {
        postItem.classList.remove('active');
    });
    let item = e.currentTarget;
    item.classList.add('active');
    let postId = item.id.replace('post-item-', '');
    showPost(postId);
}

postItems.forEach(postItem => {
    postItem.addEventListener('click', activeItem);
});

//Règles affichage liste et posts sur la page d'accueil - FIN --------------------------------------------------------------------


