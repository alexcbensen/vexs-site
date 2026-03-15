<?php
$pageTitle = 'Map - Neopolitan';
require_once __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/header.php';
?>

<section class="map-page">
    <h1>Live Map</h1>
    <p class="muted">Explore the world in your browser. <a href="/bluemap/" target="_blank" rel="noopener">Open fullscreen &rarr;</a></p>

    <div class="map-embed">
        <iframe src="/bluemap/" frameborder="0" allowfullscreen loading="lazy" allow="accelerometer; autoplay; fullscreen; gyroscope; webgl; webgl2; xr-spatial-tracking"></iframe>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
