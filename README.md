# Neebites — Artisan Confectionery WordPress & WooCommerce Theme

> **Active Theme Version**: `2.7.3`  
> **Repository**: [https://github.com/dtbrand/neebites](https://github.com/dtbrand/neebites)  
> **Production Site**: [darkcyan-sardine-448791.hostingersite.com](http://darkcyan-sardine-448791.hostingersite.com/)

A bespoke, high-converting luxury confectionery WooCommerce theme engineered for **Neebites Artisan Confectionery**. Featuring Myntra-grade product display architecture, Woodmart Fashion-2 express checkout flow, automated headless test suites, and 100% mobile-optimized touch experiences.

---

## Key Features

1. **Myntra-Grade Single Product Page (PDP)**
   - Interactive ivory stage photo gallery with cursor-following hover zoom (`scale(1.35)`).
   - Horizontal touch-swipe mobile carousel with scroll-snap and pagination dots.
   - Zero-dependency fullscreen darkroom lightbox modal (`z-index: 999999999`).
   - Pinned desktop buy box with smooth sticky scrolling.
   - Dual high-converting CTAs: Dark Cocoa `[ ADD TO BAG ]` and Sunset Amber `[ ⚡ BUY NOW ]`.
   - Food-grade rounded interactive quantity stepper with bidirectional synchronization.
   - Automated pack size weight variation selection and options modal sheet.

2. **Woodmart Fashion-2 Express Checkout**
   - Clean, focused 2-column checkout layout with 30px column gap and sticky order sidebar.
   - Deep forest green (`#1B3B2B`) checkout steps header (`SHOPPING CART → CHECKOUT → ORDER COMPLETE`).
   - Clean 44-50px white field boxes with `#D5C9BD` border and dark cocoa focus ring.
   - Receipt-style `YOUR ORDER` review table with inline stepper quantity updater.
   - Full-width deep forest green `[ PLACE ORDER ]` CTA with 256-bit SSL trust architecture.

3. **Modernized Shop & Category Catalog**
   - Full-width 4-column responsive confectionery catalog on desktop (`> 900px`).
   - Strict 2-column mobile layout (`<= 900px`) with touch-friendly 10-12px gutters.
   - Native app-grade horizontal swipeable flavour rail on mobile (`overflow-x: auto`).
   - Ultra-luxury slide-out off-canvas filter drawer (`#shop-secondary`).
   - Photorealistic confectionery packaging tin photography across all cards.
   - Clean typography with zero clipping and streamlined modern pricing (Current Price + Discount % OFF).

4. **WordPress Admin Product Specifications**
   - Native WordPress 6.x admin meta box and WooCommerce Product Data tab.
   - 6 artisan confectionery pillar fields (Cocoa Profile, Nut Roasting, Glaze & Flavor, Dietary Integrity, Shelf Life, Packaging).
   - Dynamic custom specifications repeater with 1-click presets.

---

## Theme Architecture

```text
neebites/
├── style.css                      # Theme declaration & global styling (v2.7.3)
├── functions.php                  # Theme bootstrap, hooks, and dynamic controllers
├── header.php                     # Global site header, sticky navigation, search
├── footer.php                     # Luxury site footer & sticky bottom buy bar
├── front-page.php                 # Artisan confectionery homepage template
├── index.php                      # Fallback index template
├── sidebar.php / sidebar-shop.php # Blog & WooCommerce catalog sidebar widgets
├── assets/
│   ├── css/
│   │   └── woocommerce.css        # Full WooCommerce stylesheet & responsive rules
│   ├── js/
│   │   └── main.js                # Frontend controllers (stepper, gallery, checkout)
│   └── images/                    # Packaging photography & artisan brand assets
├── inc/
│   ├── core/                      # Theme setup, assets enqueuing, customizer
│   └── woocommerce/               # Product loop, variation sync, cart & checkout hooks
├── woocommerce/                   # WooCommerce template overrides
│   └── single-product/
│       ├── product-image.php      # Myntra-grade product gallery override
│       └── related.php            # Balanced 4-card related products override
└── AGENTS.md                      # Master agent testing, audit & defect logs (105/105 tests)
```

---

## Automated In-Agent Testing

Every component is continuously validated via the inside-agent headless test suite (`105/105` automated checks passing):

- Zero live browser window popups.
- HTTP status 200 OK, full DOM parsing, and CSS/JS asset integrity verification.
- Mobile and desktop responsive layout parity checks.

---

## Author & License

- **Brand**: Neebites Artisan Confectionery
- **Developer**: dtbrand (`gkv9006@gmail.com`)
- **Version**: 2.7.3
