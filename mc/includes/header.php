<?php require_once __DIR__ . "/config.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? SITE_NAME ?></title>
    <meta name="description" content="<?= SITE_TAGLINE ?>">
    <meta property="og:title" content="<?= SITE_NAME ?>">
    <meta property="og:description" content="<?= SITE_TAGLINE ?>">
    <meta property="og:image" content="/assets/img/server-icon.png">
    <link rel="icon" href="/assets/img/server-icon.png">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <nav>
        <div class="nav-inner">
            <a href="/" class="nav-brand"><?= SITE_NAME ?></a>
            <button class="nav-toggle" aria-label="Toggle menu">&#9776;</button>
            <div class="nav-links">
                <a href="/" <?= basename($_SERVER["SCRIPT_NAME"]) === "index.php" ? "class=\"active\"" : "" ?>>Home</a>
                <a href="/join" <?= basename($_SERVER["SCRIPT_NAME"]) === "join.php" ? "class=\"active\"" : "" ?>>Join</a>
                <a href="/stats" <?= basename($_SERVER["SCRIPT_NAME"]) === "stats.php" ? "class=\"active\"" : "" ?>>Stats</a>
                <a href="/rules" <?= basename($_SERVER["SCRIPT_NAME"]) === "rules.php" ? "class=\"active\"" : "" ?>>Rules</a>
                <a href="/commands" <?= basename($_SERVER["SCRIPT_NAME"]) === "commands.php" ? "class=\"active\"" : "" ?>>Commands</a>
                <a href="/faq" <?= basename($_SERVER["SCRIPT_NAME"]) === "faq.php" ? "class=\"active\"" : "" ?>>FAQ</a>
                <a href="/map" <?= basename($_SERVER["SCRIPT_NAME"]) === "map.php" ? "class=\"active\"" : "" ?>>Map</a>
                <a href="/news" <?= basename($_SERVER["SCRIPT_NAME"]) === "news.php" ? "class=\"active\"" : "" ?>>Changelog</a>
            </div>
        </div>
    </nav>
    <main>
