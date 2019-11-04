<section class="container pt-5">
    <div class="row">
        <div class="col-12 col-md-8">
            <nav class="m-3">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php if ($pagination['nextLink'] === '#') { echo 'disabled'; } ?>">
                        <a class="page-link" href="<?= $pagination['nextLink']; ?>" aria-label="Next" title="Plus récent">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#"  title="Publication courante"><?= $pagination['current']; ?> / <?= $pagination['total']; ?> </a>
                    </li>
                    <li class="page-item <?php if ($pagination['prevLink'] === '#') { echo 'disabled'; } ?>">
                        <a class="page-link" href="<?= $pagination['prevLink']; ?>" aria-label="Previous" title="Plus ancien">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <?php if (isset($updated)) { ?>        
                <div class="form-group row">
                    <div class="col-12 col-lg-6 bg-light" id="valid-message">
                        <p>La publication a été mise à jour.</p>
                    </div>
                </div>
            <?php } ?>

            <div class="border p-3">
                <div>
                    <div class="d-flex w-100 justify-content-between align-item-center">
                        <?php if ($user->isAuthenticated() AND $user->isAdmin()) { ?>
                            <p class="mb-2 text-right"><a class="text-white bg-primary p-1" href="/admin/post-<?= $post->id(); ?>">Modifier la publication</a></p>
                        <?php } ?>
                        <p class="mb-2 text-right"><a class="p-1" href="#comments">Commentaires <i class="fas fa-long-arrow-alt-down"></i></a></p>
                    </div>
                    <h1 class="display-5 w-100"><?= $post->title(); ?></h1>

                </div>
                <p class="lead">Par <?= htmlspecialchars($author->pseudo()); ?></p>
                <p> 
                    <em>Publié le <?= $post->addDate()->format('d/m/Y à H\hi'); ?></em>
                    <?php
                    if ($post->updateDate() !== null) { 
                        echo '<em class="text-danger"> - Modifié le ' . $post->updateDate()->format('d/m/Y à H\hi') . '.</em>'; 
                    } ?>                  
                </p>
                <hr class="my-4">
                <p class="text-justify"><?= $post->content(); ?></p>
            </div>

            <div class="p-3" id="comments">
                <?php 
                if ($user->isAuthenticated()) {
                    include('_commentForm.php');
                }

                foreach ($comments as $comment ) { ?>
                    <div class="p-3 mb-3 bg-light border border-dark rounded" id="comment-<?= $comment->id(); ?>">
                        <?php
                        if ((int)$comment->removed() === 1) { ?>
                            <p class="ml-3"><em>Ce commentaire a été supprimé [...]</em></p>
                        <?php 
                        } else { ?>
                            <p>
                                <strong><a href="/member-<?= $comment->memberId(); ?>-1"><?= htmlspecialchars($members[$comment->id()]->pseudo(),ENT_QUOTES | ENT_SUBSTITUTE); ?></a></strong>
                                <span class="mb-1 ml-2 badge badge-success"><?php if ($members[$comment->id()]->privilege() !== null) { echo $members[$comment->id()]->privilege(); } ?></span>
                                - le <?= $comment->addDate()->format('d/m/Y à H\hi'); 
                                if ($comment->updateDate() !== null) { echo '<em> - Modifié le ' . $comment->updateDate()->format('d/m/Y à H\hi') . '.</em>'; } ?>                  
                            </p>
                            <p class="m-0"><?= nl2br(htmlspecialchars($comment->content(),ENT_QUOTES | ENT_SUBSTITUTE)); ?></p>

                            <?php include(__DIR__ . '/../../../templates/_comment_admin.php'); ?>
                    <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

