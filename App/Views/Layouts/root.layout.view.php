<?php

/** @var string $contentHTML */
/** @var \Framework\Auth\AppUser $user */
/** @var \Framework\Support\LinkGenerator $link */

// Aktuálny controller (na zvýraznenie položky v menu)
$current = strtolower($_GET['c'] ?? 'home');
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= App\Configuration::APP_NAME ?></title>
    <!-- Favicons -->
    <link rel="icon" type="image/svg+xml" href="<?= $link->asset('images/favicon.svg') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $link->asset('favicons/apple-touch-icon.png') ?>">
    <link rel="manifest" href="<?= $link->asset('favicons/site.webmanifest') ?>">
    <link rel="shortcut icon" href="<?= $link->asset('favicons/favicon.ico') ?>">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
    <!-- Písma -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,600;1,400&family=Inter:wght@400;500;600&family=UnifrakturCook:wght@700&display=swap" rel="stylesheet">
    <!-- Vlastné štýly (až po Bootstrape) -->
    <link rel="stylesheet" href="<?= $link->asset('css/style.css') ?>">
    <script src="<?= $link->asset('js/script.js') ?>"></script>
</head>
<body class="d-flex flex-column min-vh-100">

<header class="site-header sticky-top">
    <nav class="navbar navbar-expand-md" aria-label="Hlavní navigace">
        <div class="container-fluid px-3 px-md-5">
            <a class="navbar-brand d-flex align-items-center gap-2 m-0" href="<?= $link->url('dennik.index') ?>" aria-label="Dračí Doupě – domů">
                <span class="brand__mark" aria-hidden="true">D</span>
                <span>Dračí Doupě</span>
            </a>

            <a class="user ms-auto me-2 me-md-0 order-md-last" href="<?= $user->isLoggedIn() ? $link->url('auth.logout') : App\Configuration::LOGIN_URL ?>"
               aria-label="<?= $user->isLoggedIn() ? 'Odhlásit se' : 'Přihlásit se' ?>">
                <img src="<?= $link->asset('images/user.svg') ?>" alt="" width="40" height="40">
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#hlavni-menu" aria-controls="hlavni-menu" aria-expanded="false" aria-label="Zobrazit menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="hlavni-menu">
                <ul class="navbar-nav ms-md-auto me-md-4 gap-md-3">
                    <li class="nav-item"><a class="nav-link" href="#">Povolání</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Rasa</a></li>
                    <li class="nav-item">
                        <a class="nav-link<?= $current === 'dennik' ? ' active' : '' ?>"
                           href="<?= $link->url('dennik.index') ?>"
                           <?= $current === 'dennik' ? 'aria-current="page"' : '' ?>>Deníky</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<?= $contentHTML ?>

</body>
</html>
