<?php
/**
 * Plugin Name:     Sync Mastodon
 * Description:     Fetch posts from a Mastodon RSS feed and add them to a custom post type
 * Author:          Ross Wintle
 * Author URI:      https://rosswintle.uk
 * Text Domain:     sync-mastodon
 * Domain Path:     /languages
 * Version:         1.3.0
 * GitHub Plugin URI: https://github.com/rosswintle/sync-mastodon
 *
 * @package Sync_Mastodon
 */

namespace SyncMastodon;

require_once __DIR__ . '/src/class-sync-mastodon-options.php';
require_once __DIR__ . '/src/post-types/mastodon-post.php';
require_once __DIR__ . '/src/data/class-mastodon-post.php';
require_once __DIR__ . '/src/data/class-mastodon-media.php';
require_once __DIR__ . '/src/post-types/class-mastodon-post.php';
// require_once '/src/taxonomies/mastodon-tag.php';
require_once __DIR__ . '/src/class-sync-mastodon-admin.php';
require_once __DIR__ . '/src/class-sync-mastodon-meta-boxes.php';
require_once __DIR__ . '/src/class-sync-mastodon-cron.php';
require_once __DIR__ . '/src/class-sync-mastodon-wp-cli.php';
require_once __DIR__ . '/src/class-mastodon-rss-api.php';
require_once __DIR__ . '/src/class-sync-mastodon-core.php';
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Sync Mastodon class
 */
class Sync_Mastodon {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Initial hooks.
		add_action( 'init', [ $this, 'init_hooks' ] );
		add_action( 'admin_menu', [ $this, 'admin_menu_hooks' ] );

		$this->register_deactivation_hook();
	}

	/**
	 * Run any init hook actions
	 *
	 * @return void
	 */
	public function init_hooks() {
		new Sync_Mastodon_Cron();
		new Sync_Mastodon_Meta_Boxes();
		new Sync_Mastodon_WPCLI();
	}

	/**
	 * Run any admin menu hook actions
	 *
	 * @return void
	 */
	public function admin_menu_hooks() {
		new Sync_Mastodon_Admin();
	}

	/**
	 * Register a deactivation hook - this will trigger the sync_mastodon_deactivate action
	 * so anything that needs to be done on deactivation should be done using that hook.
	 *
	 * @return void
	 */
	public function register_deactivation_hook() {
		register_deactivation_hook( __FILE__, [ $this, 'run_deactivation_hook' ] );
	}

	/**
	 * This actually does the sync_mastodon_deactivate hook
	 */
	public function run_deactivation_hook() {
		do_action( 'sync_mastodon_deactivate' );
	}

	/**
	 * This takes a timestamp and turns it into local time using the gmt_offset options
	 */
	public static function make_time_local( $timestamp ) {
		$offset_secs = ( (int) get_option( 'gmt_offset' ) ) * 60 * 60;
		return $timestamp + $offset_secs;
	}

	/**
	 * This does information logging based on how the sync has been called
	 */
	public static function log( $message ) {
		if ( class_exists( 'WP_CLI' ) ) {
			\WP_CLI::log( $message );
		}
		return;
	}

	/**
	 * This does error logging based on how the sync has been called
	 */
	public static function error( $message ) {
		if ( class_exists( 'WP_CLI' ) ) {
			\WP_CLI::error( $message );
		}
	}

}

$syncmastodon_instance = new Sync_Mastodon();
