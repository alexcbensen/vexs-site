<?php
$pageTitle = 'Commands - Neopolitan';
require __DIR__ . '/includes/header.php';
?>

<section class="commands-page">
    <h1>Commands</h1>
    <p class="muted">Player commands available on Neopolitan</p>

    <div class="cmd-category">
        <h2>Teleportation</h2>
        <div class="cmd-list">
            <div class="cmd">
                <code>/spawn</code>
                <p>Teleport to the server spawn point</p>
            </div>
            <div class="cmd">
                <code>/home</code>
                <p>Teleport to your saved home location</p>
            </div>
            <div class="cmd">
                <code>/sethome</code>
                <p>Set your home to your current location</p>
            </div>
            <div class="cmd">
                <code>/delhome</code>
                <p>Delete your saved home</p>
            </div>
            <div class="cmd">
                <code>/back</code>
                <p>Return to your previous location (after teleporting or dying)</p>
            </div>
            <div class="cmd">
                <code>/tpa &lt;player&gt;</code>
                <p>Request to teleport to another player</p>
            </div>
            <div class="cmd">
                <code>/tpaccept</code>
                <p>Accept a teleport request</p>
            </div>
            <div class="cmd">
                <code>/tpdeny</code>
                <p>Deny a teleport request</p>
            </div>
        </div>
    </div>

    <div class="cmd-category">
        <h2>Land Claims</h2>
        <p class="cmd-note">Claims protect your builds from griefing. Grab a <strong>golden shovel</strong> and right-click two opposite corners to create one.</p>
        <div class="cmd-list">
            <div class="cmd">
                <code>/trust &lt;player&gt;</code>
                <p>Give a player full build access in your claim</p>
            </div>
            <div class="cmd">
                <code>/containertrust &lt;player&gt;</code>
                <p>Give a player access to chests, doors, and buttons in your claim</p>
            </div>
            <div class="cmd">
                <code>/accesstrust &lt;player&gt;</code>
                <p>Give a player access to buttons, levers, and beds in your claim</p>
            </div>
            <div class="cmd">
                <code>/untrust &lt;player&gt;</code>
                <p>Remove a player's permissions in your claim</p>
            </div>
            <div class="cmd">
                <code>/trustlist</code>
                <p>See who has permissions in your current claim</p>
            </div>
            <div class="cmd">
                <code>/abandonclaim</code>
                <p>Delete the claim you're standing in</p>
            </div>
            <div class="cmd">
                <code>/abandonallclaims</code>
                <p>Delete all of your claims</p>
            </div>
            <div class="cmd">
                <code>/claimslist</code>
                <p>List all your claims and their locations</p>
            </div>
            <div class="cmd">
                <code>/trapped</code>
                <p>Escape if you're stuck inside someone else's claim</p>
            </div>
        </div>
    </div>

    <div class="cmd-category">
        <h2>Communication</h2>
        <div class="cmd-list">
            <div class="cmd">
                <code>/msg &lt;player&gt; &lt;message&gt;</code>
                <p>Send a private message to another player</p>
            </div>
            <div class="cmd">
                <code>/r &lt;message&gt;</code>
                <p>Reply to the last private message you received</p>
            </div>
            <div class="cmd">
                <code>/mail send &lt;player&gt; &lt;message&gt;</code>
                <p>Send offline mail to a player</p>
            </div>
            <div class="cmd">
                <code>/mail read</code>
                <p>Read your mail</p>
            </div>
            <div class="cmd">
                <code>/mail clear</code>
                <p>Clear all your mail</p>
            </div>
        </div>
    </div>

    <div class="cmd-category">
        <h2>General</h2>
        <div class="cmd-list">
            <div class="cmd">
                <code>/list</code>
                <p>See who's currently online</p>
            </div>
            <div class="cmd">
                <code>/afk</code>
                <p>Toggle AFK status</p>
            </div>
            <div class="cmd">
                <code>/rules</code>
                <p>View the server rules in chat</p>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
