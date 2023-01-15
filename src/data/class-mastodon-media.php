<?php
/**
 * A data class for Mastodon media items
 */

namespace SyncMastodon\Data;

use SyncMastodon\Sync_Mastodon;

/**
 * Data object for Mastodon media items.
 */
class Mastodon_Media {

	/**
	 * The URL of the media item
	 *
	 * @var string
	 */
	public $url = '';

	/**
	 * The title of the media item
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * The descrition (alt text) of the media item
	 *
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
		$media->title       = $data['title'];
		$media->description = $data['description'];

		return $media;
	}

	/**
	 * Undocumented function
	 *
	 * @param  int $wp_post_id The WordPress post ID to attach to.
	 * @return int    The WordPress media ID
	 */
	public function sideload( $wp_post_id ) {
		Sync_Mastodon::log( 'Sideloading media item ' . $this->url . ' to post ID ' . $wp_post_id . '...' );

		$this->wordpress_id = media_sideload_image( $this->url, $wp_post_id, $this->title, 'id' );

		if ( is_wp_error( $this->wordpress_id ) ) {
			Sync_Mastodon::log( 'Error sideloading media item ' . $this->url . ' to post ID ' . $wp_post_id . ': ' . $this->wordpress_id->get_error_message() );
			return 0;
		}

		// Set the alt text
		update_post_meta( $this->wordpress_id, '_wp_attachment_image_alt', $this->description );

		return $this->wordpress_id;
	}

}
