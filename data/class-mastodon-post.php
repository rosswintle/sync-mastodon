<?php

namespace SyncMastodon\Data;

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
		return $post;
	}
}
