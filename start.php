<?php

/**
 * Videolist Extras
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 * @copyright Copyright (c) 2015, Ismayil Khayredinov
 */

require_once __DIR__ . '/autoloader.php';

elgg_register_event_handler('init', 'system', 'videolist_extras_init');

/**
 * Initialize the plugin
 * @return void
 */
function videolist_extras_init() {

	elgg_extend_view('plugins/videolist/settings', 'plugins/videolist/domains');

	elgg_register_plugin_hook_handler('videolist:prepare', 'platforms', 'videolist_extras_add_platform');
}

/**
 * Adds HTTP Parser to platforms
 * 
 * @param string                        $hook      "videolsit:prepare"
 * @param string                        $type      "platforms"
 * @param Videolist_PlatformInterface[] $platforms Platforms
 * @param array                         $params    Hook params
 * @return Videolist_PlatformInterface[]
 */
function videolist_extras_add_platform($hook, $type, $platforms, $params) {

	$parser = new hypeJunction\Videolist\HttpParser();
	$platforms[$parser->getType()][] = $parser;

	return $platforms;
}