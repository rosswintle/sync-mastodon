<?php

namespace SyncMastodon;

class Mastodon_Post {

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
		if ( is_array( $posts ) ) {
			return current( $posts );
		} else {
			return null;
		}
	}

}
