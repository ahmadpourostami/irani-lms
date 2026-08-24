<?php
/**
 * Plugin Name: Irani LMS
 * Plugin URI: https://github.com/ahmadpourostami/irani-lms
 * Description: بستر بومی مدیریت و فروش دوره‌های آموزشی وردپرس.
 * Version: 0.1.0
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Author: Ahmad Pourostami
 * Text Domain: irani-lms
 * Domain Path: /languages
 * License: GPL-2.0-or-later
 */

defined( 'ABSPATH' ) || exit;

const IRANI_LMS_VERSION = '0.1.0';
const IRANI_LMS_FILE    = __FILE__;
const IRANI_LMS_PATH    = __DIR__ . '/';
const IRANI_LMS_URL     = plugin_dir_url( __FILE__ );

require_once IRANI_LMS_PATH . 'src/Plugin.php';

\IraniLMS\Plugin::boot();
