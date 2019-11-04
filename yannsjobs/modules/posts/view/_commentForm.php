<form method="post" action="user/insert-comment-<?= $post->id(); ?>" class="mb-3">
    <div class="form-group">
        <label for="content">Votre commentaire :</label>
        <textarea class="form-control" name="comment" id="content" cols="30" rows="2" required=""></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Publier</button>
</form>