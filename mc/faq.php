<?php
$pageTitle = 'FAQ - Neopolitan';
require __DIR__ . '/includes/header.php';
?>

<section class="faq-page">
    <h1>FAQ</h1>

    <div class="faq-list">
        <div class="faq-item">
            <h3>What version does the server run?</h3>
            <p>We run <strong><?= mcVersionDisplay() ?></strong> on <?= MC_ENGINE ?>. Older clients can usually connect thanks to ViaVersion.</p>
        </div>

        <div class="faq-item">
            <h3>Can I play on Bedrock Edition?</h3>
            <p>Yes! We use <strong>Geyser + Floodgate</strong> so Bedrock players can join without a Java account. Use the same server address (<code><?= MC_SERVER_DISPLAY ?></code>) with port <strong>19132</strong>. You'll still need to be whitelisted — ask in <a href="https://discord.gg/UvySgMH" target="_blank" rel="noopener">Discord</a> to get your gamertag added.</p>
        </div>

        <div class="faq-item">
            <h3>How do I protect my builds?</h3>
            <p>We use <strong>GriefPrevention</strong> for land claims. Grab a <strong>golden shovel</strong> and right-click two opposite corners to create a claim. You earn claim blocks passively — about 100 per hour.</p>
            <p>Use <code>/trust &lt;player&gt;</code> to give someone build access. See the <a href="/commands">Commands page</a> for more.</p>
        </div>

        <div class="faq-item">
            <h3>What mods are allowed?</h3>
            <p>Performance mods (Sodium, Optifine, Lithium), minimaps, and cosmetic mods are fine. Anything that gives you an unfair advantage is not — no x-ray, no auto-clickers, no hacked clients. If you're unsure, just ask.</p>
        </div>

        <div class="faq-item">
            <h3>What are resource worlds?</h3>
            <p>We have separate <strong>resource nether</strong> and <strong>resource end</strong> worlds for gathering materials. These reset yearly so they stay fresh, while the main worlds remain permanent.</p>
        </div>

        <div class="faq-item">
            <h3>Is PVP enabled?</h3>
            <p>PVP is enabled, but it's <strong>consensual only</strong>. Don't attack players who haven't agreed to fight. Spawn killing is not allowed.</p>
        </div>

        <div class="faq-item">
            <h3>Still have questions?</h3>
            <p>Hop in the <a href="https://discord.gg/UvySgMH" target="_blank" rel="noopener">Discord</a> and ask — someone will help you out.</p>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
