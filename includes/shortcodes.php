<?php

if (!defined('ABSPATH')) {
	exit;
}
if (!class_exists('CustomShortcodes')) {

	class CustomShortcodes extends CustomFunctions {

		public function __construct() {
			add_shortcode('relation', array(&$this, 'relation_shortcode_func'));
			add_shortcode('Relation', array(&$this, 'relation_shortcode_func'));
			add_shortcode('send_quiz', array(&$this, 'send_quizzes_shortcode_func'));
			add_shortcode('results_relation', array(&$this, 'results_relation_shortcode_func'));
			add_shortcode('quiz_received', array(&$this, 'quiz_received_shortcode_func'));
		}

		public function relation_shortcode_func($atts = array(), $content = '') {
			$atts = shortcode_atts(array(
				'limit' => -1,
				), $atts, 'relation');
			$current_user_id = get_current_user_id();
			ob_start();
			if (user_can($current_user_id, 'expert')) {
				$user_ids = get_users(array('fields' => 'ID', 'role' => 'autist', 'meta_key' => 'user_expert', 'meta_value' => $current_user_id, 'meta_compare' => '=', 'number' => $atts['limit']));
				$user_ids = (!empty($user_ids)) ? $user_ids : array(0);
				if (class_exists('BuddyPress')) {
					include(QSM_CUSTOM_ADDON_TEMPLATES_DIR . '/users-list.php');
				}
			}
			if (user_can($current_user_id, 'autist')) {
				$user_expert = get_user_meta($current_user_id, 'user_expert', true);
				$user_ids = (!empty($user_expert)) ? array($user_expert) : array(0);
				if (class_exists('BuddyPress')) {
					include(QSM_CUSTOM_ADDON_TEMPLATES_DIR . '/users-list.php');
				}
			}
			return ob_get_clean();
		}

		public function send_quizzes_shortcode_func($atts = array(), $content = '') {
			$current_user_id = get_current_user_id();
			ob_start();
			if (user_can($current_user_id, 'expert')) {
				$user_autists = get_users(array('role' => 'autist', 'meta_key' => 'user_expert', 'meta_value' => $current_user_id, 'meta_compare' => '='));
				include(QSM_CUSTOM_ADDON_TEMPLATES_DIR . '/send-quiz.php');
			}
			return ob_get_clean();
		}

		public function results_relation_shortcode_func($atts = array(), $content = '') {
			$current_user_id = get_current_user_id();
			ob_start();
			if (user_can($current_user_id, 'expert')) {
				include(QSM_CUSTOM_ADDON_TEMPLATES_DIR . '/results-relation.php');
			}
			return ob_get_clean();
		}

		public function quiz_received_shortcode_func($atts = array(), $content = '') {
			$current_user_id = get_current_user_id();
			ob_start();
			if (user_can($current_user_id, 'autist') || user_can($current_user_id, 'subscriber')) {
				include(QSM_CUSTOM_ADDON_TEMPLATES_DIR . '/quiz-received.php');
			}
			return ob_get_clean();
		}

	}

}
new CustomShortcodes();
