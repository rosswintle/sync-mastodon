<?php
/**
 * Core code for syncing Mastodon posts to WordPress posts
 */

namespace SyncMastodon;

use SyncMastodon\Data\Mastodon_Post;
use SyncMastodon\Mastodon_API;
use SyncMastodon\Sync_Mastodon;
use SyncMastodon\Sync_Mastodon_Options;

/**
 * Core class for syncing
 */
class Sync_Mastodon_Core {

	// Time of last "recent" method call - this can only be called every minute
	// protected $last_recent_call = null;

	// Timestamp of the last sync
	protected $last_sync = null;

	public function __construct() {
		$stored_last_sync = get_option( 'sync-mastodon-last-sync' );

		$this->last_sync = ( false !== $stored_last_sync ) ? $stored_last_sync : 0;
	}

	public function sync() {
		$api = new Mastodon_API();

		Sync_Mastodon::log( 'Getting last post from WordPress' );

		$latest_posts = get_posts(
			[
				'post_type' => Sync_Mastodon_Options::get_post_type(),
				'posts_per_page' => 1,
				'orderby' => 'date',
				'order' => 'DESC',
			]
		);

		if ( empty( $latest_posts ) ) {
			Sync_Mastodon::log( 'No last post found. This will sync all!' );
			$latest_post_date = 0;
		} else {
			Sync_Mastodon::log( 'Last post found: ' . $latest_posts[0]->post_date_gmt );
			$latest_post_date = strtotime( $latest_posts[0]->post_date_gmt );
		}

		/**
	* @var Mastodon_Post[]
*/
		$new_posts = [];

		Sync_Mastodon::log( 'Fetching Mastodon posts' );

		$fetched_posts = $api->posts_latest();

		Sync_Mastodon::log( 'Fetched ' . count( $fetched_posts ) . ' posts' );

		foreach ( $fetched_posts as $post ) {
			if ( $post->date > $latest_post_date ) {
				// Sync_Mastodon::log( 'Post created at ' . $post->created . ' is newer than latest boopostkmark date ' . $latest_post_date );
				$new_posts[] = $post;
			} else {
				break;
			}
		}

		if ( ! is_array( $new_posts ) ) {
			Sync_Mastodon::error( 'Tried sync, but no new posts were retrieved' );
			return;
		}

		Sync_Mastodon::log( 'Retrieved ' . count( $new_posts ) . ' from Mastodon' );

		// Get the author ID to use.
		$author_id = Sync_Mastodon_Options::get_post_author();

		// Added post count
		$added_post_count = 0;

		// Loop through posts creating posts for them.
		foreach ( $new_posts as $post ) {

			Sync_Mastodon::log( 'Syncing post: ' . $post->title );

			$existing_post = \SyncMastodon\Mastodon_Post::with_id( $post->id );
			if ( $existing_post ) {
				Sync_Mastodon::log( 'Existing post with ID ' . $existing_post->ID . ' found. Skipping.' );
				continue;
			}

			$post_data = [
				'post_type'    => Sync_Mastodon_Options::get_post_type(),
				'post_date'    => date( 'Y-m-d H:i:s', Sync_Mastodon::make_time_local( $post->date ) ),
				'post_title'   => empty( $post->title ) ? date( 'Y-m-d H:i:s', Sync_Mastodon::make_time_local( $post->date ) ) : $post->title,
				'post_content' => $post->content,
				// 'post_status'  => 'yes' === $post->shared ? 'publish' : 'private',
				'post_status'  => 'publish',
				'meta_input'      => [
					'mastodon_permalink' => $post->permalink,
					'mastodon_id'        => $post->id,
					'mastodon_excerpt'   => $post->excerpt,
				],
				'post_author'  => $author_id,
			];

			$result = wp_insert_post( $post_data );

			if ( $result === 0 ) {
				Sync_Mastodon::error( 'Error inserting post' );
				continue;
			}

			$added_post_count++;

			$post->sideload_media( $result );

			// TAXONOMIES?
			// if ( $result > 0 ) {
			// wp_set_post_terms( $result, $post->tags, 'mastodon-tag' );
			// }

		}

		Sync_Mastodon::log( 'Added ' . $added_post_count . ' new posts' );

		// Update last sync time
		$this->last_sync = time();
		update_option( 'sync-mastodon-last-sync', $this->last_sync );

	}

}
