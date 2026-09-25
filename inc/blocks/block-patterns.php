<?php
/**
 * Gutenberg Block Patterns - Artisan Chocolates & Confectionery
 *
 * @package Neebites
 * @version 2.0.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Register Neebites Pattern Categories and Patterns
 */
function neebites_register_block_patterns() {
    if (!function_exists('register_block_pattern_category')) return;

    register_block_pattern_category('neebites-confectionery', [
        'label' => esc_html__('Neebites Chocolatier & Confectionery', 'neebites'),
    ]);

    // 1. Hero Banner Pattern
    register_block_pattern('neebites/hero-banner', [
        'title'       => esc_html__('Artisan Chocolates Hero Banner', 'neebites'),
        'description' => esc_html__('Full-width artisan confectionery hero banner with dark cocoa styling, CTAs, and perks.', 'neebites'),
        'categories'  => ['neebites-confectionery', 'header', 'featured'],
        'content'     => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"20px","right":"20px"}},"color":{"background":"#3D2314","text":"#ffffff"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-text-color has-background" style="background-color:#3D2314;color:#ffffff;padding-top:80px;padding-right:20px;padding-bottom:80px;padding-left:20px">
    <!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
    <div class="wp-block-columns alignwide are-vertically-aligned-center">
        <!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
        <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%">
            <!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"2px","fontWeight":"700"}},"textColor":"accent"} -->
            <p class="has-accent-color has-text-color" style="letter-spacing:2px;font-weight:700;text-transform:uppercase;color:#C59B27">🍫 Fresh Roastery Drop</p>
            <!-- /wp:paragraph -->
            <!-- wp:heading {"level":1,"style":{"typography":{"lineHeight":"1.15","fontSize":"3.2rem"},"color":{"text":"#ffffff"}},"textColor":"white"} -->
            <h1 class="wp-block-heading has-white-color has-text-color" style="font-size:3.2rem;line-height:1.15;color:#ffffff">Handcrafted <em style="color:#C59B27;font-style:italic">Artisan Chocolates</em> &amp; Confectionery</h1>
            <!-- /wp:heading -->
            <!-- wp:paragraph {"style":{"typography":{"fontSize":"1.15rem"}}} -->
            <p style="font-size:1.15rem;color:#FAF6F0">Slow-roasted California nonpareil almonds enrobed in silky single-origin Belgian chocolate, tangy fruit glazes, and Bourbon vanilla. Delivered cold-pack fresh.</p>
            <!-- /wp:paragraph -->
            <!-- wp:buttons {"style":{"spacing":{"margin":{"top":"28px"}}}} -->
            <div class="wp-block-buttons" style="margin-top:28px">
                <!-- wp:button {"style":{"color":{"background":"#C59B27","text":"#241408"},"border":{"radius":"30px"}},"fontSize":"medium"} -->
                <div class="wp-block-button has-custom-font-size has-medium-font-size"><a class="wp-block-button__link has-text-color has-background wp-element-button" href="/shop" style="border-radius:30px;background-color:#C59B27;color:#241408;font-weight:700;padding:14px 28px">Explore Chocolates</a></div>
                <!-- /wp:button -->
                <!-- wp:button {"style":{"color":{"background":"rgba(255,255,255,0.15)","text":"#ffffff"},"border":{"radius":"30px"}},"fontSize":"medium"} -->
                <div class="wp-block-button has-custom-font-size has-medium-font-size"><a class="wp-block-button__link has-text-color has-background wp-element-button" href="/product-category/chocolate-coated-almonds/" style="border-radius:30px;background-color:rgba(255,255,255,0.15);color:#ffffff;font-weight:600;padding:14px 28px">Flavour Variants 🍫</a></div>
                <!-- /wp:button -->
            </div>
            <!-- /wp:buttons -->
        </div>
        <!-- /wp:column -->
        <!-- wp:column {"verticalAlignment":"center","width":"45%"} -->
        <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%">
            <!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"hero-plant-illustration"} -->
            <figure class="wp-block-image size-large hero-plant-illustration"><img src="' . esc_url(get_template_directory_uri() . '/assets/images/hero-chocolate.svg') . '" alt="Artisan Chocolate Coated Almonds" /></figure>
            <!-- /wp:image -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->',
    ]);

    // 2. Benefits / Perks Bar (4 Columns)
    register_block_pattern('neebites/benefits-bar', [
        'title'       => esc_html__('Confectionery Freshness & Craft Benefits Bar', 'neebites'),
        'description' => esc_html__('4 columns highlighting cocoa butter purity, cold packaging, and roast freshness.', 'neebites'),
        'categories'  => ['neebites-confectionery', 'columns'],
        'content'     => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"36px","bottom":"36px","left":"20px","right":"20px"}},"color":{"background":"#FAF6F0"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-background" style="background-color:#FAF6F0;padding-top:36px;padding-right:20px;padding-bottom:36px;padding-left:20px;border-bottom:1px solid #EAE0D5">
    <!-- wp:columns {"align":"wide"} -->
    <div class="wp-block-columns alignwide">
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"1.1rem"}},"color":{"text":"#3D2314"}} -->
            <h4 class="wp-block-heading" style="font-size:1.1rem;color:#3D2314">🍫 100% Pure Cocoa Butter</h4>
            <!-- /wp:heading -->
            <!-- wp:paragraph {"style":{"typography":{"fontSize":"0.95rem"}},"color":{"text":"#5C3D2E"}} -->
            <p style="font-size:0.95rem;color:#5C3D2E">Zero palm oil, zero compound fat, and zero hydrogenated oils. Pure indulgence.</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"1.1rem"}},"color":{"text":"#3D2314"}} -->
            <h4 class="wp-block-heading" style="font-size:1.1rem;color:#3D2314">❄️ Cold-Pack Insulated Delivery</h4>
            <!-- /wp:heading -->
            <!-- wp:paragraph {"style":{"typography":{"fontSize":"0.95rem"}},"color":{"text":"#5C3D2E"}} -->
            <p style="font-size:0.95rem;color:#5C3D2E">Temperature-controlled chill boxing guarantees arrival in glossy, solid perfection.</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"1.1rem"}},"color":{"text":"#3D2314"}} -->
            <h4 class="wp-block-heading" style="font-size:1.1rem;color:#3D2314">🥜 California Nonpareil Almonds</h4>
            <!-- /wp:heading -->
            <!-- wp:paragraph {"style":{"typography":{"fontSize":"0.95rem"}},"color":{"text":"#5C3D2E"}} -->
            <p style="font-size:0.95rem;color:#5C3D2E">Sweet-kernel Californian almonds slow batch-roasted for unbeatable nutty crunch.</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"1.1rem"}},"color":{"text":"#3D2314"}} -->
            <h4 class="wp-block-heading" style="font-size:1.1rem;color:#3D2314">✨ Master Chocolatier Recipes</h4>
            <!-- /wp:heading -->
            <!-- wp:paragraph {"style":{"typography":{"fontSize":"0.95rem"}},"color":{"text":"#5C3D2E"}} -->
            <p style="font-size:0.95rem;color:#5C3D2E">Signature kiwi fruit glazes, French Fleur de Sel, and authentic Japanese matcha.</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->',
    ]);

    // 3. Category Circle Grid Pattern (User's Hierarchy)
    register_block_pattern('neebites/category-circles', [
        'title'       => esc_html__('Chocolate Flavour Variants Circular Grid', 'neebites'),
        'description' => esc_html__('Circular category tiles for Dark, Kiwi, Milk, White, and Gourmet Almond Variants.', 'neebites'),
        'categories'  => ['neebites-confectionery'],
        'content'     => '<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"50px","bottom":"50px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="padding-top:50px;padding-bottom:50px">
    <!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"2.2rem"}},"color":{"text":"#3D2314"}} -->
    <h2 class="wp-block-heading has-text-align-center" style="font-size:2.2rem;color:#3D2314">Shop by Confectionery Collection</h2>
    <!-- /wp:heading -->
    <!-- wp:paragraph {"textAlign":"center","style":{"spacing":{"margin":{"bottom":"40px"}}},"color":{"text":"#5C3D2E"}} -->
    <p class="has-text-align-center" style="margin-bottom:40px;color:#5C3D2E">Explore our handcrafted chocolate coated almonds and signature fruit glazes</p>
    <!-- /wp:paragraph -->
    <!-- wp:columns {"align":"wide"} -->
    <div class="wp-block-columns alignwide">
        <div class="wp-block-column" style="text-align:center;padding:10px">
            <div style="width:110px;height:110px;border-radius:50%;background:#FAF6F0;border:2px solid #EAE0D5;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;font-size:42px">🍫</div>
            <h4 style="margin:0 0 6px"><a href="/product-category/chocolate-coated-nuts/chocolate-coated-almonds/dark-chocolate-almond/" style="text-decoration:none;color:#3D2314;font-weight:700">Dark Chocolate Almond</a></h4>
            <p style="font-size:13px;color:#C59B27;font-weight:600;margin:0">70% Single-Origin</p>
        </div>
        <div class="wp-block-column" style="text-align:center;padding:10px">
            <div style="width:110px;height:110px;border-radius:50%;background:#FAF6F0;border:2px solid #EAE0D5;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;font-size:42px">🥝</div>
            <h4 style="margin:0 0 6px"><a href="/product-category/chocolate-coated-nuts/chocolate-coated-almonds/kiwi-chocolate-almond/" style="text-decoration:none;color:#3D2314;font-weight:700">Kiwi Chocolate Almond</a></h4>
            <p style="font-size:13px;color:#C59B27;font-weight:600;margin:0">Signature Real Fruit</p>
        </div>
        <div class="wp-block-column" style="text-align:center;padding:10px">
            <div style="width:110px;height:110px;border-radius:50%;background:#FAF6F0;border:2px solid #EAE0D5;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;font-size:42px">🥛</div>
            <h4 style="margin:0 0 6px"><a href="/product-category/chocolate-coated-nuts/chocolate-coated-almonds/milk-chocolate-almond/" style="text-decoration:none;color:#3D2314;font-weight:700">Milk Chocolate Almond</a></h4>
            <p style="font-size:13px;color:#C59B27;font-weight:600;margin:0">38% Swiss Velvet</p>
        </div>
        <div class="wp-block-column" style="text-align:center;padding:10px">
            <div style="width:110px;height:110px;border-radius:50%;background:#FAF6F0;border:2px solid #EAE0D5;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;font-size:42px">🤍</div>
            <h4 style="margin:0 0 6px"><a href="/product-category/chocolate-coated-nuts/chocolate-coated-almonds/white-chocolate-almond/" style="text-decoration:none;color:#3D2314;font-weight:700">White Chocolate Almond</a></h4>
            <p style="font-size:13px;color:#C59B27;font-weight:600;margin:0">Bourbon Vanilla</p>
        </div>
        <div class="wp-block-column" style="text-align:center;padding:10px">
            <div style="width:110px;height:110px;border-radius:50%;background:#FAF6F0;border:2px solid #EAE0D5;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;font-size:42px">✨</div>
            <h4 style="margin:0 0 6px"><a href="/product-category/chocolate-coated-nuts/chocolate-coated-almonds/other-flavoured-almonds/" style="text-decoration:none;color:#3D2314;font-weight:700">Other Flavoured Almonds</a></h4>
            <p style="font-size:13px;color:#C59B27;font-weight:600;margin:0">Caramel, Matcha, Ruby</p>
        </div>
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->',
    ]);

    // 4. Cocoa Club Newsletter Pattern
    register_block_pattern('neebites/green-club', [
        'title'       => esc_html__('Cocoa Club 10% Off Newsletter', 'neebites'),
        'description' => esc_html__('High-converting newsletter banner offering 10% discount on artisan chocolates.', 'neebites'),
        'categories'  => ['neebites-confectionery', 'buttons'],
        'content'     => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"60px","bottom":"60px","left":"20px","right":"20px"}},"color":{"background":"#231205","text":"#ffffff"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-text-color has-background" style="background-color:#231205;color:#ffffff;padding-top:60px;padding-right:20px;padding-bottom:60px;padding-left:20px">
    <div style="max-width:680px;margin:0 auto;text-align:center">
        <span style="display:inline-block;padding:4px 14px;border-radius:20px;background:rgba(197,155,39,0.2);color:#C59B27;font-size:13px;font-weight:700;margin-bottom:16px;letter-spacing:1px">JOIN 50,000+ CHOCOLATE CONNOISSEURS</span>
        <h2 style="font-size:2.4rem;color:#ffffff;margin:0 0 12px">Unlock 10% Off Your First Chocolate Box</h2>
        <p style="font-size:1.1rem;opacity:0.9;color:#FAF6F0;margin:0 0 28px">Sign up for secret roastery batches, seasonal fruit glaze drops, and member-only gifting perks.</p>
        <form style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap">
            <input type="email" placeholder="Enter your email address..." required style="padding:14px 20px;border-radius:30px;border:none;flex:1;min-width:260px;font-size:16px;outline:none;background:#FAF6F0;color:#231205" />
            <button type="submit" style="padding:14px 28px;border-radius:30px;border:none;background:#C59B27;color:#241408;font-weight:700;font-size:16px;cursor:pointer">Claim 10% Off</button>
        </form>
    </div>
</div>
<!-- /wp:group -->',
    ]);

    // 5. The Alchemy of Pure Cocoa & Roasted Almonds Pattern
    register_block_pattern('neebites/plant-care-guide', [
        'title'       => esc_html__('Chocolate Craft & Roasting Wisdom', 'neebites'),
        'description' => esc_html__('4 essential pillars of artisan confectionery: Pure Cocoa Butter, Roast Profiles, Real Fruit, Cold Chain.', 'neebites'),
        'categories'  => ['neebites-confectionery', 'columns'],
        'content'     => '<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"60px","bottom":"60px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="padding-top:60px;padding-bottom:60px">
    <div style="text-align:center;max-width:650px;margin:0 auto 40px">
        <span style="display:inline-block;color:#C59B27;font-weight:700;font-size:13px;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:8px">THE CHOCOLATIER PHILOSOPHY</span>
        <h2 style="font-size:2.2rem;color:#3D2314;margin:0 0 12px">The Alchemy of Pure Cocoa &amp; Roasted Nuts</h2>
        <p style="color:#5C3D2E;font-size:1.05rem;line-height:1.6">Every single batch is tempered with precision and crafted with slow-roasted California almonds.</p>
    </div>
    <!-- wp:columns {"align":"wide"} -->
    <div class="wp-block-columns alignwide" style="gap:24px">
        <div class="wp-block-column" style="background:#ffffff;border:1px solid #EAE0D5;border-radius:14px;padding:28px 24px;box-shadow:0 4px 16px rgba(61,35,20,0.06)">
            <div style="font-size:36px;margin-bottom:14px">🍫</div>
            <h3 style="font-size:1.25rem;color:#3D2314;margin:0 0 10px">100% Pure Cocoa Butter</h3>
            <p style="color:#5C3D2E;font-size:0.95rem;line-height:1.6">Zero vegetable fats or artificial shortening. We only use authentic cocoa butter that melts at body temperature.</p>
        </div>
        <div class="wp-block-column" style="background:#ffffff;border:1px solid #EAE0D5;border-radius:14px;padding:28px 24px;box-shadow:0 4px 16px rgba(61,35,20,0.06)">
            <div style="font-size:36px;margin-bottom:14px">🥜</div>
            <h3 style="font-size:1.25rem;color:#3D2314;margin:0 0 10px">Batch Almond Roasting</h3>
            <p style="color:#5C3D2E;font-size:0.95rem;line-height:1.6">California Nonpareil almonds roasted in small batches to preserve their delicate natural oils and crisp texture.</p>
        </div>
        <div class="wp-block-column" style="background:#ffffff;border:1px solid #EAE0D5;border-radius:14px;padding:28px 24px;box-shadow:0 4px 16px rgba(61,35,20,0.06)">
            <div style="font-size:36px;margin-bottom:14px">🥝</div>
            <h3 style="font-size:1.25rem;color:#3D2314;margin:0 0 10px">Signature Fruit Infusions</h3>
            <p style="color:#5C3D2E;font-size:0.95rem;line-height:1.6">Real fruit powders and glazes deliver an authentic burst of sweet tartness in harmony with white chocolate.</p>
        </div>
        <div class="wp-block-column" style="background:#ffffff;border:1px solid #EAE0D5;border-radius:14px;padding:28px 24px;box-shadow:0 4px 16px rgba(61,35,20,0.06)">
            <div style="font-size:36px;margin-bottom:14px">❄️</div>
            <h3 style="font-size:1.25rem;color:#3D2314;margin:0 0 10px">Cold-Chain Guaranteed</h3>
            <p style="color:#5C3D2E;font-size:0.95rem;line-height:1.6">Insulated packaging with food-grade gel ice packs shields your sweets against transit temperatures.</p>
        </div>
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->',
    ]);

    // 6. Customer Reviews Pattern (Social Proof)
    register_block_pattern('neebites/customer-reviews', [
        'title'       => esc_html__('Chocolate Connoisseur Reviews', 'neebites'),
        'description' => esc_html__('3 verified reviews from confectionery lovers with star ratings and avatars.', 'neebites'),
        'categories'  => ['neebites-confectionery', 'columns'],
        'content'     => '<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"50px","bottom":"50px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="padding-top:50px;padding-bottom:50px">
    <div style="text-align:center;margin-bottom:40px">
        <span style="color:#C59B27;font-size:22px;letter-spacing:4px">★★★★★</span>
        <h2 style="font-size:2.2rem;color:#3D2314;margin:8px 0">Loved by 18,000+ Chocolate Enthusiasts</h2>
        <p style="color:#5C3D2E">Real feedback and tasting notes from our gourmet confectionery community</p>
    </div>
    <!-- wp:columns {"align":"wide"} -->
    <div class="wp-block-columns alignwide" style="gap:24px">
        <div class="wp-block-column" style="background:#FAF6F0;border:1px solid #EAE0D5;border-radius:14px;padding:28px 24px;display:flex;flex-direction:column">
            <div style="color:#C59B27;font-size:16px;margin-bottom:12px">★★★★★</div>
            <p style="color:#3D2314;font-style:italic;line-height:1.6;flex:1;margin-bottom:20px">"The Kiwi Chocolate Almond is a revelation! Tangy fruit glaze paired with rich white chocolate and a crisp roasted almond crunch. We reordered 3 boxes!"</p>
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:44px;height:44px;border-radius:50%;background:#3D2314;color:#FAF6F0;display:flex;align-items:center;justify-content:center;font-size:20px">🥝</div>
                <div>
                    <strong style="display:block;color:#3D2314;font-size:14px">Sophie L.</strong>
                    <span style="font-size:12px;color:#C59B27">🍫 Verified Connoisseur</span>
                </div>
            </div>
        </div>
        <div class="wp-block-column" style="background:#FAF6F0;border:1px solid #EAE0D5;border-radius:14px;padding:28px 24px;display:flex;flex-direction:column">
            <div style="color:#C59B27;font-size:16px;margin-bottom:12px">★★★★★</div>
            <p style="color:#3D2314;font-style:italic;line-height:1.6;flex:1;margin-bottom:20px">"70% Dark Chocolate Almond is perfection. Genuine Belgian cocoa with balanced intensity and perfectly roasted California almonds."</p>
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:44px;height:44px;border-radius:50%;background:#3D2314;color:#FAF6F0;display:flex;align-items:center;justify-content:center;font-size:20px">🍫</div>
                <div>
                    <strong style="display:block;color:#3D2314;font-size:14px">Marcus V.</strong>
                    <span style="font-size:12px;color:#C59B27">✨ Dark Cocoa Purist</span>
                </div>
            </div>
        </div>
        <div class="wp-block-column" style="background:#FAF6F0;border:1px solid #EAE0D5;border-radius:14px;padding:28px 24px;display:flex;flex-direction:column">
            <div style="color:#C59B27;font-size:16px;margin-bottom:12px">★★★★★</div>
            <p style="color:#3D2314;font-style:italic;line-height:1.6;flex:1;margin-bottom:20px">"Arrived in scorching 38°C weather completely solid and cold inside the insulated pouch. Outstanding product and delivery service."</p>
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:44px;height:44px;border-radius:50%;background:#3D2314;color:#FAF6F0;display:flex;align-items:center;justify-content:center;font-size:20px">❄️</div>
                <div>
                    <strong style="display:block;color:#3D2314;font-size:14px">Emma K.</strong>
                    <span style="font-size:12px;color:#C59B27">🎁 Cocoa Club Member</span>
                </div>
            </div>
        </div>
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->',
    ]);

    // 7. Instagram Confectionery Community Pattern
    register_block_pattern('neebites/instagram-community', [
        'title'       => esc_html__('Instagram #NeebitesChocolates Gallery', 'neebites'),
        'description' => esc_html__('Community showcase with handle tag and follow button.', 'neebites'),
        'categories'  => ['neebites-confectionery', 'gallery'],
        'content'     => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"50px","bottom":"60px","left":"20px","right":"20px"}},"color":{"background":"#FAF6F0"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-background" style="background-color:#FAF6F0;padding-top:50px;padding-right:20px;padding-bottom:60px;padding-left:20px">
    <div style="text-align:center;margin-bottom:32px">
        <span style="font-size:13px;font-weight:700;color:#C59B27;letter-spacing:2px;text-transform:uppercase">TAG US TO BE FEATURED</span>
        <h2 style="font-size:2.2rem;color:#3D2314;margin:6px 0 8px">#NeebitesChocolates</h2>
        <p style="color:#5C3D2E;margin:0 0 16px">Share your chocolate unboxing moments and confectionery pairings with our community.</p>
        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" style="display:inline-block;padding:10px 24px;border-radius:30px;background:#3D2314;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600">Follow @neebites.chocolates &rarr;</a>
    </div>
</div>
<!-- /wp:group -->',
    ]);
}
add_action('init', 'neebites_register_block_patterns');
