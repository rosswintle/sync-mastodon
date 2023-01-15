<?php

namespace SyncMastodon\Data;

use SyncMastodon\Sync_Mastodon;

/**
 * Data object for Mastodon media items.
 */
class Mastodon_Media {

	/**
	 * @var string
	 */
	public $url = '';

	/**
	 * @var string
	 */
	public $description = '';

	/**
	 * This is set by the sideload operation
	 *
	 * @var int
	 */
	public $wordpress_id = null;

	/**
	 * Create a new instance from a SimplePie RSS item object.
	 *
	 * @param  array $data The data to create from.
	 * @return self
	 */
	public static function from_array( $data ) {
		$media = new self();
		$media->url         = $data['url'];
		$media->description = $data['description'];

		return $media;
	}

	/**
	 * Undocumented function
	 *
	 * @param  int    $wp_post_id  The WordPress post ID to attach to.
	 * @return int    The WordPress media ID
	 */
	public function sideload( $wp_post_id ) {
		Sync_Mastodon::log('Sideloading media item ' . $this->url . ' to post ID ' . $wp_post_id . '...');

		$this->wordpress_id = media_sideload_image( $this->url, $wp_post_id, $this->description, 'id' );
		return $this->wordpress_id;
	}

}
