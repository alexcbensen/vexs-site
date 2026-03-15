<?php
$pageTitle = 'Join - Neopolitan';
require __DIR__ . '/includes/header.php';
?>

<section class="join-page">
    <div class="join-hero">
        <img src="/assets/img/server-icon.png" alt="Neopolitan" class="hero-icon">
        <h1>Join Neopolitan</h1>
        <p class="muted">Enter your username, add the server, play.</p>
    </div>

    <div class="whitelist-card">
        <form id="whitelist-form" class="wl-form">
            <div class="form-group">
                <label for="mc-username">Minecraft Username</label>
                <div class="input-row">
                    <input type="text" id="mc-username" name="username" placeholder="Steve" maxlength="16" pattern="[a-zA-Z0-9_]{3,16}" required autocomplete="off" spellcheck="false">
                    <button type="submit" class="btn-submit">Join</button>
                </div>
                <span class="form-hint">Java Edition only (3-16 characters)</span>
            </div>

            <div class="form-checks">
                <label class="check-label">
                    <input type="checkbox" id="agree-rules" required>
                    <span>I agree to the <a href="/rules" target="_blank">server rules</a></span>
                </label>
                <label class="check-label">
                    <input type="checkbox" id="agree-age" required>
                    <span>I'm 18 or older</span>
                </label>
            </div>
        </form>

        <div id="wl-result" class="wl-result" hidden>
            <div class="result-icon"></div>
            <p class="result-message"></p>
            <div class="result-connect" hidden>
                <p class="connect-label">Connect to</p>
                <span class="connect-ip copy-ip" data-ip="<?= MC_SERVER_DISPLAY ?>"><?= MC_SERVER_DISPLAY ?></span>
                <p class="connect-hint muted">Click to copy</p>
            </div>
        </div>
    </div>

    <div class="join-details">
        <div class="join-detail">Java + Bedrock</div>
        <div class="join-detail"><?= mcVersionDisplay() ?></div>
        <div class="join-detail">18+ Only</div>
        <div class="join-detail">Survival &middot; Hard</div>
    </div>

    <div class="wl-info">
        <div class="wl-info-item">
            <h3>How it works</h3>
            <p>We verify your username with Mojang and add you to the whitelist automatically. You can join the server right away.</p>
        </div>
        <div class="wl-info-item">
            <h3>Bedrock players</h3>
            <p>Bedrock players need to be whitelisted too. Ask an admin in <a href="https://discord.gg/UvySgMH" target="_blank" rel="noopener">Discord</a> to add your Xbox gamertag.</p>
        </div>
    </div>

    <div class="join-discord">
        <p>Questions? Come hang out.</p>
        <a href="https://discord.gg/UvySgMH" target="_blank" rel="noopener" class="discord-btn">Join Discord</a>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
