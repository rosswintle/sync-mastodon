<?php

namespace SyncMastodon\Data;

use SyncMastodon\Data\Mastodon_Media;
use SyncMastodon\Sync_Mastodon;

/**
 * Data object for Mastodon posts.
 */
class Mastodon_Post {

	/**
	 * @var string
	 */
	public $id = '';

	/**
	 * @var string
	 */
	public $permalink = '';

	/**
	 * The link title
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * The short description/content of the post.
	 *
	 * @var string
	 */
	public $excerpt = '';

	/**
	 * The longer description/content of the post.
	 *
	 * @var string
	 */
	public $content = '';

	/**
	 * This will be a Unix timestamp
	 *
	 * @var int
	 */
	public $date = 0;

	/**
	 * This is an array of URLs to media files
	 *
	 * @var Media_Item[]
	 */
	public $media = [];

	/**
	 * Array of WordPress media IDs - this will be set by the sideload operation
	 *
	 * This can be null (not set) or an empty array (no media for the post)
	 *
	 * @var int[]|null
	 */
	public $media_ids = null;

	/**
	 * Create a new instance from a SimplePie RSS item object.
	 *
	 * @param  StdClass $data The data to create from.
	 * @return self
	 */
	public static function from_rss_object( $data ) {
		$post            = new self();
		$post->id        = $data->get_id();
		$post->permalink = $data->get_permalink();
		$post->title     = $data->get_title();
		$post->excerpt   = $data->get_description();
		$post->content   = $data->get_content();
		$post->date      = $data->get_gmdate( 'U' );

		$media = $data->get_enclosures();

		if ( ! is_array( $media ) || empty( $media ) ) {
			return $post;
		}

		foreach ( $media as $media_item ) {
			if ( $media_item->get_type() !== null ) {
				$post->media[] = Mastodon_Media::from_array( [
					'url'         => $media_item->get_link(),
					'description' => $media_item->get_description(),
				] );
			}
		}

		return $post;
	}

	/**
	 * This will sideload the media files and attach them to the specifed WordPress post.
	 *
	 * You'll need to do this once a WP post has been created.
	 *
	 * @param  int $wp_post_id The WordPress post ID to attach items to.
	 * @return void
	 */
	public function sideload_media( $wp_post_id ) {
		Sync_Mastodon::log('Loading media for post ID ' . $wp_post_id . '...');

		if ( empty( $this->media ) ) {
			Sync_Mastodon::log('No media to load.');
			return;
		}

		$this->media_ids = [];

		foreach ( $this->media as $media_item ) {
			$this->media_ids[] = $media_item->sideload( $wp_post_id );
		}

		if ( ! empty( $this->media_ids ) ) {
			Sync_Mastodon::log('Setting post thumbnail for post ID ' . $wp_post_id . ' to media ID ' . $this->media_ids[0] . '...');
			set_post_thumbnail($wp_post_id, $this->media_ids[0]);
		}
	}
}
