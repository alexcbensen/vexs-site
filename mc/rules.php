<?php
$pageTitle = 'Rules - Neopolitan';
require __DIR__ . '/includes/header.php';
?>

<section class="rules-page">
    <h1>Rules</h1>
    <p class="muted">Keep it simple. Be cool. Breaking rules may result in a ban.</p>

    <div class="rules-category">
        <h2>Conduct</h2>
        <ol class="rules-list">
            <li>
                <h3>Be Respectful</h3>
                <p>No harassment, hate speech, slurs, or toxic behavior. We're here to have fun.</p>
            </li>
            <li>
                <h3>18+ Server</h3>
                <p>All players must be 18 or older to join.</p>
            </li>
            <li>
                <h3>No Impersonation</h3>
                <p>Don't impersonate other players or staff.</p>
            </li>
        </ol>
    </div>

    <div class="rules-category">
        <h2>Gameplay</h2>
        <ol class="rules-list" start="4">
            <li>
                <h3>No Griefing</h3>
                <p>Don't destroy or modify other players' builds without permission. If you didn't build it, don't touch it.</p>
            </li>
            <li>
                <h3>No Stealing</h3>
                <p>Don't take items from other players' chests, farms, or builds unless they've given you permission.</p>
            </li>
            <li>
                <h3>PVP is Consensual</h3>
                <p>PVP is enabled but don't attack people who haven't agreed to fight. No spawn killing.</p>
            </li>
            <li>
                <h3>Claim Your Builds</h3>
                <p>Use GriefPrevention to claim your builds. Unclaimed builds may not be recoverable if something happens.</p>
            </li>
        </ol>
    </div>

    <div class="rules-category">
        <h2>Client & Mods</h2>
        <ol class="rules-list" start="8">
            <li>
                <h3>No Cheating</h3>
                <p>No hacked clients, x-ray, duping, or exploits. Play fair.</p>
            </li>
            <li>
                <h3>No Alt Abuse</h3>
                <p>Don't use alt accounts to evade bans or circumvent rules.</p>
            </li>
        </ol>
    </div>

    <div class="rules-category">
        <h2>Technical</h2>
        <ol class="rules-list" start="10">
            <li>
                <h3>No Lag Machines</h3>
                <p>Keep redstone and entity farms reasonable. If it tanks the TPS, it gets removed.</p>
            </li>
            <li>
                <h3>AFK Farms</h3>
                <p>AFK farms are allowed but must not cause server lag. Excessively large farms may be asked to scale down.</p>
            </li>
        </ol>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
