<div class="jumbotron">
    <div class="container">
        <h2>Jean Forteroche</h2>
        <h1 class="display-5">Billet simple pour l'Alaska</h1>
        <p><em>Bienvenue sur le site de publication de mon dernier roman. Vous trouverez ici la liste des chapitres publiés.</em></p>
        <p><strong> Excellente lecture!</strong></p>
    </div>
</div>

<section class="container">
    <div class="row">
        <div class="col-12 col-md-8">
            <nav>
                <ul class="pagination justify-content-center">
                    <li class="page-item">
                    <a class="page-link" href="<?= $prevIndex; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                    </li>
                    <?php
                    for ($i=1; $i <= $nbPages; $i++) { 
                        ?>
                        <li class="page-item 
                        <?php if ($index === $i) { echo ' active'; } ?>
                        "><a class="page-link" href="index-<?= $i; ?>"><?= $i; ?></a></li>
                        <?php
                    }
                    ?>
                    <li class="page-item">
                    <a class="page-link" href="<?= $nextIndex; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                    </li>
                </ul>
            </nav>

            <?php
            foreach ($postsList as $post) {
                ?>
                <div class="border p-3">
                    <div class="d-flex justify-content-between">
                        <h2><a href="post-<?= $post->id(); ?>"><?= htmlspecialchars($post->title(),ENT_QUOTES | ENT_SUBSTITUTE); ?></a></h2>
                        <div class="flex-shrink-0">
                            <a href="post-<?= $post->id(); ?>#comments" title="Commentaires" class="badge badge-primary badge-pill"><?= $nbComments[$post->id()]; ?></a>
                            <button class="collapsed btn btn-light ml-2" type="button" data-toggle="collapse" data-target="#postContent-<?= $post->id(); ?>" aria-controls="postContent-<?= $post->id(); ?>" aria-expanded="false" aria-label="Toggle">
                                <i class="fas fa-chevron-down fa-2x"></i>
                            </button>
                        </div>

                    </div>
                    <div class="collapse" id="postContent-<?= $post->id(); ?>" style="">
                        <p><?= $exerpts[$post->id()]; ?> ... | <a href="post-<?= $post->id(); ?>">Lire la suite</a></p>
                    </div>
                </div>              
                <?php
            }
            ?>
        </div>
    </div>
</section>
