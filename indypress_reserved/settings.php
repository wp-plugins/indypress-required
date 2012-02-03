<?php

class indypressreserved_settings {
	function indypressreserved_settings() {
		add_filter('indypress_settings_adminpage', create_function('$v', 'return true;')); //we "plug" into admin page
		add_action( 'admin_menu', array( $this, 'menu' ) );
	}
	function main_page() {
		if ( !current_user_can( 'administrator' ) )
			wp_die( __( 'You do not have sufficient permissions to access this page.' , 'indypress') );
		?>
		<div class="wrap">
			<h2>Indypress reserved</h2>
			You can choose who can use each form<br/>
			If you select nothing, everyone can use it. If you select more than one capability, any user having one of that capabilities can use the form (so, it's an OR, not an AND)
			<form action="options.php" method="post">
			<?php settings_fields( 'indypressreserved' ); ?>
			<?php do_settings_sections( 'indypress_reserved' ); ?>
			<p class="submit">
				<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'indypress'); ?>" />
			</p>
			</form>
			</div>
		<?php
		//TODO: report a summary about the current status of the plugin, with links to documentation
	}
	function register_settings() {
		add_settings_section( 'indypress_reserved_permission',
			__('Permissions', 'indypress_reserved'),
			create_function('',''),
			'indypress_reserved' /*page*/);

		foreach( get_indypress_publication_terms() as $slug ) {
			$option = 'indypressreserved_form_' . $slug;
			register_setting( 'indypressreserved' /*group*/, $option, create_function( '$v', 'if(is_array($v)) return $v; return array();' ) );
			add_settings_field( $option,
				$slug,
				create_function('', 'return setting_multiple_cap("' .  $option . '" );'),
				'indypress_reserved', //page
	 			'indypress_reserved_permission' /*section*/ );
		}


	}
	function menu() {
		add_submenu_page( 'indypress', 'Indypress reserved forms', 'Reserved', 'administrator', 'indypress_reserved_settings', array( $this, 'main_page' ), NULL );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

}
function setting_multiple_cap ($option) {
	//this code is pure CRAP; php sucks: no anonymous function, no nested function
	//with parent scope, no partial application, no currying
	$form_capabilities = get_option( $option , array() );
	echo '<p>Current: ' . implode(',', $form_capabilities) . '</p>';
	global $wp_roles, $wpdb;

	// build full capabilities list from all roles except Administrator 
	$fullCapsList = array();  
	$roles = array();
	foreach($wp_roles->roles as $rolename => $role) {
		// validate if capabilities is an array
		if (isset($role['capabilities']) && is_array($role['capabilities'])) {
			foreach ($role['capabilities'] as $capability=>$value) {
				if (isset($fullCapsList[$capability]))
					continue;
				if( in_array($capability, $form_capabilities) )
					$fullCapsList[$capability] = 'selected="selected"';
				else
					$fullCapsList[$capability] = '';
			}
			$roles[] = $rolename;
	  }
	}
	//Array is sorted this way: first, there are roles (alphabetically sorted),
	//then there are capabilities (alphabetically sorted)
	//Example: administrator, editor, add_users, edit_posts, upload_files
	krsort( &$fullCapsList );
	arsort($roles);
	foreach($roles as $capability) {
		if (isset($fullCapsList[$capability]))
			continue;
		if( in_array($capability, $form_capabilities) )
		  $fullCapsList[$capability] = 'selected="selected"';
		else
		  $fullCapsList[$capability] = '';
	}
	$fullCapsList = array_reverse( $fullCapsList );
	echo <<<EOHTML
	<select name="${option}[]" multiple="multiple">
EOHTML;
	foreach( $fullCapsList as $cap=>$selected ) {
		if( isset($wp_roles->role_names[$cap]) )
			$coolname = 'Role: ' . $wp_roles->role_names[$cap];
		else
			$coolname = $cap;
		echo <<<EOHTML
		<option value="$cap" $selected >$coolname</option>
EOHTML;
	}
	echo <<<EOHTML
	</select>
EOHTML;
};
