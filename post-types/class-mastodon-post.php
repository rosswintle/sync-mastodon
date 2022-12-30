<?php

namespace SyncMastodon;

class Mastodon_Post {

	public static function with_id( $id ) {
		$posts = get_posts([
			'post_type'  => 'mastodon-post',
			'meta_query' => [
				[
					'key'   => 'mastodon_id',
					'value' => $id,
				],
			],
		]);
		if (is_array($posts)) {
			return current($posts);
		} else {
			return null;
		}
	}

}
