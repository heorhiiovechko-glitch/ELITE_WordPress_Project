<?php
/**
 * Login / Register form — single card with toggle.
 *
 * @package Elite_Shipping
 * @see     woocommerce/templates/myaccount/form-login.php
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_customer_login_form' );

$btn_class = wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '';
$show_register = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
// phpcs:ignore WordPress.Security.NonceVerification.Missing
$active_panel  = ( $show_register && isset( $_POST['register'] ) ) ? 'register' : 'login';
?>

<div class="apex-account-auth apex-account-auth--toggle<?php echo $show_register ? '' : ' apex-account-auth--single'; ?>" id="customer_login" data-active="<?php echo esc_attr( $active_panel ); ?>">
	<div class="apex-account-auth-card">
		<?php if ( $show_register ) : ?>
			<div class="apex-account-auth-toggle" role="tablist" aria-label="<?php esc_attr_e( 'Account access', 'elite-shipping' ); ?>">
				<button
					type="button"
					class="apex-account-auth-toggle-btn<?php echo 'login' === $active_panel ? ' is-active' : ''; ?>"
					role="tab"
					id="apex-auth-tab-login"
					aria-controls="apex-auth-panel-login"
					aria-selected="<?php echo 'login' === $active_panel ? 'true' : 'false'; ?>"
					data-auth-panel="login"
				>
					<?php esc_html_e( 'Login', 'woocommerce' ); ?>
				</button>
				<button
					type="button"
					class="apex-account-auth-toggle-btn<?php echo 'register' === $active_panel ? ' is-active' : ''; ?>"
					role="tab"
					id="apex-auth-tab-register"
					aria-controls="apex-auth-panel-register"
					aria-selected="<?php echo 'register' === $active_panel ? 'true' : 'false'; ?>"
					data-auth-panel="register"
				>
					<?php esc_html_e( 'Register', 'woocommerce' ); ?>
				</button>
			</div>
		<?php else : ?>
			<h2><?php esc_html_e( 'Login', 'woocommerce' ); ?></h2>
		<?php endif; ?>

		<div
			class="apex-account-auth-panel<?php echo 'login' === $active_panel ? ' is-active' : ''; ?>"
			id="apex-auth-panel-login"
			role="tabpanel"
			<?php echo $show_register ? 'aria-labelledby="apex-auth-tab-login"' : ''; ?>
			data-auth-panel="login"
			<?php echo 'login' === $active_panel ? '' : ' hidden'; ?>
		>
			<?php if ( $show_register ) : ?>
				<p class="apex-account-auth-lead"><?php esc_html_e( 'Welcome back. Sign in to manage orders and account details.', 'elite-shipping' ); ?></p>
			<?php endif; ?>

			<form class="woocommerce-form woocommerce-form-login login" method="post" novalidate>

				<?php do_action( 'woocommerce_login_form_start' ); ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // phpcs:ignore WordPress.Security.NonceVerification.Missing ?>
				</p>
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
					<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true" />
				</p>

				<?php do_action( 'woocommerce_login_form' ); ?>

				<p class="form-row apex-account-auth-actions">
					<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
						<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
					</label>
					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
					<button type="submit" class="woocommerce-button button woocommerce-form-login__submit<?php echo esc_attr( $btn_class ); ?>" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
				</p>
				<p class="woocommerce-LostPassword lost_password">
					<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
				</p>

				<?php do_action( 'woocommerce_login_form_end' ); ?>

			</form>
		</div>

		<?php if ( $show_register ) : ?>
			<div
				class="apex-account-auth-panel<?php echo 'register' === $active_panel ? ' is-active' : ''; ?>"
				id="apex-auth-panel-register"
				role="tabpanel"
				aria-labelledby="apex-auth-tab-register"
				data-auth-panel="register"
				<?php echo 'register' === $active_panel ? '' : ' hidden'; ?>
			>
				<p class="apex-account-auth-lead"><?php esc_html_e( 'Create an account to track orders and save your details for faster checkout.', 'elite-shipping' ); ?></p>

				<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

					<?php do_action( 'woocommerce_register_form_start' ); ?>

					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
							<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // phpcs:ignore WordPress.Security.NonceVerification.Missing ?>
						</p>

					<?php endif; ?>

					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
						<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) && is_string( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required aria-required="true" /><?php // phpcs:ignore WordPress.Security.NonceVerification.Missing ?>
					</p>

					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
							<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required aria-required="true" />
						</p>

					<?php else : ?>

						<p class="apex-account-auth-note"><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'woocommerce' ); ?></p>

					<?php endif; ?>

					<?php do_action( 'woocommerce_register_form' ); ?>

					<p class="woocommerce-form-row form-row">
						<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
						<button type="submit" class="woocommerce-Button woocommerce-button button<?php echo esc_attr( $btn_class ); ?> woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
					</p>

					<?php do_action( 'woocommerce_register_form_end' ); ?>

				</form>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
