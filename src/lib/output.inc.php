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
 * Output control functions.
 */

/**
 * If DEBUG is enabled, shows the message.
 * @param $msg
 */
function debugShowMessage($msg) {

	if(DEBUG) echo " [debug] $msg\n";
}

/**
 * In case DEBUG is enabled, call chrono start function and show the message.
 * @param $msg
 * @return float
 */
function debugStartProcess($msg) {
	global $chrono;

	if(!DEBUG) return;

	debugShowMessage(">>> Starting process: $msg");
	return $chrono->chronoStart();
}

/**
 * In case DEBUG is enabled, call chrono end function and show the message with the time spent.
 * @param $msg
 * @return float
 */
function debugEndProcess($msg= "") {
	global $chrono;

	if(!DEBUG) return;

	$time= $chrono->chronoStop();
	debugShowMessage("<<< Process ended: $msg (took $time seconds)\n");
	return $time;
}