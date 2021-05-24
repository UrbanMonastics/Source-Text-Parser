<?php

/***********
 *	
 *	SourceParser
 *	https://developers.urbanmonastic.org/
 *	
 *	© Paul Prins
 *	https://paulprins.net https://paul.build/
 *	
 *	Licensed under MIT - For full license, view the LICENSE distributed with this source.
 *	
 ***********/

namespace UrbanMonastics\SourceParser\Controllers;

use UrbanMonastics\SourceParser\SourceText as SourceText;

class Index{
	// Manage the location, and loading of the various files from the location
	protected $VersionFolders = array();	// Which folder abbreviations do we have
	protected $PathContents = array();
	protected $index;

	/* -- Reference to Parent -- */
	private $SourceText;
	private $ActiveVersion;

	function __construct( SourceText &$SourceText ){
		$this->SourceText = $SourceText;
	}


	public function getIssues(){
		if( !is_array( $this->index ) || !array_key_exists( 'Issues', $this->index ) )
			return array("There is no index");

		return $this->index['Issues'];
	}


	public function loadLibrary( string $libraryPath ){
		if( !is_string( $libraryPath ) || is_null( $libraryPath ) || !file_exists( $libraryPath ) ){
			throw new \Exception('loadLibrary - Supplied path is not a valid library location');
		}

		if( is_null( $this->index ) ){
			$this->index = array();
			$this->index['Version'] = SourceText::version;
			$this->index['Sources'] = array();
			// ['Abbreviation'] = array('Path' => PathToSourceDirectory, 'Abbreviation', 'Title', 'Type' => null, 'SecondaryType' => null, 'Segments' => , 'Verses', '' )
			$this->index['SourceVersions'] = array();
			$this->index['Issues'] = array();	// Just a list of issues
		}


		$libraryPath = rtrim( $libraryPath, '/' ) . '/';

		$LibraryLevel = scandir( $libraryPath );
		foreach( $LibraryLevel as $TextAbbrv ){
			$TextPath = $libraryPath .  $TextAbbrv . '/';

			if( !is_dir( $TextPath ) || in_array($TextAbbrv, array('.', '..', '.DS_Store') ) ){
				continue;
			}

			// Check and ensure that this directory has a source.json file (or that one already has been indexed).
			if( !file_exists( $TextPath . 'source.json' ) && !array_key_exists( $TextAbbrv, $this->index['Sources'] ) ){
				throw new \Exception('loadLibrary - Directory is missing the required source.json file: ' . $TextPath);
			}


			if( file_exists( $TextPath . 'source.json' ) ){
				$ThisSource = $this->SourceText->_loadJson( $TextPath . 'source.json' );

				if( empty( $ThisSource ) ){
					throw new \Exception('loadLibrary - Malformed source file found at: \'' . $TextPath . 'source.json\'' );
				}

				if( $TextAbbrv != $ThisSource['Abbreviation'] ){
					$this->index['Issues'][] = "Mismatched abbreviations between folder name and source.json: " . $TextPath;
				}



				$this->index['Sources'][$TextAbbrv] = array('Path' => $TextPath, 
							'SourcePath' => $TextPath . 'source.json',
							'Abbreviation' => $TextAbbrv,
							'Title' => array(),
							'Type' => null,
							'SecondaryType' => null,
							'Segments' => 'None',
							'Verses' => false,
							'SegmentTitles' => false,
							'Versions' => array(),
							'VerseCounts' => NULL );

				if( array_key_exists( 'Title', $ThisSource ) ){
					$this->index['Sources'][$TextAbbrv]['Title'] = (array) $ThisSource['Title'];
				}else{
					$this->index['Issues'][] = "Source is missing its Title definition: " . $TextPath . "source.json";
				}


				$allowedSourceTypes = $this->SourceText->getAllowedSourceTypes();
				$allowedSourceSegments = $this->SourceText->getAllowedSourceSegments();
				if( array_key_exists( 'Type', $ThisSource ) && in_array( $ThisSource['Type'], $allowedSourceTypes ) ){
					$this->index['Sources'][$TextAbbrv]['Type'] = (string) $ThisSource['Type'];
				}else{
					$this->index['Issues'][] = "Source has an invalid Type definition: " . $TextPath . "source.json";
				}

				if( array_key_exists( 'SecondaryType', $ThisSource ) && !is_null( $ThisSource['SecondaryType'] ) ){
					$this->index['Sources'][$TextAbbrv]['SecondaryType'] = (string) $ThisSource['SecondaryType'];
				}

				if( array_key_exists( 'Segments', $ThisSource ) && in_array( $ThisSource['Segments'], $allowedSourceSegments ) ){
					$this->index['Sources'][$TextAbbrv]['Segments'] = (string) $ThisSource['Segments'];
				}else{
					$this->index['Issues'][] = "Source has an invalid Segments definition: " . $TextPath . "source.json";
				}


				if( array_key_exists( 'Verses', $ThisSource ) ){
					if( is_bool( $ThisSource['Verses'] ) ){
						$this->index['Sources'][$TextAbbrv]['Verses'] = (bool) $ThisSource['Verses'];

						if( $ThisSource['Verses'] ){
							$this->index['Sources'][$TextAbbrv]['VerseCounts'] = array();
						}
					}else{
						$this->index['Issues'][] = "Source has an invalid Verses definition: " . $TextPath . "source.json";
					}
				}

				if( array_key_exists( 'SegmentTitles', $ThisSource ) ){
					if( is_bool( $ThisSource['SegmentTitles'] ) ){
						$this->index['Sources'][$TextAbbrv]['SegmentTitles'] = (bool) $ThisSource['SegmentTitles'];
					}else{
						$this->index['Issues'][] = "Source has an invalid SegmentTitles definition: " . $TextPath . "source.json";
					}
				}
			}




			//
			//
			// Lets index the version directories
			$TextLevel = scandir( $TextPath );
			foreach( $TextLevel as $VersionAbbrv ){
				$VersionPath = $TextPath .  $VersionAbbrv . '/';
				if( !is_dir( $VersionPath ) || in_array($VersionAbbrv, array('.', '..', '.DS_Store') ) ){
					continue;
				}

				if( !$this->SourceText->checkVersion( $VersionAbbrv ) ){
					$Issue = "Unknown version found when scanning directories: " . $VersionAbbrv;
					if( !in_array( $Issue, $this->index['Issues'] ) ){
						$this->index['Issues'][] = $Issue;
					}
					continue;	// This version isn't registered, so lets skip it
				}


				if( !array_key_exists($VersionAbbrv, $this->index['SourceVersions'] ) ){
					$this->index['SourceVersions'][$VersionAbbrv] = array();
				}

				if( !in_array($TextAbbrv, $this->index['SourceVersions'][$VersionAbbrv] ) ){
					$this->index['SourceVersions'][$VersionAbbrv][] = $TextAbbrv;
				}

				$this->index['SourceVersions'][$VersionAbbrv][] = $TextAbbrv;

				$this->index['Sources'][$TextAbbrv]['Versions'][$VersionAbbrv] = array('Path' => $VersionPath,
						'Text' => NULL,
						'Chapters' => NULL,
						'Abbreviations' => NULL );
				switch( $this->index['Sources'][$TextAbbrv]['Segments'] ){
					case 'None':
						$this->index['Sources'][$TextAbbrv]['Versions'][$VersionAbbrv]['Text'] = array();
						break;
					case 'Chapters':
						$this->index['Sources'][$TextAbbrv]['Versions'][$VersionAbbrv]['Chapters'] = array();
						break;
					case 'Abbreviations':
						$this->index['Sources'][$TextAbbrv]['Versions'][$VersionAbbrv]['Abbreviations'] = array();
						break;
				}


				// Sort the Array to ensure it is nicer to read
				ksort( $this->index['Sources'][$TextAbbrv]['Versions'] );



				$VersionLevel = scandir( $VersionPath );
				foreach( $VersionLevel as $FileName ){
					$FilePath = $VersionPath .  $FileName;
					if( !is_file( $FilePath ) || in_array( $FileName, array('.', '..', '.DS_Store', '_template.json') ) ){
						continue;
					}

					// Load the file contents
					$FileContents = $this->SourceText->_loadJson( $FilePath );
					if( !is_array( $FileContents ) ){
						$this->index['Issues'][] = "Text file is not valid JSON and cannot be loaded: " . $FilePath;
						continue;
					}


					//
					// Lets check for some common issues
					if( !array_key_exists( 'TextAbbreviation', $FileContents ) ){
						$this->index['Issues'][] = "Text file does not have a TextAbbreviation value: " . $FilePath;
					}else if( $FileContents['TextAbbreviation'] !== str_replace('.json', '', $FileName ) ){
						$this->index['Issues'][] = "Text file value for TextAbbreviation does not match the filename: '" . $FileContents['TextAbbreviation'] . "' should match '". str_replace('.json', '', $FileName )  . "': " . $FilePath;
					}

					if( !array_key_exists( 'SourceAbbreviation', $FileContents ) ){
						$this->index['Issues'][] = "Text file does not have a SourceAbbreviation value: " . $FilePath;
					}else if( $TextAbbrv !== $FileContents['SourceAbbreviation'] ){
						$this->index['Issues'][] = "Text file value for SourceAbbreviation does not match the parent directory: '" . $FileContents['SourceAbbreviation'] . "' should match '". $TextAbbrv . "': " . $FilePath;
						
					}

					if( $this->index['Sources'][$TextAbbrv]['SegmentTitles'] == false && !empty( $FileContents['Title'] ) ){
						$this->index['Issues'][] = "Text file has a Title value when the source does not support this: " . $FilePath;
					}
					// Lets check for some common issues
					//



					$TextIndex = array('TextAbbreviation' => $FileContents['TextAbbreviation'],
						'FilePath' => $FilePath,
						'HasContent' => false);



					// Check if it is stored in Text or Verses
					$TheseVerses = NULL;
					$TextIndex['Verses'] = NULL;
					if( $this->index['Sources'][$TextAbbrv]['Verses'] ){
						if( array_key_exists('Verses', $FileContents ) ){
							$TextIndex['Verses'] = array();
							foreach( $FileContents['Verses'] as $v => $c ){
								if( strlen( trim( $c ) ) > 0 )
									$TextIndex['Verses'][] = (int) $v;	// Only list non-empty verses
							}

							$Max = 0;
							if( !empty( $TextIndex['Verses'] ) ){
								$Max = max( $TextIndex['Verses'] );
							}

							if( !array_key_exists( $FileContents['TextAbbreviation'], $this->index['Sources'][$TextAbbrv]['VerseCounts'] ) || $Max > $this->index['Sources'][$TextAbbrv]['VerseCounts'][$FileContents['TextAbbreviation']] ){
								if( !empty( $TextIndex['Verses'] ) ){
									$this->index['Sources'][$TextAbbrv]['VerseCounts'][$FileContents['TextAbbreviation']] = max( $TextIndex['Verses'] );
								}else{
									$this->index['Sources'][$TextAbbrv]['VerseCounts'][$FileContents['TextAbbreviation']] = 0;
								}
							}

							ksort( $this->index['Sources'][$TextAbbrv]['VerseCounts'] );
						}

						if( !empty( $TextIndex['Verses'] ) )
							$TextIndex['HasContent'] = true;
					}else{
						if( !empty( $FileContents['Text'] ) )
							$TextIndex['HasContent'] = true;
					}


					switch( $this->index['Sources'][$TextAbbrv]['Segments'] ){
						case 'None':
							$this->index['Sources'][$TextAbbrv]['Versions'][$VersionAbbrv]['Text'] = $TextIndex;
							break;

						case 'Chapters':
							$ChapterNumber = ltrim( str_ireplace( array('chapter-', '.json'), array('', ''), $FileName ), '0' );
							$this->index['Sources'][$TextAbbrv]['Versions'][$VersionAbbrv]['Chapters'][$ChapterNumber] = $TextIndex; 

							ksort( $this->index['Sources'][$TextAbbrv]['Versions'][$VersionAbbrv]['Chapters'] );
							break;

						case 'Abbreviations':
							$ThisAbbrv = str_ireplace( '.json', '', $FileName );
							$this->index['Sources'][$TextAbbrv]['Versions'][$VersionAbbrv]['Abbreviations'][$ThisAbbrv] = $TextIndex;

							ksort( $this->index['Sources'][$TextAbbrv]['Versions'][$VersionAbbrv]['Abbreviations'] );
							break;
					}
				}

				
			}
			// Lets index the version directories



		}


		//
		// Lets do some quick source checking
		foreach( $this->index as $TextAbrv => $Source ){
			// if( ){
			// 	$this->index['Issues'][] = "Text file has a Title value when the source does not support this: " . $FilePath;
			// }
		}



		var_dump( $this->index['Issues'] );
		return $this;
	}
	
	public function exportIndex(){
		$tmp = $this->index;
		$tmp['VerificationKey'] = md5( json_encode( $this->index, JSON_UNESCAPED_UNICODE ) );

		return $tmp;
	}
	public function importIndex( array $ExportedIndex ){
		//
		// First Check - ensure that the Version numbers match
		if( !array_key_exists('Version', $ExportedIndex ) || $ExportedIndex['Version'] != SourceText::version ){
			throw new \Exception('Unable to import the Source Text index. The Version value does not match the library, or is missing');
		}
		// First Check - Passed
		//

		//
		// Second Check - ensure that the MD5 Hash sum matches
		if( !array_key_exists('VerificationKey', $ExportedIndex ) ){
			throw new \Exception('Unable to import the Source Text index. The VerificationKey is missing from the index.');
		}

		$VerificationKey = $ExportedIndex['VerificationKey'];
		unset($ExportedIndex['VerificationKey'] );
		if( $VerificationKey !== md5( json_encode( $ExportedIndex, JSON_UNESCAPED_UNICODE ) )){
			throw new \Exception('Unable to import the Source Text index. The VerificationKey does not match the supplied index.');
		}
		// Second Check - Passed
		//


		$this->index = $ExportedIndex;

		var_dump("It was verified");
		return true;
	}


	public function clear(){
		
		return $this;
	}
}