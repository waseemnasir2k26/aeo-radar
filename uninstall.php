<?php
/**
 * Uninstall cleanup.
 *
 * AEO Radar stores no options or post meta of its own (scores are computed
 * on demand), so there is nothing persistent to remove.
 *
 * @package AEO_Radar
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
