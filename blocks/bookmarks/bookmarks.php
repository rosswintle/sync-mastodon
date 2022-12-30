<?php

namespace SyncMastodon\Blocks;

class Bookmarks
{
	public function __construct() {

    	wp_register_script(
        	'sync-mastodon-posts-block',
        	plugins_url( 'block.js', __FILE__ ),
        	array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components' )
    	);

    	register_block_type( 'sync-mastodon/bookmarks', array(
        	'editor_script' => 'sync-mastodon-posts-block',
    	) );

	}

}
