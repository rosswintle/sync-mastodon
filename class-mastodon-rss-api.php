<?php
/**
 * Mastodon RSS API class
 *
 * @package SyncMastodon
 */

namespace SyncMastodon;

use SyncMastodon\Sync_Mastodon;
use SyncMastodon\Data\Mastodon_Post;
use SyncMastodon\Sync_Mastodon_Options;

/**
 * Class for using the Mastodon RSS API
 */
class Mastodon_API {

	/**
	 * Fetch the RSS feed
	 *
	 * @return Mastodon_Post[]
	 */
	public function call() {
		// Set the feed cache time
		add_filter( 'wp_feed_cache_transient_lifetime', self::class . '::filter_feed_lifetime', 10, 2 );

		$url = Sync_Mastodon_Options::get_rss_url();

		remove_filter( 'wp_feed_cache_transient_lifetime', self::class . '::filter_feed_lifetime', 10 );

		if ( ! $url ) {
			Sync_Mastodon::log( "Feed URL is not set" );
			return [];
		}

		$feed = fetch_feed( $url );

		if ( is_wp_error( $feed ) ) {
			return [];
		}

		$rss_items = $feed->get_items();

		return $this->make_posts_array( $rss_items );
	}

	/**
	 * Filter the feed cache time
	 *
	 * @param  int $seconds The number of seconds the feed should be cached for.
	 * @param  string $url  The feed URL.
	 * @return int
	 */
	public static function filter_feed_lifetime( $seconds, $url ) {
		return 60 * 5; // 5 minutes
	}

	/**
	 * Convert an array of items returned by the RSS API into an array of
	 * Mastodon_Post objects
	 *
	 * @param  array $items Items to be converted
	 * @return Mastodon_Post[]
	 */
	public function make_posts_array( $items = [] ) {
		$posts = [];

		foreach ( $items as $item ) {
			$post    = Mastodon_Post::from_rss_object( $item );
			$posts[] = $post;
		}

		return $posts;
	}

	public function posts_latest( $options = [] ) {
		// We use a timestamp in a transient to suspend calls for a 1 seconds
		// to avoid completely spamming the API.
		$suspended = get_transient( 'mastodon-posts-all-suspended' );
		if ( false !== $suspended ) {
			Sync_Mastodon::log( "Waiting for API..." );
			// Wait for a second to ensure we are past the transient delay
			sleep( 1 );
		}

		set_transient( 'mastodon-posts-all-suspended', time(), 1 );

		return $this->call();
	}

}
