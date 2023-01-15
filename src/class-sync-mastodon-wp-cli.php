<?php

namespace SyncMastodon;

class Sync_Mastodon_WPCLI {

	public function __construct() {
		if ( class_exists( 'WP_CLI' ) ) {
			\WP_CLI::add_command( 'sync-mastodon', [ $this, 'sync' ] );
		}
	}

	/**
	 * Runs a Mastodon sync. Pull all posts since the last sync.
	 *
	 * If this is the first sync it should pull just all posts in the feed.
	 *
	 * ## OPTIONS
	 *
	 * ## EXAMPLES
	 *
	 *     wp sync-mastodon
	 *
	 * @when after_wp_load
	 */
	public function sync( $args, $assoc_args ) {
		\WP_CLI::log( 'Starting sync' );

		$last_sync = get_option( 'sync-mastodon-last-sync' );
		if ( false === $last_sync ) {
			\WP_CLI::log( 'This is the first sync. If the feed contains a lot of posts then this may take some time.' );
		} else {
			\WP_CLI::log( 'Last sync was: ' . date( 'jS F Y H:i:s', Sync_Mastodon::make_time_local( $last_sync ) ) );
		}

		$suspended = get_transient( 'mastodon-posts-all-suspended' );
		if ( false !== $suspended ) {
			\WP_CLI::log( 'API is suspended - you are not allowed to make a request. Try again shortly.' );
			exit;
		}

		$core = new Sync_Mastodon_Core();
		$core->sync();

		\WP_CLI::log( 'Sync finished' );
	}

}
