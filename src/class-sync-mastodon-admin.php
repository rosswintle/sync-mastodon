<?php
/**
 * The wp-admin side of the settings/options
 */

namespace SyncMastodon;

use SyncMastodon\Sync_Mastodon_Options;
use SyncMastodon\Sync_Mastodon_Cron;

class Sync_Mastodon_Admin {

	/**
	 * Constructor - setup all the things!
	 */
	public function __construct() {
		add_submenu_page( 'options-general.php', 'Sync Mastodon Settings', 'Sync Mastodon', 'manage_options', 'sync-mastodon', [ $this, 'page' ] );
	}

	/**
	 * Echo the page (and handle form submit)
	 *
	 * @return void
	 */
	public function page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Unauthorized user' );
		}

		$this->handle_submission();

		$rss_url         = Sync_Mastodon_Options::get_rss_url();
		$post_author      = Sync_Mastodon_Options::get_post_author();
		$post_type      = Sync_Mastodon_Options::get_post_type();
		$post_sync_status = Sync_Mastodon_Options::get_post_sync_status();

		?>
			<h1>Sync Mastodon Settings</h1>

			<hr>

			<p><strong>Please note:</strong> This is not an official Mastodon plugin. If you have any problems please direct them to the WordPress support forums for this plugin.</p>

			<hr>

			<form method="POST">
				<?php wp_nonce_field( 'sync-mastodon-settings' ); ?>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="rss-url">Mastodon RSS URL</label>
							</th>
							<td>
								<input type="text" class="regular-text" name="rss-url" id="rss-url" value="<?php echo esc_attr($rss_url ? esc_attr( $rss_url ) : ''); ?>">
								<p class="description" id="tagline-description">You can get this from your <a href="https://app.mastodon.io/settings/integrations">Mastodon user URL by adding ".rss"</a></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="post-author">Sync'ed post author</label>
							</th>
							<td>
								<select name="post-author" id="post-author">
									<?php foreach (get_users() as $user) : ?>
										<option value="<?php echo esc_attr($user->ID); ?>" <?php selected($post_author, $user->ID); ?>>
											<?php echo esc_html($user->display_name); ?>
										</option>
									<?php endforeach; ?>
								</select>
								<p class="description" id="tagline-description">All new posts synced will be assigned to this author.</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="post-type">Sync'ed post post type</label>
							</th>
							<td>
								<select name="post-type" id="post-type">
									<?php foreach (get_post_types() as $this_type) : ?>
										<option value="<?php echo esc_attr($this_type); ?>" <?php selected($post_type, $this_type); ?>>
											<?php echo esc_html($this_type); ?>
										</option>
									<?php endforeach; ?>
								</select>
								<p class="description" id="tagline-description">All new posts synced will be assigned to this post type.</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								Auto-sync
							</th>
							<td>
								<span>
									<input type="radio" name="post-sync-status" id="sync-off" value="0" <?php checked($post_sync_status, 0); ?>>
									<label for="sync-off">Off</label>
								</span>
								<span>
									<input type="radio" name="post-sync-status" id="sync-on" value="1" <?php checked($post_sync_status, 1); ?>>
									<label for="sync-on">On</label>
								</span>
								<p class="description" id="tagline-description">
									Turn this on to allow automatic syncing using WordPress's built-in scheduler (WP-Cron).
								</p>
								<?php if (1 == $post_sync_status) : ?>
									<p class="description" id="tagline-description">
										Next sync: <?php echo (new Sync_Mastodon_Cron())->next_sync_time(); ?>
									</p>
								<?php endif; ?>
							</td>
						</tr>
					</tbody>
				</table>
				<p class="submit">
					<input type="submit" class="button button-primary" name="submit" value="Update options">
				</p>
			</form>
		<?php
	}

	/**
	 * Handle a post submission to the page
	 *
	 * @return void
	 */
	public function handle_submission() {
		if ( ! isset( $_POST['_wpnonce'] ) ) {
			return;
		}

		check_admin_referer( 'sync-mastodon-settings' );

		// To validate the API token is just a URL
		$valid_url_regex = '/^https?:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?$/';

		if (
			isset( $_POST['rss-url'] )
			&& 1 === preg_match($valid_url_regex, $_POST['rss-url'])
		) {
			Sync_Mastodon_Options::set_rss_url( $_POST['rss-url'] );
		}

		if (
			isset( $_POST['post-author'] )
			&& is_numeric( $_POST['post-author'] )
			&& is_a( get_user_by( 'ID', $_POST['post-author'] ), 'WP_User' )
		) {
			Sync_Mastodon_Options::set_post_author( $_POST['post-author'] );
		}

		if (
			isset( $_POST['post-type'] )
			&& in_array( $_POST['post-type'], get_post_types() )
		) {
			Sync_Mastodon_Options::set_post_type( $_POST['post-type'] );
		}

		if (
			isset( $_POST['post-sync-status'] )
			&& is_numeric( $_POST['post-sync-status'] )
			&& ( 1 === (int)$_POST['post-sync-status'] || 0 === (int)$_POST['post-sync-status'] )
		) {
			Sync_Mastodon_Options::set_post_sync_status( $_POST['post-sync-status'] );
		}
	}

}
