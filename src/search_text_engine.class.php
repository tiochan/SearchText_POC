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
 * This is a search engine class for text files.
 * The simple usage is to instance the class passing as argument the directory where to find for.
 * Then you can call the searchText function which will return the list of files matching the
 * text that you passed as parameter with a match percentage.
 *
 * What does % match means?
 * -----------------------------------------
 * - 100%: the file contains all the words to search in the same order.
 * - This case will not be listed, but 0% means that the file does not contains any word.
 * - Between 100% and 0%, the match result is based on the number of hits.
 *
 * A word is a set of characters limited by those characters:
 * - A blank space
 * - Usual word separators ('.', ',', ';')
 * - End of line
 * - End of file
 *
 * How words are compared?
 * -----------------------------------------
 * - Two words are EQUALS if they have the same characters in the same order. The comparison process is in lower case
 *   or not, depending the constant "SEARCH_IS_KEY_INSENSITIVE" defined on the config file.
 */

include "lib/include.inc.php";
include "search_text.config.php";


class searchEngine {

	/**
	 * Those attributes are protected and not private in order to allow this class
	 * to be inherited to others and use the same attributes.
	 */
	protected $directory;       // Protected attribute to remember the directory.
	protected $fileMap;         // Associative array key => File ID, value => File name.
	protected $contentMap;      // In-memory map of file contents.

	private $iteration;

	/**
	 * Constructor.
	 *
	 * Mandatory the directory where are located the files to search into.
	 * @param string $directory
	 */
	public function searchEngine(string $directory) {

		$this->getFiles($directory);
		$this->createTextMap();
	}

	/**
	 * This method is used
	 * @param $directory
	 */
	protected function getFiles($directory) {

		debugShowMessage("Starting directory listing at $directory");

		// Store in the private attribute
		$this->directory= $directory;

		if ($handle = opendir($directory)) {

			// Loop over the directory to get files
			while (false !== ($entry = readdir($handle))) {
				if($entry == ".") continue;
				if($entry == "..") continue;

				debugShowMessage("Found file: $entry");
				$this->fileMap[]=$this->directory . "/" . $entry;
			}

			closedir($handle);
		}
	}

	/**
	 * Method to search for a string into the files.
	 *
	 * @param string $textToSearch
	 * @internal param string $text
	 * @return an array in the form:
	 *  file_name_that_matches, hit_percent
	 */
	public function searchText(string $textToSearch) {

		$start= debugStartProcess("Search process");

		// Extract all words using the defined word separator.
		$content_words= explode(WORD_SEPARATOR, $textToSearch);

		// Search each word
		$hitsArray= array();
		$wordCounter=0;
		foreach($content_words as $word) {

			debugStartProcess("* Searching word $word");

			$hitsArray[] = $this->searchWord($word);
			$wordCounter++;

			debugEndProcess("* Search $word");
		}

		debugStartProcess("* Ranking process");
		$rankingArray= $this->rankHits($hitsArray, $content_words);
		debugEndProcess("* Ended");

		// Finally, sort the array using the ranking (value):
		arsort($rankingArray);

		debugEndProcess("SEARCH PROCESS", $start);

		// And return it
		return $rankingArray;
	}

	/**
	 * Method to list the current list of files that are being use to search into.
	 */
	public function &listSearchFiles() {

		$returnArray= array();
		foreach($this->fileMap as $fileID => $fileName) $returnArray[]= $fileName;

		return $returnArray;
	}

	/**
	 * Method used to setup the internal in-memory map of words for all files.
	 * This architecture consists on an associative array, where the position is defined
	 * by the word itself, and the row contains the file IDs where is found.
	 */
	protected function createTextMap() {

		$start=debugStartProcess("* Loading files contents and creating the text map.");

		$this->contentMap= array();

		/**
		 * This loop will repeat for each file:
		 * - Get file contents and replace defined characters for word separator.
		 * - Extract all words by exploding the content using the WORD_SEPARATOR param as field separator.
		 * - For each word insert the current File ID into the contentMap array using the word as key.
		 *   Also do the analogue for lower case word into the contentMapLower.
		 */

		$fileID=0;          // # File counter
		foreach($this->fileMap as $fileName) {
			$content= file_get_contents($fileName);

			// Remove trailing spaces, double spaces, dot, comma
			$processedContent= $this->preProcessContent($content);

			// Now get the array of words, calculated
			$content_words= explode(WORD_SEPARATOR, $processedContent);

			$word_position= 0;
			foreach($content_words as $word) {
				// The content map is created in lower case if SEARCH_IS_KEY_INSENSITIVE is set to false.
				if(SEARCH_IS_KEY_INSENSITIVE) $word= strtolower($word);

				// Store the word, file and position, in a 3D array.
				// If does not exists the word yet, reserve the key and prepare as an array
				if(!array_key_exists($word, $this->contentMap)) $this->contentMap[$word]= array();
				if(!array_key_exists($fileID, $this->contentMap[$word])) $this->contentMap[$word][$fileID]= array();

				// Here we are, store the word position into the fileID for current word.
				switch(SEARCH_ENGINE) {
					case "simple":
						break;
					case "strict":
						$this->contentMap[$word][$fileID][] = $word_position;
						break;
				}

				$word_position++;
			}

			$fileID++;      // This file ID has finished. Lets work on next...
		}

		debugEndProcess("Map created.",$start);
		debugShowMessage("Word count: " . count($this->contentMap) . " words");
	}

	/**
	 * Method to remove the prohibited chars defined at the config file.
	 * @param $content
	 * @return mixed|string
	 */
	private function preProcessContent($content) {
		global $charsToReplace;

		// Replace each position of the array (defined at config file) for the associated value:
		$newString= "";
		foreach($charsToReplace as $search => $replace) {
			$newString= str_replace($search, $replace, $content);
		}

		return $newString;
	}

	/**
	 * Method to compares two words.
	 *
	 * This is not part of the algorithm decision but is delivered as public so other processes can check using
	 * the same comparison alg.
	 *
	 * Return a float for the ranking hit defined in the config file, depending exactly equal or equal in lower case.
	 *
	 * @param $word1
	 * @param $word2
	 * @return boolean
	 */
	public function compareWords($word1, $word2) {

		if(SEARCH_IS_KEY_INSENSITIVE) {
			$word1= strtolower($word1);
			$word2= strtolower($word2);
		}

		return ($word1 == $word2);
	}

	/**
	 * Method that return an array containing the file IDs that matched the word. Each array position is itself another
	 * array containing the positions where the words where found.
	 * In other words: hits[fileIDs][positions]
	 * @param $word
	 * @return array
	 */
	protected function &searchWord($word) {

		$hits= array();

		if(SEARCH_IS_KEY_INSENSITIVE) $word= strtolower($word);

		// First, search the key into the contentMap array.
		// If found, get the File IDs.
		if(array_key_exists($word, $this->contentMap)) return $this->contentMap[$word];

		return $hits;
	}

	/**
	 * Method for rank the hit conditions.
	 * Returns an associative array in the form ranking[fileID => %hits]
	 *
	 * @param $hitsArray , in the form: hitsArray[fileIDs][positions]
	 * @param $wordsArray
	 * @return array
	 */
	protected function &rankHits($hitsArray, $wordsArray) {

		$ranking= array();

		$wordCount= count($wordsArray);
		if($wordCount == 0) return $ranking;            // If void, return empty array.

		$hitIncrement= 100/$wordCount;                  // Amount of increment for each word found

		foreach($this->fileMap as $fileID => $fileName) {

			debugStartProcess("get string depth for $fileID");
			$currentRanking= $this->getStringDepth($hitsArray, $fileID);
			debugEndProcess();
			// Save only if something has been found
			if($currentRanking > 0) $ranking[$fileName]= round($currentRanking * $hitIncrement,1);
		}

		return $ranking;
	}

	/**
	 * Auxiliar method to make the first call to the recursive method "getStringDepth".
	 *
	 * The positionArray parameter is an array of arrays, where the base array represents for each position one word
	 * searched, and its content is an array with the positions where where found. [index][positions]
	 * @param $positionArray
	 * @param $fileID
	 * @return int
	 */
	private function getStringDepth($positionArray, $fileID) {
		$this->iteration=0;

		switch(SEARCH_ENGINE) {
			case "simple":
				return $this->getStringDepthRecursiveSimple($positionArray, $fileID);
			case "strict":
				return $this->getStringDepthRecursiveStrict($positionArray, $fileID) / STRICT_HIT_RANKING;
		}
	}

	/**
	 * Recursive method that returns the number of matching words for a file ID.
	 *
	 * The positionArray parameter is an array of arrays, where the base array represents for each position one word
	 * searched, and its content is an array with the positions where where found. [index][positions]
	 * @param $positionArray
	 * @param $fileID
	 * @param $currentIndex
	 * @return int
	 */
	private function getStringDepthRecursiveSimple($positionArray, $fileID, $currentIndex=0) {

		// Check end of recursive: If the the end of the chain has been reached, return back
		if($currentIndex == count($positionArray)) return 0;

		// Check if in the current position of the text to search (current word), there are hits for the file ID
		// If not, continue to next position in array.

		$hit= array_key_exists($fileID, $positionArray[$currentIndex]);
		return $this->getStringDepthRecursiveSimple($positionArray, $fileID, $currentIndex + 1) + $hit;
	}

	/**
	 * Recursive method that returns the longest path of CONSECUTIVE WORDS.
	 * On each iteration try to find in the future (rest of the chain) the longest consecutive string.
	 *
	 * The positionArray parameter is an array of arrays, where the base array represents for each position one word
	 * searched, and its content is an array with the positions where where found. [index][positions]
	 * @param $positionArray
	 * @param $fileID
	 * @param $positionToSearch
	 * @param $currentIndex
	 * @return int
	 */
	private function getStringDepthRecursiveStrict($positionArray, $fileID, $positionToSearch=false, $currentIndex=0) {

		$iteration= $this->iteration;
		$this->iteration++;

		$chainLength= 0;

		if($currentIndex == count($positionArray)) {
			debugShowMessage("[# $iteration] [START] FileID: $fileID, Iteration: $iteration, Index: $currentIndex --> Reached end of array. Return");
			return 0;
		}
		debugShowMessage("[# $iteration] [START] FileID: $fileID, Iteration: $iteration, Index: $currentIndex, Position to search $positionToSearch");

		// Check end of recursive: If the the end of the chain has been reached, return back

		// If current file does not hit current word position, then continue to next word.
		if(!array_key_exists($fileID, $positionArray[$currentIndex])) {
			// In this case the positionToSearch is "false" again, that means that the search is restarted.
			return $this->getStringDepthRecursiveStrict($positionArray, $fileID, false, $currentIndex + 1);
		}

		// Is that the first?
		if( ($positionToSearch === false) ) {
			// Is the first word, and as the control flow, is found, so hit is directly the configured HIT points.
			$hit= ($currentIndex == 0) ? STRICT_HIT_RANKING : 1;

			// After that, continue exploring from the beginning;
			foreach ($positionArray[$currentIndex][$fileID] as $position) {
				$futureLength = $this->getStringDepthRecursiveStrict($positionArray, $fileID, $position + 1, $currentIndex + 1);
				if ($position == ($positionToSearch)) $futureLength++;
				$chainLength = max($chainLength, $futureLength);
				// If it matched the maximum number of words, no needs to continue searching... it has been found:
				if($chainLength == count($positionArray)) break;
			}

			$chainLength+=$hit;

		} else {
			// Ok, current file has hits.
			// Does it contains the next char of the chain?
			$hit = in_array($positionToSearch, $positionArray[$currentIndex][$fileID]) ? STRICT_HIT_RANKING : 0;
			// Yes, is consecutive. Then multiply per factor:
			$chainLength = $this->getStringDepthRecursiveStrict($positionArray, $fileID, $positionToSearch + 1, $currentIndex + 1) + $hit;
		}

		// TODO: In strict mode, if the words are not consecutive, will not rank anything. Decide if can rank, for example, using the distance.
		debugShowMessage("[# $iteration] [END]   FileID: $fileID, Iteration: $iteration, Length: $chainLength");

		return $chainLength;
	}
}
