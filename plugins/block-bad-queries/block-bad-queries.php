<?php
/*
	Plugin Name: Block Bad Queries (BBQ)
	Plugin URI: https://perishablepress.com/block-bad-queries/
	Description: BBQ is a super fast firewall that automatically protects WordPress against malicious URL requests.
	Tags: firewall, security, protect, malicious, hack,  blacklist, lockdown, eval, http, query, request, secure, spam, whitelist
	Usage: No configuration necessary. Upload, activate and done. BBQ blocks bad queries automically to protect your site against malicious URL requests. For advanced protection check out BBQ Pro.
	Author: Jeff Starr
	Author URI: https://plugin-planet.com/
	Contributors: specialk, aldolat, WpBlogHost, jameswilkes, juliobox, lernerconsult
	Donate link: https://monzillamedia.com/donate.html
	Requires at least: 4.1
	Tested up to: 5.5
	Stable tag: 20200811
	Version: 20200811
	Requires PHP: 5.6.20
	Text Domain: block-bad-queries
	Domain Path: /languages
	License: GPLv2 or later
*/

/*
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 
	2 of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	with this program. If not, visit: https://www.gnu.org/licenses/
	
	Copyright 2020 Monzilla Media. All rights reserved.
*/

if (!defined('ABSPATH')) die();

if (!defined('BBQ_VERSION')) define('BBQ_VERSION', '20200811');
if (!defined('BBQ_FILE'))    define('BBQ_FILE', plugin_basename(__FILE__));
if (!defined('BBQ_DIR'))     define('BBQ_DIR',  plugin_dir_path(__FILE__));
if (!defined('BBQ_URL'))     define('BBQ_URL',  plugins_url('/block-bad-queries/'));

function bbq_core() {
	
	$request_uri_array  = apply_filters('request_uri_items',  array('@eval', 'eval\(', 'UNION(.*)SELECT', '\(null\)', 'base64_', '\/localhost', '\%2Flocalhost', '\/pingserver', 'wp-config\.php', '\/config\.', '\/wwwroot', '\/makefile', 'crossdomain\.', 'proc\/self\/environ', 'usr\/bin\/perl', 'var\/lib\/php', 'etc\/passwd', '\/https\:', '\/http\:', '\/ftp\:', '\/file\:', '\/php\:', '\/cgi\/', '\.cgi', '\.cmd', '\.bat', '\.exe', '\.sql', '\.ini', '\.dll', '\.htacc', '\.htpas', '\.pass', '\.asp', '\.jsp', '\.bash', '\/\.git', '\/\.svn', ' ', '\<', '\>', '\/\=', '\.\.\.', '\+\+\+', '@@', '\/&&', '\/Nt\.', '\;Nt\.', '\=Nt\.', '\,Nt\.', '\.exec\(', '\)\.html\(', '\{x\.html\(', '\(function\(', '\.php\([0-9]+\)', '(benchmark|sleep)(\s|%20)*\(', 'indoxploi', 'xrumer', 'guangxiymcd'));
	$query_string_array = apply_filters('query_string_items', array('@@', '\(0x', '0x3c62723e', '\;\!--\=', '\(\)\}', '\:\;\}\;', '\.\.\/', '127\.0\.0\.1', 'UNION(.*)SELECT', '@eval', 'eval\(', 'base64_', 'localhost', 'loopback', '\%0A', '\%0D', '\%00', '\%2e\%2e', 'allow_url_include', 'auto_prepend_file', 'disable_functions', 'input_file', 'execute', 'file_get_contents', 'mosconfig', 'open_basedir', '(benchmark|sleep)(\s|%20)*\(', 'phpinfo\(', 'shell_exec\(', '\/wwwroot', '\/makefile', 'path\=\.', 'mod\=\.', 'wp-config\.php', '\/config\.', '\$_session', '\$_request', '\$_env', '\$_server', '\$_post', '\$_get', 'indoxploi', 'xrumer', '^www\.(.*)\.cn$'));
	$user_agent_array   = apply_filters('user_agent_items',   array('acapbot', '\/bin\/bash', 'binlar', 'casper', 'cmswor', 'diavol', 'dotbot', 'finder', 'flicky', 'md5sum', 'morfeus', 'nutch', 'planet', 'purebot', 'pycurl', 'semalt', 'shellshock', 'skygrid', 'snoopy', 'sucker', 'turnit', 'vikspi', 'zmeu'));
	
	$request_uri_string  = false;
	$query_string_string = false;
	$user_agent_string   = false;
	
	if (isset($_SERVER['REQUEST_URI'])     && !empty($_SERVER['REQUEST_URI']))     $request_uri_string  = $_SERVER['REQUEST_URI'];
	if (isset($_SERVER['QUERY_STRING'])    && !empty($_SERVER['QUERY_STRING']))    $query_string_string = $_SERVER['QUERY_STRING'];
	if (isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT'])) $user_agent_string   = $_SERVER['HTTP_USER_AGENT'];
	
	if ($request_uri_string || $query_string_string || $user_agent_string) {
		
		if (
			
			// strlen( $_SERVER['REQUEST_URI'] ) > 255 || // optional
			
			preg_match('/'. implode('|', $request_uri_array)  .'/i', $request_uri_string)  || 
			preg_match('/'. implode('|', $query_string_array) .'/i', $query_string_string) || 
			preg_match('/'. implode('|', $user_agent_array)   .'/i', $user_agent_string) 
			
		) {
			
			bbq_response();
			
		}
		
	}
	
}
add_action('plugins_loaded', 'bbq_core');

function bbq_response() {
	
	header('HTTP/1.1 403 Forbidden');
	header('Status: 403 Forbidden');
	header('Connection: Close');
	
	exit();
	
}

if (is_admin()) require_once BBQ_DIR .'bbq-settings.php';
