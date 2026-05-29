<?php
/**
 * Plugin Name:       AEO Radar — Answer Engine Audit & Score
 * Plugin URI:        https://github.com/waseemnasir2k26/aeo-radar
 * Description:       Score how quotable your content is by AI answer engines (ChatGPT, Claude, Perplexity, Gemini). One-click on-page AEO audit per post + a site-wide dashboard, with specific fixes. 100% local — no API, no cloud, no per-use cost. Part of the SkynetLabs AEO Suite.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Waseem Nasir (SkynetLabs)
 * Author URI:        https://www.skynetjoe.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       aeo-radar
 * Domain Path:       /languages
 *
 * @package AEO_Radar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AEOR_VERSION', '1.0.0' );
define( 'AEOR_FILE', __FILE__ );
define( 'AEOR_DIR', plugin_dir_path( __FILE__ ) );
define( 'AEOR_URL', plugin_dir_url( __FILE__ ) );
define( 'AEOR_BASENAME', plugin_basename( __FILE__ ) );

require_once AEOR_DIR . 'includes/class-aeo-radar.php';

/**
 * Boot.
 *
 * @return AEO_Radar
 */
function aeo_radar() {
	return AEO_Radar::instance();
}

aeo_radar();
