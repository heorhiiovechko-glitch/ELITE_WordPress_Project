# Elite Shipping Containers — WordPress Theme Install

This child theme recreates the approved **type1.png** homepage layout for **Elite Shipping Containers Ltd (UK)**.

## What I can vs cannot do

| I can | I cannot |
|------|----------|
| Build theme files for you | Log into your WordPress dashboard directly |
| Match type1 layout & sections | Click Elementor for you |
| Wire WooCommerce product grids | Turn off your VPN remotely |

**You upload this once (~10 minutes).** The homepage will look like the approved design.

---

## Requirements

- WordPress on eliteshippingcontainers.co.uk
- **Astra** theme installed
- **WooCommerce** installed + products imported
- Astrill **OFF** when working on the site

---

## Install steps

### 1. Install Astra (if not installed)
`Appearance → Themes → Add New → search "Astra" → Install → Activate`

### 2. Upload this child theme

**Option A — ZIP upload**
1. Zip the folder `elite-shipping-theme` → `elite-shipping-theme.zip`
2. `Appearance → Themes → Add New → Upload Theme`
3. Choose the zip → Install → **Activate**

**Option B — SiteGround File Manager**
1. Upload folder to `/wp-content/themes/elite-shipping-theme/`
2. `Appearance → Themes → Activate "Elite Shipping Containers"`

### 3. Set homepage
1. `Pages → Add New` → title: **Home** → Publish (empty page is OK — theme uses `front-page.php`)
2. `Settings → Reading → A static page → Homepage: Home`

### 4. Create basic pages
Create and publish:
- **Shop** (WooCommerce may already have this)
- **Get a Quote**
- **About Us**
- **Contact**

### 5. WooCommerce UK settings
`WooCommerce → Settings → General`
- United Kingdom
- Currency: **GBP (£)**

### 6. Live chat (Tawk.to)
The theme loads Tawk.to automatically (same account as the previous site).

1. Ask the client for **Tawk.to login** access
2. In Tawk dashboard → **Allowed Domains** → add `eliteshippingcontainers.co.uk`
3. Tawk’s native widget appears bottom-right (green bubble + “We Are Here!” grabber)
4. No plugin required — embed is built into the theme
5. To change bubble/grabber text: Tawk dashboard → **Channels → Chat Widget → Appearance**

Property ID: `69e5da87d1bb301c336082a1` · Widget ID: `1jmktre0m`

### 7. Clear cache
Click **Purge SG Cache** in WordPress top bar.

---

## After install

Visit: `https://eliteshippingcontainers.co.uk/`

You should see:
- Navy + orange header
- Hero section like type1.png
- Product grids from WooCommerce
- About, modifications, footer
- Tawk.to live chat widget (bottom-right)

---

## Still needed manually (quick)

| Task | Where |
|------|--------|
| Import CSV products | WooCommerce → Import (you already exported) |
| Stripe/PayPal checkout | WooCommerce → Payments |
| UK phone & email in footer | Edit `front-page.php` or customize later |
| Logo image upload | Replace text logo in Customizer or edit theme |
| Quote form | Contact Form 7 on Get a Quote page |

---

## Folder structure

```
elite-shipping-theme/
├── style.css
├── functions.php
├── front-page.php
├── assets/
│   ├── css/main.css
│   └── js/main.js
└── INSTALL.md
```

---

## Support

If homepage shows old layout:
1. Confirm child theme is **activated**
2. Confirm **Home** is set in Settings → Reading
3. Purge SG Cache

If products don't show:
1. Confirm WooCommerce products are imported
2. Check `[products]` shortcode works on a test page
