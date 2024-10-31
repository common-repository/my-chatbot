<?php
/**
 * Weclome Page Class
*
* @package     MYC
* @subpackage  Admin/Welcome
* @copyright   Copyright (c) 2017, Daniel Powney
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       0.1
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * MYC_Welcome Class
 *
 * A general class for About and Credits page.
 *
 * @since 0.1
 */
class MYC_Welcome {

	/**
	 * @var string The capability users should have to view the page
	 */
	public $minimum_capability = 'manage_options';

	/**
	 * Get things started
	 *
	 * @since 0.1
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus') );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'welcome'    ), 11 );
	}

	/**
	 * Register the Dashboard Pages which are later hidden but these pages
	 * are used to render the Welcome and Credits pages.
	 *
	 * @access public
	 * @since 0.1
	 * @return void
	 */
	public function admin_menus() {

		// Changelog Page
		add_dashboard_page(
				__( 'My Chatbot Changelog', 'my-chatbot' ),
				__( 'My Chatbot Changelog', 'my-chatbot' ),
				$this->minimum_capability,
				'myc-changelog',
				array( $this, 'changelog_screen' )
		);

		// Getting Started Page
		add_dashboard_page(
				__( 'Getting started with My Chatbot', 'my-chatbot' ),
				__( 'Getting started with My Chatbot', 'my-chatbot' ),
				$this->minimum_capability,
				'myc-getting-started',
				array( $this, 'getting_started_screen' )
		);


		// Now remove them from the menus so plugins that allow customizing the admin menu don't show them
		remove_submenu_page( 'index.php', 'myc-changelog' );
		remove_submenu_page( 'index.php', 'myc-getting-started' );
	}

	/**
	 * Hide Individual Dashboard Pages
	 *
	 * @access public
	 * @since 0.1
	 * @return void
	 */
	public function admin_head() {
		?>
		<style type="text/css" media="screen">
			/*<![CDATA[*/
			.myc-about-wrap .myc-badge { float: right; border-radius: 4px; margin: 0 0 15px 15px; max-width: 100px; }
			.myc-about-wrap #myc-header { margin-bottom: 15px; }
			.myc-about-wrap #myc-header h1 { margin-bottom: 15px !important; }
			.myc-about-wrap .about-text { margin: 0 0 15px; max-width: 670px; }
			.myc-about-wrap .feature-section { margin-top: 20px; }
			.myc-about-wrap .feature-section-content,
			.myc-about-wrap .feature-section-media { width: 50%; box-sizing: border-box; }
			.myc-about-wrap .feature-section-content { float: left; padding-right: 50px; }
			.myc-about-wrap .feature-section-content h4 { margin: 0 0 1em; }
			.myc-about-wrap .feature-section-media { float: right; text-align: right; margin-bottom: 20px; }
			.myc-about-wrap .feature-section-media img { border: 1px solid #ddd; }
			.myc-about-wrap .feature-section:not(.under-the-hood) .col { margin-top: 0; }
			.myc-about-wrap .three-col .col { width: 29.75%;  display: inline-block; vertical-align: top; margin-top: 2em; margin-top: 2em; padding-right: 1em; text-align: center;}
			/* responsive */
			@media all and ( max-width: 782px ) {
				.myc-about-wrap .feature-section-content,
				.myc-about-wrap .feature-section-media { float: none; padding-right: 0; width: 100%; text-align: left; }
				.myc-about-wrap .feature-section-media img { float: none; margin: 0 0 20px; }
			}
			/*]]>*/
		</style>
		<?php
	}

	/**
	 * Welcome message
	 *
	 * @access public
	 * @since 0.1
	 * @return void
	 */
	public function welcome_message() {
		list( $display_version ) = explode( '-', MYC_VERSION );
		?>
		<div id="myc-header">

			<h1><?php printf( __( 'Welcome to My Chatbot v%s', 'my-chatbot' ), $display_version ); ?></h1>
			<p class="about-text">
				<?php _e( 'Chatbot for WordPress powered by Dialogflow.', 'my-chatbot' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Navigation tabs
	 *
	 * @access public
	 * @since 0.1
	 * @return void
	 */
	public function tabs() {
		$selected = isset( $_GET['page'] ) ? $_GET['page'] : 'myc-about';
		?>
		<h1 class="nav-tab-wrapper">
			<a class="nav-tab <?php echo $selected == 'myc-getting-started' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'myc-getting-started' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Getting Started', 'my-chatbot' ); ?>
			</a>
			<a class="nav-tab <?php echo $selected == 'myc-changelog' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'myc-changelog' ), 'index.php' ) ) ); ?>">
				<?php _e( "Changelog", 'my-chatbot' ); ?>
			</a>
		</h1>
		<?php
	}

	/**
	 * Render Changelog Screen
	 *
	 * @access public
	 * @since 0.1
	 * @return void
	 */
	public function changelog_screen() {
		?>
		<div class="wrap about-wrap myc-about-wrap">
			<?php
				// load welcome message and content tabs
				$this->welcome_message();
				$this->tabs();
			?>
			<div class="changelog">
				<div class="feature-section">
					<?php echo $this->parse_readme(); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Getting Started Screen
	 *
	 * @access public
	 * @since 0.1
	 * @return void
	 */
	public function getting_started_screen() {
		?>
		<div class="wrap about-wrap myc-about-wrap">
			<?php
				// load welcome message and content tabs
				$this->welcome_message();
				$this->tabs();
			?>

			<div class="changelog">
				<div class="feature-section">
					
					<div class="feature-section-media">
						<img src="<?php echo MYC_PLUGIN_URL . 'assets/images/screenshots/screenshot-1.PNG'; ?>" class="myc-welcome-screenshots"/>
					</div>
					
					<div class="feature-section-content">
						<h3><?php _e( '#1 Chatbot for WordPress', 'my-chatbot' ); ?></h3>
						<p><?php _e( 'Create your own branded chatbot for your WordPress website, powered by Google Dialogflow.', 'my-chatbot' ); ?></p>
						<p><?php _e( 'To get started, you will need to create a Dialogflow agent and configure the plugin to use a Google service account key file for integration. ', 'my-chatbot' ); ?></p>						
						<p><?php _e( '<a href="https://mychatbot.xyz">Visit our website</a> for documentation on how to get started.', 'my-chatbot' );?></p>

					</div>
				
					<div class="return-to-dashboard">
						<p><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'my-chatbot' ), 'options-general.php' ) ) ); ?>"><?php _e( 'Go to Options &rarr;', 'my-chatbot' ); ?></a></p>
					</div>



					<div style="clear: both;"></div>

					<h3 style="text-align: center"><?php _e( 'Key Features', 'my-chatbot' ); ?></h3>
					
					<div class="under-the-hood col three-col">

						<div class="col">
							<h4><?php _e( 'Conversation History', 'my-chatbot' ); ?></h4>
							<p><?php _e( 'Save to local storage. Persist across pages. Allow visitors to clear history.', 'my-chatbot' ); ?></p>

							<h4><?php _e( 'Rich Message Responses', 'my-chatbot' ); ?></h4>
							<p><?php _e( 'Quick Replies, Cards and Images. Add oEmbed codes (e.g. Youtube video) and hyperlinks in text responses.', 'my-chatbot' ); ?></p>

							<h4><?php _e( 'Chatbot Overlay', 'my-chatbot' ); ?></h4>
							<p><?php _e( 'Add a chatbot overlay in the bottom right corner which toggles up or down.', 'my-chatbot' ); ?></p>

							<h4><?php _e( 'Welcome Intent', 'my-chatbot' ); ?></h4>
							<p><?php _e( 'Greet your users with a welcome message when they open the chatbot.', 'my-chatbot' ); ?></p>

							<h4><?php _e( 'Custom Style Options', 'my-chatbot' ); ?></h4>
							<p><?php _e( 'Settings with color pickers for backgrounds and fonts and opacity for old conversation bubbles.', 'my-chatbot' ); ?></p>
							
						</div>

						<div class="col">

							<h4><?php _e( 'Response Message Delays', 'my-chatbot' ); ?></h4>
							<p><?php _e( 'Optionally add a delay before displaying response messages.', 'my-chatbot' ); ?></p>

							<h4><?php _e( 'Custom Branding', 'my-chatbot' ); ?></h4>
							<p><?php _e( 'Custom text for heading, powered by and input placeholder.', 'my-chatbot' ); ?></p>

							<h4><?php _e( 'Mobile Support', 'my-chatbot' ); ?></h4>
							<p><?php _e( 'Displays fine on mobile devices. Optionally disable chatbot overlay on mobile devices.', 'my-chatbot' ); ?></p>

							<h4><?php _e( 'Display Control', 'my-chatbot' ); ?></h4>
							<p><?php _e( 'Choose which page types (e.g. home, single, search etc...) post types (e.g. post & page) or specific posts where the overlay can be shown.', 'my-chatbot' ); ?></p>

							<h4><?php _e( 'Message Alignment', 'my-chatbot' ); ?></h4>
							<p><?php _e( 'Options to change request and response bubbles to be aligned either left or right.', 'my-chatbot' ); ?></p>
							
						</div>

						<div class="col last-feature">

							<h4><?php _e( 'Text Size', 'my-chatbot' ); ?></h4>
							<p><?php _e( 'Visitors can click an icon to increase or decrease the chatbot text size.', 'my-chatbot' ); ?></p>

							<h4><?php _e( 'Overlay State', 'my-chatbot' ); ?></h4>
							<p><?php _e( 'Default open option. Remember open state when navigating website.', 'my-chatbot' ); ?></p>

							<h4><?php _e( 'Shortcode & Widget', 'my-chatbot' ); ?></h4>
							<p><?php _e( 'Add chatbot in your post content using the [my_chatbot] shortcode. Add a chatbot widget to your page layouts.' ); ?></p>

							<h4><?php _e( 'Secure', 'my-chatbot' ); ?></h4>
							<p><?php _e( 'No credentials exposed in the browser. Google API authentication is server-side using OAuth 2.', 'my-chatbot' ); ?></p>

							<h4><?php _e( 'Well Built', 'my-chatbot' ); ?></h4>
							<p><?php _e( 'Developer friendly. In-built template system. 60+ stargazers on Github. REST API. Lightweight.', 'my-chatbot' ); ?></p>
						</div>
					</div>

				</div>
			</div>

			<div style="clear: both;"></div>

		</div>
		<?php
	}

	/**
	 * Parse the MYC readme.txt file
	 *
	 * @since 0.1
	 * @return string $readme HTML formatted readme file
	 */
	public function parse_readme() {
		$file = file_exists( MYC_PLUGIN_DIR . 'readme.txt' ) ? MYC_PLUGIN_DIR . 'readme.txt' : null;

		if ( ! $file ) {
			$readme = '<p>' . __( 'No valid changelog was found.', 'my-chatbot' ) . '</p>';
		} else {
			$readme = file_get_contents( $file );
			$readme = nl2br( esc_html( $readme ) );
			$readme = explode( '== Changelog ==', $readme );
			$readme = end( $readme );

			$readme = preg_replace( '/`(.*?)`/', '<code>\\1</code>', $readme );
			$readme = preg_replace( '/[\040]\*\*(.*?)\*\*/', ' <strong>\\1</strong>', $readme );
			$readme = preg_replace( '/[\040]\*(.*?)\*/', ' <em>\\1</em>', $readme );
			$readme = preg_replace( '/= (.*?) =/', '<h4>\\1</h4>', $readme );
			$readme = preg_replace( '/\[(.*?)\]\((.*?)\)/', '<a href="\\2">\\1</a>', $readme );
		}

		return $readme;
	}


	/**
	 * Render Contributors List
	 *
	 * @since 0.1
	 * @uses MYC_Welcome::get_contributors()
	 * @return string $contributor_list HTML formatted list of all the contributors for MYC
	 */
	public function contributors() {
		$contributors = $this->get_contributors();

		if ( empty( $contributors ) )
			return '';

		$contributor_list = '<ul class="wp-people-group">';

		foreach ( $contributors as $contributor ) {
			$contributor_list .= '<li class="wp-person">';
			$contributor_list .= sprintf( '<a href="%s">',
				esc_url( 'https://github.com/' . $contributor->login ),
				esc_html( sprintf( __( 'View %s', 'my-chatbot' ), $contributor->login ) )
			);
			$contributor_list .= sprintf( '<img src="%s" width="64" height="64" class="gravatar" alt="%s" />', esc_url( $contributor->avatar_url ), esc_html( $contributor->login ) );
			$contributor_list .= '</a>';
			$contributor_list .= sprintf( '<a class="web" href="%s">%s</a>', esc_url( 'https://github.com/' . $contributor->login ), esc_html( $contributor->login ) );
			$contributor_list .= '</a>';
			$contributor_list .= '</li>';
		}

		$contributor_list .= '</ul>';

		return $contributor_list;
	}

	/**
	 * Retreive list of contributors from GitHub.
	 *
	 * @access public
	 * @since 0.1
	 * @return array $contributors List of contributors
	 */
	public function get_contributors() {
		$contributors = get_transient( 'myc_contributors' );

		if ( false !== $contributors )
			return $contributors;

		$response = wp_remote_get( 'https://api.github.com/repos/danielpowney/my-chatbot/contributors?per_page=999', array( 'sslverify' => false ) );

		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) )
			return array();

		$contributors = json_decode( wp_remote_retrieve_body( $response ) );

		if ( ! is_array( $contributors ) )
			return array();

		set_transient( 'myc_contributors', $contributors, 3600 );

		return $contributors;
	}

	/**
	 * Sends user to the Welcome page on first activation of MYC as well as each
	 * time MYC is upgraded to a new version
	 *
	 * @access public
	 * @since 1.4
	 * @return void
	 */
	public function welcome() {
		// Bail if no activation redirect
		if ( ! get_transient( '_myc_activation_redirect' ) )
			return;

		// Delete the redirect transient
		delete_transient( '_myc_activation_redirect' );

		// Bail if activating from network, or bulk
		// if ( is_network_admin() || isset( $_GET['activate-multi'] ) )
		//	return;

		//$upgrade = get_option( 'myc_version_upgraded_from' );

		//if( ! $upgrade ) { // First time install
			wp_safe_redirect( admin_url( 'index.php?page=myc-getting-started' ) ); exit;
		//} else { // Update
		//	wp_safe_redirect( admin_url( 'index.php?page=myc-about' ) ); exit;
		//}
	}
}
new MYC_Welcome();
