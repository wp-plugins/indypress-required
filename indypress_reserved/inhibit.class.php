<?php
	/**
	 * indypress_inhibit() 
	 * 
	 * This class will filter the forms list to disable any form that requires
	 * permission that the current user has not
	 * 
	 * @author boyska <piuttosto@logorroici.org>
	 * @license GPLv2 or later
	 */
	class indypress_inhibit {
		function indypress_inhibit() {
			add_filter('get_indypress_publication_terms', array( $this, 'remove_forms' ) );
		}
		function remove_forms( $forms ) {
			/**
			 * check_form 
			 * 
			 * Checks if a form should be removed or not. If it should be removed, false
			 * is returned. Otherwise, $slug
			 */
			if(!function_exists('check_form')) {
				function check_form( $slug ) {
					$capabilities = get_option( 'indypressreserved_form_' . $slug, array() );
					if( !$capabilities )
						return $slug;
					$can = false;
					foreach( $capabilities as $cap ) {
						$can = current_user_can($cap);
						if( $can )
							break;
					}
					if( !$can )
						return false;
					return $slug;
				}
			}
			$new_forms = array();
			foreach( $forms as $slug ) {
				if( check_form( $slug ) !== false )
					$new_forms[] = $slug;
			}

			return $new_forms;
		}
	}
