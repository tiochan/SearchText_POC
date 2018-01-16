# SearchText_POC
--------------------------------------------------
                Introduction
--------------------------------------------------

This is a search engine class for look for text into text files.

The study of algorithms to search text into files is a deep study, with many complex solutions
delivered, using many kinds of data structure, like trees, which are not complex to implement,
but are complex to manage. Those solutions allow algs to reach the needed performance to make
suitable the text search. In the current exercise, I will use a simple data structure and a
simple way to determine the matching process, facing a coding example.

The simple usage is to instance the class passing as argument the directory where to find for.
Then you can call the searchText function which will return the list of files matching the
text that you passed as parameter with a match percentage.

This match % means:
- 100%: the file contains all the words to search
- 0% means that the file does not contains any word (this case will not be listed).
- Between 100% and 0%, the match result is based on the hits.


--------------------------------------------------
                  Algorithm
--------------------------------------------------

The algorithm take it basis on how to construct the file map. The class will read every file
and construct an associative 3D array. 3D means that it has 3 subsets of arrays.

             words --> [ word ] [ file ID ] [ positions , ... ]

- The first level contains all the DIFFERENT words on all files. Lower case or upper case depends
on the configuration parameter SEARCH_IS_KEY_INSENSITIVE. For each word:
- The second level contains the IDs of the files that contains each word. For each file ID:
- The third level contains all the positions into the file where the word has been found.

Using the positions of a word across each file is easy to determine the hit %.


--------------------------------------------------
               Considerations
--------------------------------------------------

- A word is a set of characters limited by those characters:
   * A blank space
   * Usual word separators ('.', ',', ';')
   * End of line
   * End of file


--------------------------------------------------
               Unitary tests
--------------------------------------------------

The unitary tests are implemented with PHPUnit.

In order to install this software, follow those steps:

             wget https://phar.phpunit.de/phpunit-6.5.phar
             chmod +x phpunit-6.5.phar
             sudo mv phpunit-6.5.phar /usr/local/bin/phpunit
             phpunit --version
             sudo apt-get install php
             phpunit --version

To test the application, just execute:

		php search_text_engine.php <path_to_files>



