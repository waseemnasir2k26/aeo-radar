<?php
/**
 * Core loader.
 *
 * @package AEO_Radar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class. Singleton.
 */
final class AEO_Radar {

	/**
	 * Single instance.
	 *
	 * @var AEO_Radar|null
	 */
	private static $instance = null;

	/**
	 * Get the singleton.
	 *
	 * @return AEO_Radar
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Wire everything.
	 */
	private function __construct() {
		require_once AEOR_DIR . 'includes/class-aeor-analyzer.php';
		require_once AEOR_DIR . 'includes/class-aeor-meta-box.php';
		require_once AEOR_DIR . 'includes/class-aeor-admin.php';

		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_filter( 'plugin_action_links_' . AEOR_BASENAME, array( $this, 'action_links' ) );

		new AEOR_Meta_Box();
		new AEOR_Admin();
	}

	/**
	 * Translations.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'aeo-radar', false, dirname( AEOR_BASENAME ) . '/languages' );
	}

	/**
	 * Admin styles on editor + our dashboard.
	 *
	 * @param string $hook Current admin page.
	 */
	public function assets( $hook ) {
		$screens = array( 'post.php', 'post-new.php', 'tools_page_aeo-radar' );
		if ( ! in_array( $hook, $screens, true ) ) {
			return;
		}
		wp_enqueue_style( 'aeor-admin', AEOR_URL . 'assets/admin.css', array(), AEOR_VERSION );
	}

	/**
	 * Score → CSS color band.
	 *
	 * @param int $score Score.
	 * @return string Hex color.
	 */
	public static function color( $score ) {
		if ( $score >= 85 ) {
			return '#1a7f37';
		}
		if ( $score >= 70 ) {
			return '#3858e9';
		}
		if ( $score >= 50 ) {
			return '#bd8600';
		}
		return '#b32d2e';
	}

	/**
	 * Settings/dashboard link on the plugins screen.
	 *
	 * @param array $links Links.
	 * @return array
	 */
	public function action_links( $links ) {
		$url  = admin_url( 'tools.php?page=aeo-radar' );
		$link = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'AEO Dashboard', 'aeo-radar' ) . '</a>';
		array_unshift( $links, $link );
		return $links;
	}
}
