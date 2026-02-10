/** GDPR Consent Tracking [#84] **/
function checkGdprConsent() {
    if (!localStorage.getItem('gdpr_consent')) {
        const banner = document.createElement('div');
        banner.id = 'gdpr-banner';
        banner.style = 'position:fixed; bottom:0; width:100%; background:#161b22; color:white; padding:20px; z-index:9999; border-top:1px solid var(--accent-color); display:flex; justify-content:space-between; align-items:center;';
        banner.innerHTML = `
            <div>🛡️ We value your privacy. By using this system, you agree to our data processing terms (GDPR compliant).</div>
            <button onclick="acceptGdpr()" class="btn btn-primary" style="margin-left:20px;">I AGREE</button>
        `;
        document.body.appendChild(banner);
    }
}

function acceptGdpr() {
    localStorage.setItem('gdpr_consent', 'true');
    document.getElementById('gdpr-banner').remove();
    // Optional: Log to server
}

window.addEventListener('load', checkGdprConsent);
