<template>
  <footer class="site-footer">
    <div class="footer-inner">
      <div class="footer-top">

        <div class="footer-brand">
          <a href="/" class="footer-logo">
            <span class="logo-icon">▶</span>
            <span class="logo-text">ReelAI</span>
          </a>
          <p class="footer-tagline">The AI content studio built for Myanmar TikTok creators.</p>
          <div class="footer-socials">
            <a href="#" class="social-link">TikTok</a>
            <a href="#" class="social-link">Facebook</a>
            <a href="#" class="social-link">Telegram</a>
          </div>
        </div>

        <div class="footer-links-group">
          <h4 class="footer-group-title">Products</h4>
          <a href="/recap" class="footer-link">Movie Recap Studio</a>
          <a href="/blogs" class="footer-link">TikTok Blog Generator</a>
          <a href="/dashboard" class="footer-link">Dashboard</a>
        </div>

        <div class="footer-links-group">
          <h4 class="footer-group-title">Account</h4>
          <a href="/auth/google" class="footer-link">Sign In with Google</a>
          <a href="/profile" class="footer-link">My Profile</a>
          <a href="#pricing" class="footer-link">Upgrade Plan</a>
        </div>

        <div class="footer-links-group">
          <h4 class="footer-group-title">Support</h4>
          <a href="#" class="footer-link">How It Works</a>
          <a href="#" class="footer-link">FAQ</a>
          <a href="#" class="footer-link">Contact Us</a>
        </div>

      </div>

      <div class="footer-bottom">
        <p class="footer-copy">© {{ year }} ReelAI Studio. All rights reserved.</p>
        <p class="footer-made">Made with ♥ for Myanmar Creators</p>
      </div>
    </div>
  </footer>
</template>

<script setup>
const year = new Date().getFullYear();
</script>

<style scoped>
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
.logo-icon {
  width: 28px;
  height: 28px;
  background: linear-gradient(135deg, #7C3AED, #06B6D4);
  border-radius: 7px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  color: white;
}
.logo-text {
  font-size: 18px;
  font-weight: 800;
  color: #F1F5F9;
  letter-spacing: -0.5px;
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
.social-link {
  font-size: 12px;
  font-weight: 600;
  color: #475569;
  text-decoration: none;
  padding: 6px 12px;
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 8px;
  transition: color 0.2s, border-color 0.2s;
}
.social-link:hover { color: #F1F5F9; border-color: rgba(255,255,255,0.2); }
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