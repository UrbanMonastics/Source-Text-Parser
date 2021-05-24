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

use UrbanMonastics\SourceParser\Models\Text as Text;
use UrbanMonastics\SourceParser\Models\Version as Version;
use UrbanMonastics\SourceParser\SourceText as SourceText;

class Texts{
	// Manage the location, and loading of the various files from the location
	protected $ActiveTexts;
	protected $TextPath;
	protected $SourceFilePath;		// If we find a source.json when indexing we store the path here
	protected $HasVersionFolders;
	protected $VersionFolders = array();	// Which folder abbreviations do we have
	protected $PathContents = array();

	/* -- Reference to Parent -- */
	private $SourceText;
	private $ActiveVersion;

	function __construct( SourceText &$SourceText, Version $Version = NULL ){
		$this->SourceText = $SourceText;

		if( !is_null( $Version ) ){
			$this->ActiveVersion = $Version;
		}
	}


	public function getSourceFilePath(){
		return $this->SourceFilePath;
	}

	public function listTexts(){
		
	}

	public function listVersions(){
		return $this->VersionFolders;
	}

	public function setTextsPath( String $PathToTexts, Bool $HasVersionFolders = false ){
		// ensure that the path exists, and we can reach it.
		if( !is_string( $PathToTexts ) || is_null( $PathToTexts ) || !file_exists( $PathToTexts ) ){
			return $this;
		}

		$this->TextPath = $PathToTexts;

		// Mark if that path has folders for versions or not.
		$this->HasVersionFolders = $HasVersionFolders;

		// Look to see what is in the folder
		$this->indexPath();

		return $this;
	}

	private function indexPath(){
		$firstLevel = scandir( $this->TextPath );

		foreach( $firstLevel as $v ){
			if( in_array($v, array('.', '..', '.DS_Store') ) ){
				continue;
			}


			if( $this->HasVersionFolders ){
				if( is_dir( $this->TextPath . '/' . $v ) ){
					$this->VersionFolders[] = $v;
					$this->PathContents[$v] = array();

					$versionLevel = scandir( $this->TextPath . '/' . $v );
					foreach( $versionLevel as $f ){
						if( in_array($f, array('.', '..', '.DS_Store') ) ){
							continue;
						}

						if( is_dir( $this->TextPath . '/' . $v . '/' . $f ) ){
							// There should never be directories at this level.
							continue;
						}

						$this->PathContents[$v][] = $f;
					}
				}else{
					if( $v == 'source.json' ){	$this->SourceFilePath = $this->TextPath . '/' . $v;	}

					$this->PathContents[] = $v;
				}
			}else{
				if( $v == 'source.json' ){	$this->SourceFilePath = $this->TextPath . '/' . $v;	}

				$this->PathContents[] = $v;
			}
		}


		var_dump( $this->VersionFolders, $this->PathContents );
		return $this;
	}



	public function reset(){
		$this->ActiveTexts = null;
		$this->TextPath = null;
		$this->SourceFilePath = null;
		$this->HasVersionFolders = null;
		$this->VersionFolders = array();
		$this->PathContents = array();
		
		return $this;
	}
}