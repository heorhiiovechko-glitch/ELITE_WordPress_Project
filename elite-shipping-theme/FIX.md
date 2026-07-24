# FIX: Website Looks Broken / Incomplete

Your screenshots show **CSS not loading** + **Coming Soon mode** active.

## Do these 5 steps (10 minutes)

### 1. Turn OFF Astrill VPN
Required for eliteshippingcontainers.co.uk

### 2. Upload FIXED theme v1.1.0
1. `Appearance → Themes`
2. **Delete** old "Elite Shipping Containers" theme (activate Astra/default first if needed)
3. `Add New → Upload Theme`
4. Upload **`elite-shipping-theme.zip`** (v1.1.0 from project folder)
5. **Activate** it

> v1.1.0 is a **standalone theme** — you do NOT need Astra anymore.

### 3. Purge ALL cache
1. Click **Purge SG Cache** (top WordPress bar)
2. `SiteGround Optimizer → Caching → Purge Cache`
3. Hard refresh browser: **Ctrl + Shift + R**

### 4. Turn OFF "Coming Soon" mode
Your screenshots show: *"This page is in Coming soon mode"*

Fix:
1. `SiteGround → Speed Optimizer` or look for **Coming Soon** plugin
2. OR `Settings → General` — disable maintenance/coming soon
3. OR `Plugins` — deactivate **Coming Soon**, **SeedProd**, or **Maintenance** plugin
4. Site must be **public** for client to see it

### 5. Confirm homepage setting
`Settings → Reading →`
- A static page
- Homepage: **Home**

---

## After fix you should see

- Navy top bar + white header (horizontal menu)
- Dark hero with orange buttons
- Product grid in 4 columns with images
- Dark footer with 4 columns

---

## If CSS still broken

Check CSS loads:
1. Open site in browser
2. Press **F12 → Network tab**
3. Reload page
4. Look for `main.css` — should be **200 OK**

If 404:
- Re-upload theme folder to `/wp-content/themes/elite-shipping-theme/`
- Confirm folder contains `assets/css/main.css`

---

## Product grid broken row

Fixed in v1.1.0:
- Featured products: 4 items (not 8)
- Best sellers: 4 items (not 5)
- Orange Add to Cart buttons
- Grid layout fixes for WooCommerce

---

## Still incomplete after theme fix?

| Missing | Fix |
|---------|-----|
| Checkout | WooCommerce → Settings → Payments → Enable Stripe/PayPal |
| Live chat | Install Tawk.to plugin |
| About/Contact pages | Pages → Add New → publish |
| Logo image | Appearance → Customize → Site Identity → upload logo |
| UK phone | Edit `template-parts/header-site.php` or ask for update |
