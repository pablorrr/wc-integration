<?php
/**
 * Plugin Name: WC Integration
 * Plugin URI: https://websitecreator.cba.pl
 * Description: Change product name in WC integration bookmark
 * Author: Pawel Kalisz
 * Author URI: https://websitecreator.cba.pl
 * Version: 1.0
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */


if (!defined('ABSPATH')) exit;
require 'wc-tabs.php';
function Run_WC_Tabs(): WC_Tabs
{
    return WC_Tabs::instance();
}
Run_WC_Tabs();
