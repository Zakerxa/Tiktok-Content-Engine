<template>
  <footer class="site-footer">
    <div class="footer-inner">
      <div class="footer-top">

        <div class="footer-brand">
          <a href="/" class="footer-logo">
            <img src="/favicon.png" alt="icon" class="logo-icon" srcset="">
                    <span style="font-size:20px;font-weight:800;letter-spacing:1px;color:#F1F5F9;position: relative;left: -10px;">Z.<span class="pc">A</span>.K.<span class="pc">E</span>.R.<span class="pc">X</span><span>.A</span></span>
          </a>
          <p class="footer-tagline">The AI content studio built for Myanmar TikTok creators.</p>
          <div class="footer-socials">
            <a href="https://www.tiktok.com/@z.a.k.e.r.x.a" target="_blank" rel="noopener" class="social-icon" title="TikTok" aria-label="TikTok">
              <svg viewBox="0 0 24 24" fill="currentColor" class="social-svg">
                <path d="M16.6 5.82c-.9-.98-1.4-2.26-1.4-3.6h-3.1v13.86c0 1.6-1.3 2.9-2.9 2.9a2.9 2.9 0 0 1-2.9-2.9c0-1.6 1.3-2.9 2.9-2.9.3 0 .58.04.85.13V12.2a6.02 6.02 0 0 0-.85-.06 6.02 6.02 0 0 0-6.02 6.02A6.02 6.02 0 0 0 9.2 24.18a6.02 6.02 0 0 0 6.02-6.02V9.7a9.1 9.1 0 0 0 5.3 1.7V8.28a5.9 5.9 0 0 1-3.92-2.46z" transform="translate(0 -2.18)"/>
              </svg>
              <span class="social-label">TikTok</span>
            </a>
            <a href="https://t.me/+PgQX0UwLGIRhYTU1" target="_blank" rel="noopener" class="social-icon" title="Telegram Channel" aria-label="Telegram Channel">
              <svg viewBox="0 0 24 24" fill="currentColor" class="social-svg">
                <path d="M21.94 4.5c.27-1.13-.86-2.03-1.9-1.58L2.4 10.36c-1.16.5-1.14 2.16.03 2.63l4.4 1.77 1.98 6.14c.24.76 1.2.98 1.77.42l2.55-2.48 4.6 3.4c.83.6 2.02.16 2.24-.85L21.94 4.5zM8.53 13.6l9.1-5.7c.34-.2.66.23.36.48l-7.6 6.72a1 1 0 0 0-.32.6l-.2 2.02-1.34-4.12z"/>
              </svg>
              <span class="social-label">Channel</span>
            </a>
            <a href="https://t.me/+6hc4y3AceQJmNTQ1" target="_blank" rel="noopener" class="social-icon" title="Telegram Group" aria-label="Telegram Group">
              <svg viewBox="0 0 24 24" fill="currentColor" class="social-svg">
                <path d="M21.94 4.5c.27-1.13-.86-2.03-1.9-1.58L2.4 10.36c-1.16.5-1.14 2.16.03 2.63l4.4 1.77 1.98 6.14c.24.76 1.2.98 1.77.42l2.55-2.48 4.6 3.4c.83.6 2.02.16 2.24-.85L21.94 4.5zM8.53 13.6l9.1-5.7c.34-.2.66.23.36.48l-7.6 6.72a1 1 0 0 0-.32.6l-.2 2.02-1.34-4.12z"/>
              </svg>
              <span class="social-label">Group</span>
            </a>
          </div>
        </div>

        <div class="footer-links-group mt-5">
          <h4 class="footer-group-title">Products</h4>
          <a href="/movie-recap" class="footer-link">Movie Recap Studio</a>
          <a href="/blogs" class="footer-link">TikTok Posts Generator</a>
          <a href="/dashboard" class="footer-link">Dashboard</a>
        </div>

        <div class="footer-links-group mt-5">
          <h4 class="footer-group-title">Account</h4>
          <a href="/auth/google" class="footer-link">Sign In with Google</a>
          <a href="/profile" class="footer-link">My Profile</a>
          <a href="#pricing" class="footer-link">Upgrade Plan</a>
        </div>

        <div class="footer-links-group mt-5">
          <h4 class="footer-group-title">Support</h4>
          <a href="/Disclaimer" class="footer-link">Disclaimer</a>
          <a href="/Policy" class="footer-link">Privacy & Policy</a>
          <a href="/ContactUs" class="footer-link">Contact Us</a>
        </div>

      </div>

      <div class="footer-bottom">
        <p class="footer-copy">© {{ year }} Z.A.K.E.R.X.A Studio. All rights reserved.</p>
        <p class="footer-made">Made with ♥ for Myanmar Creators</p>
      </div>
    </div>
  </footer>
</template>

<script setup>
const year = new Date().getFullYear();
</script>

<style >
.site-footer {
  background: #060810;
  border-top: 1px solid rgba(255,255,255,0.06);
  padding: 80px 0 40px;
}
.footer-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
}
.footer-top {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 48px;
  margin-bottom: 56px;
}
@media (max-width: 900px) {
  .footer-top { grid-template-columns: 1fr 1fr; gap: 32px; }
}
@media (max-width: 480px) {
  .footer-top { grid-template-columns: 1fr; }
}
.footer-logo {
  display: flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
  margin-bottom: 16px;
}
.footer-tagline {
  font-size: 14px;
  color: #475569;
  line-height: 1.6;
  margin-bottom: 20px;
  max-width: 240px;
}
.footer-socials {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}
.social-icon {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  font-weight: 600;
  color: #94A3B8;
  text-decoration: none;
  padding: 8px 14px;
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 8px;
  background: rgba(255,255,255,0.02);
  transition: color 0.2s, border-color 0.2s, background 0.2s, transform 0.15s;
}
.social-icon:hover {
  color: #F1F5F9;
  border-color: #7C3AED;
  background: rgba(124,58,237,0.12);
  transform: translateY(-1px);
}
.social-svg {
  width: 16px;
  height: 16px;
  flex-shrink: 0;
}
.social-label {
  white-space: nowrap;
}
.footer-links-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.footer-group-title {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  color: #334155;
  margin-bottom: 4px;
}
.footer-link {
  font-size: 14px;
  color: #475569;
  text-decoration: none;
  transition: color 0.2s;
}
.footer-link:hover { color: #94A3B8; }
.footer-bottom {
  border-top: 1px solid rgba(255,255,255,0.05);
  padding-top: 32px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
}
.footer-copy, .footer-made {
  font-size: 13px;
  color: #334155;
}
</style>