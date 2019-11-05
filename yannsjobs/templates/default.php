<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="icon" href="#" sizes="32x32">

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
            <nav class="navbar navbar-expand-md navbar-dark bg-success">
                <div class="container">
                    <a class="navbar-brand" href="/">YannsJobs</a>
                    <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarsExample07" aria-controls="navbarsExample07" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarsExample07" style="">
                        <ul class="navbar-nav">
                            <li class="nav-item active">
                                <a class="nav-link" href="/"><i class="fas fa-home"></i> Home</a>
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
                        <ul class="navbar-nav ml-auto" id="member">
                            <?php if ($user->isAuthenticated()) { ?>
                            <li class="nav-item active d-flex">
                                <a class="nav-link" href="/user/profile-<?= $user->getAttribute('id'); ?>" title="Mon profil"><i class="fas fa-member-alt"></i> <?= $user->getAttribute('pseudo'); ?></a>
                                <a class="nav-link ml-3" href="/deconnection" title="Se deconnecter"><i class="fas fa-power-off"></i></a>
                            </li>
                            <?php } else { ?>
                                <li class="nav-item active position-relative">
                                    <a href="#" class="collapsed nav-link" data-toggle="collapse" data-target="#navbarsConnect" aria-controls="navbarsConnect" aria-expanded="false" aria-label="Toggle navigation">
                                        Se connecter
                                    </a>
                                    <div class="collapse position-absolute list-group" id="navbarsConnect" style="">
                                        <a class="list-group-item list-group-item-action" href="/candidate"><i class="fas fa-user-alt text-primary mr-3"></i> Candidats</a>
                                        <a class="list-group-item list-group-item-action" href="/recruiters"><i class="fas fa-user-alt text-danger mr-3"></i> Recruteurs</a>
                                        <a class="list-group-item list-group-item-action" href="/admin"><i class="fas fa-user-alt text-black mr-3"></i> Administrateur</a>
                                    </div>
                                </li>
                            <?php } ?>

                        </ul>
                    </div>
                </div>

            </nav>
        </header>

        <div id="top-page" class="bg-light">
            <?= $content; ?>
        </div>

        <footer class="bg-dark text-light">
            <div class="container p-4">
                <p class="float-right m-0"><a href="#top-page" class="text-light">Haut de page</a></p>
                <p class="m-0 text-secondary"> 2019 - Yaaann · <a href="https://www.yaaann.ovh" class="text-light">Mes autres projets</a> · <a href="https://openclassrooms.com/fr/" class="text-light">OpenClassrooms</a></p>
            </div>
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