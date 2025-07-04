<?php

namespace Rtcl\Controllers\Admin;

class NoticeController {
	public function __construct() {
		$current      = time();
		$currentYear  = gmdate( 'Y' );
		$black_friday = mktime( 0, 0, 0, 11, 19, $currentYear ) <= $current && $current <= mktime( 0, 0, 0, 1, 5, $currentYear + 1 );

		if ( $black_friday ) {
			add_action( 'admin_init', [ $this, 'black_friday_notice' ] );
		} else {
			register_activation_hook( RTCL_PLUGIN_FILE, [ $this, 'update_activation_time' ] );
			add_action( 'admin_init', [ $this, 'ratingNotice' ] );
			add_action( 'admin_init', [ $this, 'update_rating_status' ], 5 );
		}
		add_action( 'admin_notices', [ __CLASS__, 'eid_special_deal_admin_notice' ] );
		add_action( 'wp_ajax_rtcl_dismiss_eid_notice', [ __CLASS__, 'dismiss_eid_notice' ] );
	}

	public static function eid_special_deal_admin_notice() {
		// Set expiration date (April 7)
		$expiration_date = strtotime( 'April 7, 2025' );

		// Check if the current date is past the expiration date
		if ( time() > $expiration_date ) {
			return;
		}

		// Check if notice is dismissed
		if ( get_user_meta( get_current_user_id(), 'rtcl_dismissed_ramadan_notice', true ) ) {
			return;
		}


		$plugin_name   = 'Classified Listing';
		$download_link = 'https://www.radiustheme.com/downloads/classified-listing-pro-plugins-bundle/';

		?>

		<div class="notice notice-info is-dismissible rtcl-ramadan-notice" data-rtcl-dismissable="rtcl_dismiss_ramadan_notice"
			 style="display:grid !important;grid-template-columns: 100px auto;padding-top: 25px; padding-bottom: 22px;">
			<img alt="<?php echo esc_attr( $plugin_name ); ?>"
				 src="<?php echo esc_url( rtcl()->get_assets_uri( 'images/classified-listing-promo.gif' ) ); ?>"
				 width="74px" height="74px" style="grid-row: 1 / 4; align-self: center;justify-self: center"/>
			<h3 style="margin:0;display: inline-flex;align-items: center;gap: 4px;">
				<?php echo sprintf( ' %s – 🌙 Eid Special Offer', esc_html( $plugin_name ) ); ?>
				<img alt="Deal" style="width: 60px;position: static" src="<?php echo esc_url( rtcl()->get_assets_uri( 'images/deal.gif' ) ); ?>">
			</h3>
			<p style="margin-top: 0; font-size: 14px;">
				<strong>Eid Special:</strong>
				Celebrate Eid with exclusive discounts on
				<b><a href="<?php echo esc_url( $download_link ); ?>" style="text-decoration: none;color: inherit">Classified Listing Bundle</a></b>. Save
				<b style="display:inline-block;color: white;background:red;padding: 0 8px;border-radius:3px; transform: skewX(-10deg);">UP TO 40%</b>
				for a limited time! 🎁🌙✨
			</p>
			<p style="margin:0;">
				<a class="button button-primary" href="<?php echo esc_url( $download_link ); ?>"
				   style="background: #3232FF;"
				   target="_blank">Buy Now</a>
			</p>
		</div>

		<script>
			jQuery(document).on('click', '.rtcl-ramadan-notice .notice-dismiss', function () {
				jQuery.post(ajaxurl, {
					action: 'rtcl_dismiss_eid_notice',
					security: '<?php echo wp_create_nonce( "dismiss_eid_notice" ); ?>',
				})
			})
		</script>
		<?php
	}

	public static function dismiss_eid_notice() {
		check_ajax_referer( 'dismiss_eid_notice', 'security' );
		update_user_meta( get_current_user_id(), 'rtcl_dismissed_ramadan_notice', true );
		wp_die();
	}

	/**
	 * Display Admin Notice, asking for a review
	 **/
	public function display_admin_notice() {
		// wordpress global variable
		global $pagenow;

		$exclude = [
			'themes.php',
			'users.php',
			'tools.php',
			'options-general.php',
			'options-writing.php',
			'options-reading.php',
			'options-discussion.php',
			'options-media.php',
			'options-permalink.php',
			'options-privacy.php',
			'edit-comments.php',
			'upload.php',
			'media-new.php',
			'admin.php',
			'import.php',
			'export.php',
			'site-health.php',
			'export-personal-data.php',
			'erase-personal-data.php'
		];

		if ( ! in_array( $pagenow, $exclude ) ) {
			$args         = [ '_wpnonce' => wp_create_nonce( 'rtcl_notice_nonce' ) ];
			$dont_disturb = add_query_arg( $args + [ 'rtcl_skip' => '1' ], $this->current_admin_url() );
			$remind_me    = add_query_arg( $args + [ 'rtcl_reminder' => '1' ], $this->current_admin_url() );
			$rated        = add_query_arg( $args + [ 'rtcl_rated' => '1' ], $this->current_admin_url() );
			$reviewUrl    = 'https://wordpress.org/support/plugin/classified-listing/reviews/?filter=5#new-post';

			printf( '<div class="notice rtcl-review-notice rtcl-review-notice--extended">
                <div class="rtcl-review-notice_content">
                    <h3>Enjoying Classified Listing?</h3>
                    <p>Thank you for choosing The Classified Listing. If you have found our plugin useful and makes you smile, please consider giving us a 5-star rating on WordPress.org. It will help us to grow.</p>
                    <div class="rtcl-review-notice_actions">
                        <a href="%s" class="rtcl-review-button rtcl-review-button--cta" target="_blank"><span>⭐ Yes, You Deserve It!</span></a>
                        <a href="%s" class="rtcl-review-button rtcl-review-button--cta rtcl-review-button--outline"><span>😀 Already Rated!</span></a>
                        <a href="%s" class="rtcl-review-button rtcl-review-button--cta rtcl-review-button--outline"><span>🔔 Remind Me Later</span></a>
                        <a href="%s" class="rtcl-review-button rtcl-review-button--cta rtcl-review-button--error rtcl-review-button--outline"><span>😐 No Thanks</span></a>
                    </div>
                </div>
            </div>', esc_url( $reviewUrl ), esc_url( $rated ), esc_url( $remind_me ), esc_url( $dont_disturb ) );

			echo '<style> 
            .rtcl-review-button--cta {
                --e-button-context-color: #4C6FFF;
                --e-button-context-color-dark: #4C6FFF;
                --e-button-context-tint: rgb(75 47 157/4%);
                --e-focus-color: rgb(75 47 157/40%);
            } 
            .rtcl-review-notice {
                position: relative;
                margin: 5px 20px 5px 2px;
                border: 1px solid #ccd0d4;
                background: #fff;
                box-shadow: 0 1px 4px rgba(0,0,0,0.15);
                font-family: Roboto, Arial, Helvetica, Verdana, sans-serif;
                border-inline-start-width: 4px;
            }
            .rtcl-review-notice.notice {
                padding: 0;
            }
            .rtcl-review-notice:before {
                position: absolute;
                top: -1px;
                bottom: -1px;
                left: -4px;
                display: block;
                width: 4px;
                background: -webkit-linear-gradient(bottom, #4C6FFF 0%, #6939c6 100%);
                background: linear-gradient(0deg, #4C6FFF 0%, #6939c6 100%);
                content: "";
            } 
            .rtcl-review-notice_content {
                padding: 20px;
            } 
            .rtcl-review-notice_actions > * + * {
                margin-inline-start: 8px;
                -webkit-margin-start: 8px;
                -moz-margin-start: 8px;
            } 
            .rtcl-review-notice p {
                margin: 0;
                padding: 0;
                line-height: 1.5;
            }
            p + .rtcl-review-notice_actions {
                margin-top: 1rem;
            }
            .rtcl-review-notice h3 {
                margin: 0;
                font-size: 1.0625rem;
                line-height: 1.2;
            }
            .rtcl-review-notice h3 + p {
                margin-top: 8px;
            } 
            .rtcl-review-button {
                display: inline-block;
                padding: 0.4375rem 0.75rem;
                border: 0;
                border-radius: 3px;;
                background: var(--e-button-context-color);
                color: #fff;
                vertical-align: middle;
                text-align: center;
                text-decoration: none;
                white-space: nowrap; 
            }
            .rtcl-review-button:active {
                background: var(--e-button-context-color-dark);
                color: #fff;
                text-decoration: none;
            }
            .rtcl-review-button:focus {
                outline: 0;
                background: var(--e-button-context-color-dark);
                box-shadow: 0 0 0 2px var(--e-focus-color);
                color: #fff;
                text-decoration: none;
            }
            .rtcl-review-button:hover {
                background: var(--e-button-context-color-dark);
                color: #fff;
                text-decoration: none;
            } 
            .rtcl-review-button.focus {
                outline: 0;
                box-shadow: 0 0 0 2px var(--e-focus-color);
            } 
            .rtcl-review-button--error {
                --e-button-context-color: #d72b3f;
                --e-button-context-color-dark: #ae2131;
                --e-button-context-tint: rgba(215,43,63,0.04);
                --e-focus-color: rgba(215,43,63,0.4);
            }
            .rtcl-review-button.rtcl-review-button--outline {
                border: 1px solid;
                background: 0 0;
                color: var(--e-button-context-color);
            }
            .rtcl-review-button.rtcl-review-button--outline:focus {
                background: var(--e-button-context-tint);
                color: var(--e-button-context-color-dark);
            }
            .rtcl-review-button.rtcl-review-button--outline:hover {
                background: var(--e-button-context-tint);
                color: var(--e-button-context-color-dark);
            } 
            </style>';
		}
	}

	protected function current_admin_url() {
		$uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$uri = preg_replace( '|^.*/wp-admin/|i', '', $uri );

		if ( ! $uri ) {
			return '';
		}

		return remove_query_arg( [
			'_wpnonce',
			'rtcl_rating_status_clear',
			'rtcl_reminder',
			'rtcl_skip',
			'rtcl_rated'
		], admin_url( $uri ) );
	}

	// remove the notice for the user if review already done or if the user does not want to
	public function update_rating_status() {
		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'rtcl_notice_nonce' ) ) {
			return;
		}

		if ( ! empty( $_GET['rtcl_skip'] ) && $_GET['rtcl_skip'] == 1 ) {
			update_option( 'rtcl_rating_status', "skip" );
		}

		if ( ! empty( $_GET['rtcl_reminder'] ) && $_GET['rtcl_reminder'] == 1 ) {
			update_option( 'rtcl_rating_status', strtotime( "now" ) );
		}

		if ( ! empty( $_GET['rtcl_rated'] ) && $_GET['rtcl_rated'] == 1 ) {
			update_option( 'rtcl_rating_status', 'rated' );
		}

		if ( ! empty( $_GET['rtcl_rating_status_clear'] ) && $_GET['rtcl_rating_status_clear'] == 1 ) {
			delete_option( 'rtcl_rating_status' );
		}
	}

	//check if review notice should be shown or not
	public function ratingNotice() {
		$ratingStatus = get_option( 'rtcl_rating_status' );

		if ( "rated" === $ratingStatus || "skip" === $ratingStatus ) {
			return;
		}

		$install_date = get_option( 'rtcl_activation_time' );
		$past_date    = strtotime( '-10 days' );
		if ( ! is_numeric( $ratingStatus ) || ( (int) $ratingStatus != $ratingStatus ) ) {
			$ratingStatus = false;
		}

		$remind_due = strtotime( '+15 days', $ratingStatus );
		$now        = strtotime( "now" );

		if ( $now >= $remind_due || ( ( $past_date >= $install_date ) && empty( $ratingStatus ) ) ) {
			add_action( 'admin_notices', [ $this, 'display_admin_notice' ] );
		}
	}


	// add plugin activation time
	public function update_activation_time() {
		$get_activation_time = strtotime( "now" );
		add_option( 'rtcl_activation_time', $get_activation_time );
	}

	public function black_friday_notice() {
		delete_option( 'rtcl_dismiss_admin_notice' );
		$currentYear = date( 'Y' );
		if ( get_option( 'rtcl_dismiss_admin_notice_' . $currentYear ) != '1' && ! isset( $GLOBALS['rtcl_dismiss_admin_notice_notice'] ) ) {
			$GLOBALS['rtcl_dismiss_admin_notice_notice'] = 'rtcl_dismiss_admin_notice';
			$this->bfNoticeActions();
		}
	}


	/**
	 * Undocumented function.
	 *
	 * @return void
	 */
	public function bfNoticeActions() {
		add_action( 'admin_enqueue_scripts', function () {
			wp_enqueue_script( 'jquery' );
		} );
		add_action(
			'admin_notices',
			function () {
				$currentYear   = date( 'Y' );
				$plugin_name   = 'Classified Listing';
				$download_link = 'https://www.radiustheme.com/downloads/classified-listing-pro-plugins-bundle/'; ?>
				<div class="notice notice-info is-dismissible" data-rtcl-bf-dismiss-able="rtcl_dismiss_admin_notice"
					 style="display:grid;grid-template-columns: 100px auto;column-gap:10px;padding-top: 15px; padding-bottom: 12px;">
					<img alt="<?php echo esc_attr( $plugin_name ); ?>"
						 src="<?php echo esc_url( rtcl()->get_assets_uri( 'images/classified-listing-promo.gif' ) ) ?>"
						 width="90px"
						 height="90px" style="grid-row: 1 / 4; align-self: center;justify-self: center"/>
					<h3 style="margin:0;display: flex;align-items: center"><?php echo sprintf( '%s - End of Year 2024 <img style="width: 45px;position: relative;margin-left: 6px" src="%s" />',
							esc_html( $plugin_name ), rtcl()->get_assets_uri( 'images/deal.gif' ) ); ?></h3>

					<p style="margin:3px 0 5px; font-size: 14px">
						Year-end sale is live now! Get the <strong>plugin bundle</strong> or
						<strong>individual addon</strong> and enjoy discounts <span style="color: #fe0100; font-weight: 600">up to 50%</span>. Limited time
						offer!!
					</p>

					<p style="margin:0;">
						<a class="button button-primary" href="<?php echo esc_url( $download_link ); ?>"
						   target="_blank">Buy Now</a>
						<a class="button button-dismiss" href="#">Dismiss</a>
					</p>
				</div>
				<?php
			}
		);

		add_action(
			'admin_footer',
			function () {
				?>
				<script type="text/javascript">
					(function ($) {
						$(function () {
							setTimeout(function () {
								$('div[data-rtcl-bf-dismiss-able] .notice-dismiss, div[data-rtcl-bf-dismiss-able] .button-dismiss')
									.on('click', function (e) {
										e.preventDefault();
										$.post(ajaxurl, {
											'action': 'rtcl_bf_dismiss_admin_notice',
											'nonce': <?php echo wp_json_encode( wp_create_nonce( 'rtcl-bf-dismissible-notice' ) ); ?>
										});
										$(e.target).closest('.is-dismissible').remove();
									});
							}, 1000);
						});
					})(jQuery);
				</script>
				<?php
			}
		);

		add_action(
			'wp_ajax_rtcl_bf_dismiss_admin_notice',
			function () {
				$currentYear = date( 'Y' );
				check_ajax_referer( 'rtcl-bf-dismissible-notice', 'nonce' );

				update_option( 'rtcl_dismiss_admin_notice_' . $currentYear, '1' );
				wp_die();
			}
		);
	}

}