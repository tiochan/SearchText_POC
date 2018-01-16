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
 * Class chrono implements methods to measure the time spent in a tasks.
 */

class chrono {
	private $start;
	private $end;

	function __construct() {
		$this->start=0.0;
		$this->end=0.0;
	}

	private function getmicrotime(){
		list($usec, $sec) = explode(" ",microtime());
		return ((float)$usec + (float)$sec);
	}

	// Starts the chrono and show a message if set.
	public function chronoStart() {
		$this->start= $this->getmicrotime();
		return $this->start;
	}

	// Starts the chrono and show a message if set.
	public function chronoStop($startTimeOptional=0) {
		$this->end= $this->getmicrotime();
		$start= ($startTimeOptional > 0) ? $startTimeOptional : $this->start;
		$time = round($this->end - $start, 3);

		return $time;
	}
}
