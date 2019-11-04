<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="icon" href="/assets/quill-ink.png" sizes="32x32">

        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" 
                integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" 
                crossorigin="anonymous">

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
                integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"    
                crossorigin="anonymous">

        <link rel="stylesheet" href="/assets/style.css">

        <title><?= $tabTitle; ?></title>
    </head>
    <body>
        <header class="site-header sticky-top">
            <nav class="navbar navbar-expand-md navbar-dark bg-dark">
                <div class="container">
                    <!--<a class="navbar-brand" href="/">J. Forteroche</a>-->
                    <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarsExample07" aria-controls="navbarsExample07" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarsExample07" style="">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item <?php if ($activeNav === 'home') { echo ' active'; } ?>">
                                <a class="nav-link" href="/"><i class="fas fa-home"></i> Home</a>
                            </li>
                            <li class="nav-item <?php if ($activeNav === 'comments') { echo ' active'; } ?>">
                                <a class="nav-link" href="/comments-index-1"><i class="far fa-comment-dots"></i> Derniers commentaires</a>
                            </li>
                        <?php if ($user->isAdmin()) { ?>
                            <li class="nav-item <?php if ($activeNav === 'reports') { echo ' active'; } ?>">
                                <a class="nav-link" href="/admin/reports-index-1"><i class="fas fa-exclamation"></i> Signalements</a>
                            </li>
                            <li class="nav-item <?php if ($activeNav === 'redaction') { echo ' active'; } ?>">
                                <a class="nav-link" href="/admin/redaction"><i class="fas fa-feather-alt"></i> Redaction</a>
                            </li>
                        <?php } ?>
                        </ul>
                        <ul class="navbar-nav" id="member">
                            <?php if ($user->isAuthenticated()) { ?>
                            <li class="nav-item active d-flex">
                                <a class="nav-link" href="/user/profile-<?= $user->getAttribute('id'); ?>" title="Mon profil"><i class="fas fa-member-alt"></i> <?= $user->getAttribute('pseudo'); ?></a>
                                <a class="nav-link ml-3" href="/deconnection" title="Se deconnecter"><i class="fas fa-power-off"></i></a>
                            </li>
                            <?php } else { ?>
                                <li class="nav-item <?php if ($activeNav === 'connect') { echo ' active'; } ?>">
                                    <a class="nav-link" href="/user"><i class="fas fa-member-alt"></i> Se connecter</a>
                                </li>
                            <?php } ?>

                        </ul>
                    </div>
                </div>

            </nav>
        </header>

        <div id="top-page">
            <?= $content; ?>
            <hr class="featurette-divider">
        </div>

        <footer class="container">
            <p class="float-right"><a href="#top-page">Haut de page</a></p>
            <p> 2019 - Yaaann · <a href="https://www.yaaann.ovh">Mes autres projets</a> · <a href="https://openclassrooms.com/fr/">OpenClassrooms</a></p>
        </footer>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" 
                integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" 
                crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" 
                integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" 
                crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" 
                integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" 
                crossorigin="anonymous"></script>
        
    </body>
</html>