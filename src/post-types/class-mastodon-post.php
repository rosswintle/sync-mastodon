<?php
/**
 * Post type class for Mastodon posts
 */

namespace SyncMastodon;

/**
 * Post type class for Mastodon posts
 */
class Mastodon_Post {

	/**
	 * [Static] Fetch the post with the specified ID
	 *
	 * @param  string $id The ID of the post to fetch
	 * @return \WP_Post|null
	 */
	public static function with_id( $id ) {
		$posts = get_posts(
			[
				'post_type'  => Sync_Mastodon_Options::get_post_type(),
				'meta_query' => [
					[
						'key'   => 'mastodon_id',
						'value' => $id,
					],
				],
			]
		);
		if ( ! empty( $posts ) ) {
			return current( $posts );
		} else {
			return null;
		}
	}

}
