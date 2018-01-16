<?php
/**
 * User: Sebastian Gomez
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
 * along with SearchTextEngine;l if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
 * or see http://www.gnu.org/licenses/.
 */

/**
 * Here are defined the search text default configuration parameters.
 * Be free to adapt them as you consider...
 *
 */


// Constants
define("DEBUG", false);                          // Set it to false in production environments.

define("WORD_SEPARATOR"," ");                   // Which is the word separator used?
define("SEARCH_IS_KEY_INSENSITIVE", false);     // [true|false] If false, the search is made key-sensitive. If true, all
												// file words and search words are converted to lower case previously to
												// compare process.
define("SEARCH_ENGINE", "strict");              // Search engine:
												// - simple: hits per word found, independent on positions
												// - strict: hits per consecutive words
define("STRICT_HIT_RANKING",5);                 // Value for each HIT in strict mode (consecutive word)
define("RANKING_TOP", 10);                      // This constant


// Other stuffs used along the program:
$charsToReplace= array(                         // This array contains the chars to be replaced.
	"." => WORD_SEPARATOR,
	"," => WORD_SEPARATOR,
	"\n" => WORD_SEPARATOR,
	"\r" => WORD_SEPARATOR,
	WORD_SEPARATOR . WORD_SEPARATOR => WORD_SEPARATOR
);
