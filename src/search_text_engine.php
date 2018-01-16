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

include "search_text_engine.class.php";

// Simple function to search usage using the current program name.
function show_usage() {
	global $argv;

	echo "Usage:\n" .
		"   " . $argv[0] . " <directory_to_scan>\n\n";
}

// Check for number of parameters and exit if fails
if($argc < 2) {
	show_usage();
	exit(1);
}

// Check the directory, if exists, if there are files, ...
$dirName= $argv[1];
if(!is_dir($dirName)) {
	echo "Error: Directory $dirName does not exists or is not a dir.\n\n";
	exit(1);
}

$search_engine= new searchEngine($argv[1]);     // Lets see that here is instantianted the search class !!!
$files= $search_engine->listSearchFiles();

if(count($files) == 0) {
	echo "Attention, no files found on the directory.";
	exit(0);
}

echo count($files) . " files read in directory " . $argv[1] . "\n";


// Main loop waiting orders
$handle = fopen ("php://stdin","r");
while(1) {
	echo "search> ";

	$line= trim(fgets($handle));
	if($line == ":quit") break;     // Received the order to exit the program.

	// Received a string to search for...
	$ranking= $search_engine->searchText($line);

	$count=0;
	foreach($ranking as $fileName => $ranking) {
		echo $fileName . " : " . $ranking . "%\n";
		$count++;

		if($count >= RANKING_TOP) break;
	}
}
fclose($handle);

echo "Bye!\n\n";

// End reached. Exit with exit code 0 (success)
exit(0);
