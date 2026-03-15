// ── Copy IP ──────────────────────────────────
function copyText(text) {
    // Try modern API first
    if (navigator.clipboard && window.isSecureContext) {
        return navigator.clipboard.writeText(text);
    }
    // Fallback for HTTP
    return new Promise((resolve, reject) => {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.focus();
        ta.select();
        try {
            document.execCommand('copy') ? resolve() : reject();
        } catch (e) {
            reject(e);
        }
        document.body.removeChild(ta);
    });
}

document.querySelectorAll('.copy-ip').forEach(el => {
    el.addEventListener('click', () => {
        const ip = el.dataset.ip || el.textContent.trim();
        if (!ip) return;
        copyText(ip).then(() => {
            el.classList.add('copied');
            setTimeout(() => el.classList.remove('copied'), 1500);
        }).catch(() => {
            // Last resort: select text so user can Ctrl+C
            const range = document.createRange();
            range.selectNodeContents(el);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
        });
    });
});

// ── Hamburger Nav ────────────────────────────
const toggle = document.querySelector('.nav-toggle');
const links = document.querySelector('.nav-links');

if (toggle && links) {
    toggle.addEventListener('click', () => {
        links.classList.toggle('open');
        toggle.textContent = links.classList.contains('open') ? '\u2715' : '\u2630';
    });

    document.addEventListener('click', (e) => {
        if (!toggle.contains(e.target) && !links.contains(e.target)) {
            links.classList.remove('open');
            toggle.textContent = '\u2630';
        }
    });
}

// ── Whitelist Form ───────────────────────────
const wlForm = document.getElementById('whitelist-form');
if (wlForm) {
    wlForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = wlForm.querySelector('.btn-submit');
        const result = document.getElementById('wl-result');
        const msg = result.querySelector('.result-message');
        const icon = result.querySelector('.result-icon');
        const connect = result.querySelector('.result-connect');

        const username = document.getElementById('mc-username').value.trim();
        const agreedRules = document.getElementById('agree-rules').checked;
        const agreedAge = document.getElementById('agree-age').checked;

        btn.disabled = true;
        btn.textContent = 'Checking...';

        try {
            const res = await fetch('/api/whitelist.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    username,
                    agreed_rules: agreedRules,
                    agreed_age: agreedAge,
                }),
            });

            const data = await res.json();
            result.hidden = false;

            if (data.success) {
                icon.textContent = '\u2713';
                icon.className = 'result-icon success';
                msg.textContent = data.message;
                connect.hidden = false;
                wlForm.style.display = 'none';
            } else {
                icon.textContent = '\u2717';
                icon.className = 'result-icon error';
                msg.textContent = data.error || 'Something went wrong';
                connect.hidden = true;
                btn.disabled = false;
                btn.textContent = 'Whitelist Me';
            }
        } catch {
            result.hidden = false;
            icon.textContent = '\u2717';
            icon.className = 'result-icon error';
            msg.textContent = 'Network error — try again';
            connect.hidden = true;
            btn.disabled = false;
            btn.textContent = 'Whitelist Me';
        }
    });
}

// ── Voting Confetti ──────────────────────────
const voteCards = document.querySelectorAll('.vote-card');
if (voteCards.length) {
    const clicked = new Set();
    voteCards.forEach(card => {
        card.addEventListener('click', () => {
            card.classList.add('voted');
            clicked.add(card.dataset.vote);
            if (clicked.size === voteCards.length) {
                document.querySelector('.voting-page')?.classList.add('all-voted');
            }
        });
    });
}
