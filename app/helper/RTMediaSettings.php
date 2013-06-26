<?php
/**
 * Description of RTMediaSettings
 *
 * @author Gagandeep Singh <gagandeep.singh@rtcamp.com>
 * @author Joshua Abenazer <joshua.abenazer@rtcamp.com>
 */
if (!class_exists('RTMediaSettings')) {

    class RTMediaSettings {

        public function __construct() {
            add_action('admin_init', array($this, 'settings'));
//            if (is_multisite()) {
//                add_action('network_admin_notices', array($this, 'privacy_notice'));
//            } else {
//                add_action('admin_notices', array($this, 'privacy_notice'));
//            }
        }

        /**
         * Register Settings
         *
         * @global string 'rt-media'
         */

        /**
         *
         * @global BPMediaAddon $rt_media_addon
         */
        public function settings() {
            global $rt_media, $rt_media_addon;
            
			// Save Settings first then proceed.
			if(isset($_POST) && count($_POST)) {

				$options = $_POST['rt-media-options'];
                                
				if(isset($options['rt-media-general']))
					$this->save_general_settings($options['rt-media-general']);
				else
					$this->sanitize_general_settings();

				if(isset($options['rt-media-allowed-types']))
					$this->save_types_settings($options['rt-media-allowed-types']);
				else
					$this->sanitize_types_settings();

				if(isset($options['rt-media-default-sizes']))
					$this->save_sizes_settings($options['rt-media-default-sizes']);
				else
					$this->sanitize_sizes_settings();

				if(isset($options['rt-media-privacy']))
					$this->save_privacy_settings($options['rt-media-privacy']);
				else
					$this->sanitize_privacy_settings();

				if(isset($options['rt-media-buddypress']))
					$this->save_buddypress_settings($options['rt-media-buddypress']);
				else
					$this->sanitize_buddypress_settings();
			}
			
            $rt_media_addon = new RTMediaAddon();
            add_settings_section('rtm-addons', __('BuddyPress Media Addons for Photos', 'rt-media'), array($rt_media_addon, 'get_addons'), 'rt-media-addons');

            add_settings_section('rtm-support', __('Support', 'rt-media'), array($this, 'rt_media_support_intro'), 'rt-media-support');

//            if (!BPMediaPrivacy::is_installed()) {
//                $rt_media_privacy = new BPMediaPrivacySettings();
//                add_filter('rt_media_add_sub_tabs', array($rt_media_privacy, 'ui'), 99, 2);
//                add_settings_section('rtm-privacy', __('Update Database', 'rt-media'), array($rt_media_privacy, 'init'), 'rt-media-privacy');
//            }

            //$rt_media_album_importer = new BPMediaAlbumimporter();
            //add_settings_section('rtm-rt-album-importer', __('BP-Album Importer', 'rt-media'), array($rt_media_album_importer, 'ui'), 'rt-media-importer');
            //register_setting('rt_media', 'rt_media_options', array($this, 'sanitize'));
        }

		public function sanitize_general_settings() {
			rt_media_update_site_option('rt-media-albums-enabled', 0);
			rt_media_update_site_option('rt-media-comments-enabled', 0);
			rt_media_update_site_option('rt-media-download-button', 0);
			rt_media_update_site_option('rt-media-enable-lightbox', 0);
			rt_media_update_site_option('rt-media-per-page-media', 0);
			rt_media_update_site_option('rt-media-media-end-point_enable', 0);
			rt_media_update_site_option('rt-media-show-admin-menu', 0);

			global $rt_media;
			$key = 'rt-media-general';

			$rt_media->options[$key]['rt-media-albums-enabled']['args']['value'] = 0;
			$rt_media->options[$key]['rt-media-comments-enabled']['args']['value'] = 0;
			$rt_media->options[$key]['rt-media-download-button']['args']['value'] = 0;
			$rt_media->options[$key]['rt-media-enable-lightbox']['args']['value'] = 0;
			$rt_media->options[$key]['rt-media-per-page-media']['args']['value'] = 0;
			$rt_media->options[$key]['rt-media-media-end-point_enable']['args']['value'] = 0;
			$rt_media->options[$key]['rt-media-show-admin-menu']['args']['value'] = 0;
		}

		public function sanitize_types_settings_bak() {			

			global $rt_media;

			$rt_media->options['rt-media-allowed-types'] = wp_parse_args($rt_media->allowed_types, $rt_media->options['rt-media-allowed-types']);

			rt_media_update_site_option('rt-media-allowed-types', $rt_media->options['rt-media-allowed-types']);
		}

		public function sanitize_sizes_settings() {
			$options = array(
				'image' => array(
					'title' => __("Image","rt-media"),
					'thumbnail' => array(
						'title' => __("Thumbnail","rt-media"),
						'dimensions' => array('width' => 0, 'height' => 0, 'crop' => 0)
					),
					'medium' => array(
						'title' => __("Medium","rt-media"),
						'dimensions' => array('width' => 0, 'height' => 0, 'crop' => 0)
					),
					'large' => array(
						'title' => __("Large","rt-media"),
						'dimensions' => array('width' => 0, 'height' => 0, 'crop' => 0)
					)
				),
				'video' => array(
					'title' => __("Video","rt-media"),
					'activity_player' => array(
						'title' => __("Activity Player","rt-media"),
						'dimensions' => array('width' => 0, 'height' => 0)
					),
					'single_player' => array(
						'title' => __("Single Player","rt-media"),
						'dimensions' => array('width' => 0, 'height' => 0)
					)
				),
				'audio' => array(
					'title' => __("Audio","rt-media"),
					'activity_player' => array(
						'title' => __("Activity Player","rt-media"),
						'dimensions' => array('width' => 0)
					),
					'single_player' => array(
						'title' => __("Single Player","rt-media"),
						'dimensions' => array('width' => 0)
					)
				),
				'featured' => array(
					'title' => __("Featured Media","rt-media"),
					'default' => array(
						'title' => __("Default","rt-media"),
						'dimensions' => array('width' => 0, 'height' => 0, 'crop' => 0)
					)
				)
			);

			global $rt_media;
			$rt_media->options['rt-media-default-sizes'] = wp_parse_args($options, $rt_media->options['rt-media-default-sizes']);

			rt_media_update_site_option('rt-media-default-sizes', $rt_media->options['rt-media-default-sizes']);
		}

		public function sanitize_privacy_settings() {

			$options = array(
				'enable' => array(
					'title' => __("Enable Privacy","rt-media"),
					'callback' => array("RTMediaFormHandler", "checkbox"),
					'args' => array(
						'id' => 'rt-media-privacy-enable',
						'key' => 'rt-media-privacy][enable',
						'value' => 0
					)
				),
				'default' => array(
					'title' => __("Default Privacy","rt-media"),
					'callback' => array("RTMediaFormHandler","radio"),
					'args' => array(
						'key' => 'rt-media-privacy][default',
						'radios' => array(
							60 => __('<strong>Private</strong> - Visible only to the user', 'rt-media'),
							40 => __('<strong>Friends</strong> - Visible to user\'s friends', 'rt-media'),
							20 => __('<strong>Users</strong> - Visible to registered users', 'rt-media'),
							0 => __('<strong>Public</strong> - Visible to the world', 'rt-media')
						),
						'default' => 0
					),
				),
				'user-override' => array(
					'title' => __("User Override","rt-media"),
					'callback' => array("RTMediaFormHandler", "checkbox"),
					'args' => array(
						'key' => 'rt-media-privacy][user-override',
						'value' => 0
					)
				)
			);

			global $rt_media;
			$rt_media->options['rt-media-privacy'] = wp_parse_args($options, $rt_media->options['rt-media-privacy']);

			rt_media_update_site_option('rt-media-privacy', $rt_media->options['rt-media-privacy']);
		}

		public function sanitize_buddypress_settings() {

			global $rt_media;
			$key = 'rt-media-buddypress';

			rt_media_update_site_option('rt-media-enable-on-activity', 0);
			rt_media_update_site_option('rt-media-enable-on-profile', 0);
			rt_media_update_site_option('rt-media-enable-on-group', 0);

			$rt_media->options[$key]['rt-media-enable-on-activity']['args']['value'] = 0;
			$rt_media->options[$key]['rt-media-enable-on-profile']['args']['value'] = 0;
			$rt_media->options[$key]['rt-media-enable-on-group']['args']['value'] = 0;

		}

		public function save_general_settings($settings) {

			global $rt_media;
			$prev_options = $rt_media->options['rt-media-general'];
			$defaults = array(
				"rt-media-albums-enabled" => 0,
				"rt-media-comments-enabled" => 0,
				"rt-media-download-button" => 0,
				"rt-media-enable-lightbox" => 0,
				"rt-media-per-page-media" => rt_media_get_site_option('rt-media-per-page-media'),
				"rt-media-show-admin-menu" => 0
			);

			$settings = wp_parse_args($settings,$defaults);

			foreach ($settings as $key => $value) {
				rt_media_update_site_option($key, $value);
				$prev_options[$key]['args']['value'] = $settings[$key];
			}

			$rt_media->options['rt-media-general'] = $prev_options;
		}

		public function save_types_settings($settings) {
			global $rt_media;

			$defaults = $rt_media->options['rt-media-allowed-types'];

			foreach ($defaults as $key => $value) {

				if(isset($settings[$key]['enable']))
					$defaults[$key]['enable'] = $settings[$key]['enable'];
				else
					$defaults[$key]['enable'] = 0;
				if(isset($settings[$key]['featured']))
					$defaults[$key]['featured'] = $settings[$key]['featured'];
				else
					$defaults[$key]['featured'] = 0;
			}
			rt_media_update_site_option('rt-media-allowed-types', $defaults);
			$rt_media->options['rt-media-allowed-types'] = $defaults;
		}

		public function save_sizes_settings($settings) {
			global $rt_media;

			$old_values = $rt_media->options['rt-media-default-sizes'];
			$defaults = $rt_media->options['rt-media-default-sizes'];

			foreach ($defaults as $type => $type_value) {
				unset($type_value['title']);
				foreach ($type_value as $entity => $entity_value) {
					unset($entity_value['title']);
					foreach ($entity_value as $dimensions) {
						foreach ($dimensions as $dimension => $value) {
							if(isset($settings[$type][$entity][$dimension]))
								$old_values[$type][$entity]['dimensions'][$dimension] = $settings[$type][$entity][$dimension];
							else
								$old_values[$type][$entity]['dimensions'][$dimension] = 0;
						}
					}
				}
			}

			rt_media_update_site_option('rt-media-default-sizes', $old_values);
			$rt_media->options['rt-media-default-sizes'] = $old_values;
		}

		public function save_privacy_settings($settings) {
			global $rt_media;

			$defaults = $rt_media->options['rt-media-privacy'];

			foreach ($defaults as $key => $value) {
				if(isset($settings[$key])) {
					if($key=='default')
						$defaults[$key]['args']['default'] = $settings[$key];
					else
						$defaults[$key]['args']['value'] = $settings[$key];
				} else {
					if($key=='default')
						$defaults[$key]['args']['default'] = 0;
					else
						$defaults[$key]['args']['value'] = 0;
				}
			}

			rt_media_update_site_option('rt-media-privacy', $defaults);
			$rt_media->options['rt-media-privacy'] = $defaults;
		}

		public function save_buddypress_settings($settings) {
			global $rt_media;

			$defaults = $rt_media->options['rt-media-buddypress'];

			foreach ($defaults as $key => $value) {
				if(isset($settings[$key])){
					rt_media_update_site_option($key, $value);
					$defaults[$key]['args']['value'] = $settings[$key];
				} else {
					rt_media_update_site_option($key, 0);
					$defaults[$key]['args']['value'] = 0;
				}
			}

			$rt_media->options['rt-media-buddypress'] = $defaults;
		}



        public function network_notices() {
            $flag = 1;
            if (rt_media_get_site_option('rtm-media-enable', false)) {
                echo '<div id="setting-error-bpm-media-enable" class="error"><p><strong>' . rt_media_get_site_option('rtm-media-enable') . '</strong></p></div>';
                delete_site_option('rtm-media-enable');
                $flag = 0;
            }
            if (rt_media_get_site_option('rtm-media-type', false)) {
                echo '<div id="setting-error-bpm-media-type" class="error"><p><strong>' . rt_media_get_site_option('rtm-media-type') . '</strong></p></div>';
                delete_site_option('rtm-media-type');
                $flag = 0;
            }
            if (rt_media_get_site_option('rtm-media-default-count', false)) {
                echo '<div id="setting-error-bpm-media-default-count" class="error"><p><strong>' . rt_media_get_site_option('rtm-media-default-count') . '</strong></p></div>';
                delete_site_option('rtm-media-default-count');
                $flag = 0;
            }

            if (rt_media_get_site_option('rtm-recount-success', false)) {
                echo '<div id="setting-error-bpm-recount-success" class="updated"><p><strong>' . rt_media_get_site_option('rtm-recount-success') . '</strong></p></div>';
                delete_site_option('rtm-recount-success');
                $flag = 0;
            } elseif (rt_media_get_site_option('rtm-recount-fail', false)) {
                echo '<div id="setting-error-bpm-recount-fail" class="error"><p><strong>' . rt_media_get_site_option('rtm-recount-fail') . '</strong></p></div>';
                delete_site_option('rtm-recount-fail');
                $flag = 0;
            }

            if (get_site_option('rtm-settings-saved') && $flag) {
                echo '<div id="setting-error-bpm-settings-saved" class="updated"><p><strong>' . get_site_option('rtm-settings-saved') . '</strong></p></div>';
            }
            delete_site_option('rtm-settings-saved');
        }

        public function allowed_types() {
            $allowed_types = get_site_option('upload_filetypes', 'jpg jpeg png gif');
            $allowed_types = explode(' ', $allowed_types);
            $allowed_types = implode(', ', $allowed_types);
            echo '<span class="description">' . sprintf(__('Currently your network allows uploading of the following file types. You can change the settings <a href="%s">here</a>.<br /><code>%s</code></span>', 'rt-media'), network_admin_url('settings.php#upload_filetypes'), $allowed_types);
        }

        /**
         * Sanitizes the settings
         */

        /**
         *
         * @global type $rt_media_admin
         * @param type $input
         * @return type
         */
        public function sanitize($input) {
            global $rt_media_admin;
            if (isset($_POST['refresh-count'])) {
                if ($rt_media_admin->update_count()) {
                    if (is_multisite())
                        update_site_option('rtm-recount-success', __('Recounting of media files done successfully', 'rt-media'));
                    else
                        add_settings_error(__('Recount Success', 'rt-media'), 'rtm-recount-success', __('Recounting of media files done successfully', 'rt-media'), 'updated');
                } else {
                    if (is_multisite())
                        update_site_option('rtm-recount-fail', __('Recounting Failed', 'rt-media'));
                    else
                        add_settings_error(__('Recount Fail', 'rt-media'), 'rtm-recount-fail', __('Recounting Failed', 'rt-media'));
                }
            }
//            if (!isset($_POST['rt_media_options']['enable_on_profile']) && !isset($_POST['rt_media_options']['enable_on_group'])) {
//                if (is_multisite())
//                    update_site_option('rtm-media-enable', __('Enable BuddyPress Media on either User Profiles or Groups or both. Atleast one should be selected.', 'rt-media'));
//                else
//                    add_settings_error(__('Enable BuddyPress Media', 'rt-media'), 'rtm-media-enable', __('Enable BuddyPress Media on either User Profiles or Groups or both. Atleast one should be selected.', 'rt-media'));
//                $input['enable_on_profile'] = 1;
//            }
            if (!isset($_POST['rt_media_options']['videos_enabled']) && !isset($_POST['rt_media_options']['audio_enabled']) && !isset($_POST['rt_media_options']['images_enabled'])) {
                if (is_multisite())
                    update_site_option('rtm-media-type', __('Atleast one Media Type Must be selected', 'rt-media'));
                else
                    add_settings_error(__('Media Type', 'rt-media'), 'rtm-media-type', __('Atleast one Media Type Must be selected', 'rt-media'));
                $input['images_enabled'] = 1;
            }

            $input['default_count'] = intval($_POST['rt_media_options']['default_count']);
            if (!is_int($input['default_count']) || ($input['default_count'] < 0 ) || empty($input['default_count'])) {
                if (is_multisite())
                    update_site_option('rtm-media-default-count', __('"Number of media" count value should be numeric and greater than 0.', 'rt-media'));
                else
                    add_settings_error(__('Default Count', 'rt-media'), 'rtm-media-default-count', __('"Number of media" count value should be numeric and greater than 0.', 'rt-media'));
                $input['default_count'] = 10;
            }
            if (is_multisite())
                update_site_option('rtm-settings-saved', __('Settings saved.', 'rt-media'));
            do_action('rt_media_sanitize_settings', $_POST, $input);
            return $input;
        }

        public function image_settings_intro() {
            if (is_plugin_active('regenerate-thumbnails/regenerate-thumbnails.php')) {
                $regenerate_link = admin_url('/tools.php?page=regenerate-thumbnails');
            } elseif (array_key_exists('regenerate-thumbnails/regenerate-thumbnails.php', get_plugins())) {
                $regenerate_link = admin_url('/plugins.php#regenerate-thumbnails');
            } else {
                $regenerate_link = wp_nonce_url(admin_url('update.php?action=install-plugin&plugin=regenerate-thumbnails'), 'install-plugin_regenerate-thumbnails');
            }
            echo '<span class="description">' . sprintf(__('If you make changes to width, height or crop settings, you must use "<a href="%s">Regenerate Thumbnail Plugin</a>" to regenerate old images."', 'rt-media'), $regenerate_link) . '</span>';
			echo '<div class="clearfix">&nbsp;</div>';
        }

        /**
         * Output a checkbox
         *
         * @global array $rt_media
         * @param array $args
         */

        public function privacy_notice() {
            if (current_user_can('create_users')) {
//                if (BPMediaPrivacy::is_installed())
//                    return;
                $url = add_query_arg(
                        array('page' => 'rt-media-privacy'), (is_multisite() ? network_admin_url('admin.php') : admin_url('admin.php'))
                );

                $notice = '
				<div class="error">
				<p>' . __('BuddyPress Media 2.6 requires a database upgrade. ', 'rt-media')
                        . '<a href="' . $url . '">' . __('Update Database', 'rt-media') . '.</a></p>
				</div>
				';
                echo $notice;
            }
        }

        public function rt_media_support_intro() {
            echo '<p>' . __('If your site has some issues due to BuddyPress Media and you want one on one support then you can create a support topic on the <a target="_blank" href="http://rtcamp.com/groups/buddypress-media/forum/?utm_source=dashboard&utm_medium=plugin&utm_campaign=buddypress-media">rtCamp Support Forum</a>.', 'rt-media') . '</p>';
            echo '<p>' . __('If you have any suggestions, enhancements or bug reports, then you can open a new issue on <a target="_blank" href="https://github.com/rtCamp/buddypress-media/issues/new">GitHub</a>.', 'rt-media') . '</p>';
        }

    }

}
?>
