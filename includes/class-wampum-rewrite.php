<?php
/**
 * Wampum
 *
 * @package   Wampum
 * @author    Mike Hemberger <mike@bizbudding.com.com>
 * @link      https://github.com/JiveDig/wampum/
 * @copyright 2016 Mike Hemberger
 * @license   GPL-2.0+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main plugin class.
 *
 * @package Wampum
 */
class Wampum_Rewrite {

	/**
	 * @setup rewrite rules
	 */
	protected function rewrite_rules(){
		if ( ! is_singular('wampum_programs') ) {
			return;
		}
		$user_page_id = $ultimatemember->permalinks->core['user'];
		$account_page_id = $ultimatemember->permalinks->core['account'];
		$user = get_post($user_page_id);

		if ( isset( $user->post_name ) ) {

			$user_slug = $user->post_name;
			$account = get_post($account_page_id);
			$account_slug = $account->post_name;

			add_rewrite_rule(
					'^'.$user_slug.'/([^/]*)$',
					'index.php?page_id='.$user_page_id.'&um_user=$matches[1]',
					'top'
			);

			add_rewrite_rule(
					'^'.$account_slug.'/([^/]*)$',
					'index.php?page_id='.$account_page_id.'&um_tab=$matches[1]',
					'top'
			);
			flush_rewrite_rules( true );
		}
	}

}