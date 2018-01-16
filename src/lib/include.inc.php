<?php
/**
 * User: Sebastian Gomez (sebastian.g.moran@gmail.com)
 * Date: 14/01/18
 *
 * @package SearchText
 * @subpackage
 * @copyright (C) -
 * @license GNU/GPL, see license.txt
 * SearchTextEngineis free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.
 *
 * SearchTextEngine is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with SearchTextEngine; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
 * or see http://www.gnu.org/licenses/.
 */

/**
 * This file must be included from all PHP scripts of the same app.
 *
 */

define("SYSHOME", dirname(dirname(__FILE__)));              // Get current file directory
define("INC_DIR", SYSHOME . "/lib");                        // Include directory

// Include auxiliar functions
include INC_DIR . "/chrono.class.php";
include INC_DIR . "/output.inc.php";

// Create a global var for chrono to make it available over the whole app.
global $chrono;
$chrono= new chrono();
