<?php 
if ($user->isAuthenticated()) {
    if ((int)$user->getAttribute('id') === (int)$comment->memberId() OR $user->isAdmin()) { ?>
        <p class="mt-2 mb-0"><a href="/user/comment-<?= $comment->id(); ?>">Modifier/Supprimer</a></p>
    <?php 
    } else { ?>
    <p class="mt-2 mb-0"><a href="/user/comment-report-<?= $comment->id(); ?>">Signaler</a></p> 
    <?php
    } 
}?>                      
