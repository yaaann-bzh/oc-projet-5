<section class="container pt-5">
    <div class="row">
        <div class="col-12 offset-md-1 col-md-10">
            <h1 class="h2">Redaction</h1>

            <?php if (isset($message)) { ?>
                <div class="col-12 col-lg-6 bg-light p-3 text-danger border rounded border-danger">
                    <p class="m-0">Une erreur s'est produite lors de l'enregistrement</p>
                    <p class="m-0"> => <?= $message; ?></p>
                </div>
            <?php } ?>

            <form method="post" action="" class="mb-3">
                <div class="form-group">
                    <label for="title">Titre :</label>
                    <input class="form-control col-12 col-md-10 col-lg-8" type="text" name="title" id="title" autofocus required value="<?php if (isset($post)) { echo $post->title(); } ?>">
                </div>
                <div class="form-group">
                    <label for="content">Texte :</label>
                    <textarea name="content" id="content" cols="30" rows="20"><?php if (isset($post)) { echo $post->content(); } ?></textarea>
                </div>
                <?php if (isset($post)) { ?>
                    <input type="submit" class="btn btn-success mr-3" name="action" value="Modifier">
                    <input type="submit" class="btn btn-danger" name="action" value="Supprimer">
                <?php } else { ?>
                    <input type="submit" class="btn btn-primary" value="Publier">
                <?php } ?>
            </form>

            <script src="https://cdn.tiny.cloud/1/yyqsfdko2tg7akueyctfkasll6yienujy96w06tbyhvuuzlq/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
            <script>
                tinymce.init({
                    selector:'#content',
                    invalid_elements:'script'                
                });
            </script>

        </div>
    </div>    

</section>