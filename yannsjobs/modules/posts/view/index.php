<section class="container pt-5">
    <div class="row">
        <?php if (isset($error)) { ?>
            <div class="col-12 bg-light p-3 m-3 text-danger border rounded border-danger">
                <p class="m-0"> => <?= $error; ?></p>
            </div>
        <?php } ?>
        <div class="col-12 col-md-8">
            <?php if ($pagination['hasTo']) { ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <li class="page-item">
                            <a class="page-link" href="<?= $pagination['previous']; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php
                        for ($i = 1; $i <= $pagination['total']; $i++) {
                            ?>
                            <li class="page-item
                        <?php if ($pagination['current'] === $i) {
                                echo ' active';
                            } ?>
                        "><a class="page-link" href="index-<?= $i; ?>"><?= $i; ?></a></li>
                            <?php
                        }
                        ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= $pagination['next']; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php
            }
            foreach ($postsList as $post) {
                ?>
                <div class="border p-3">
                    <div class="d-flex justify-content-between">
                        <h2><a href="post-<?= $post->id(); ?>"><?= htmlspecialchars($post->title(),ENT_QUOTES | ENT_SUBSTITUTE); ?></a></h2>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</section>
