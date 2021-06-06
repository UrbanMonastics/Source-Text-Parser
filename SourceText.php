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

namespace UrbanMonastics\SourceParser;

use UrbanMonastics\SourceParser\SourceParser as SourceParser;
use UrbanMonastics\SourceParser\Controllers\Index as IndexController;
use UrbanMonastics\SourceParser\Controllers\Languages as LanguageController;
use UrbanMonastics\SourceParser\Controllers\Texts as TextController;
use UrbanMonastics\SourceParser\Controllers\Versions as VersionController;
use UrbanMonastics\SourceParser\Models\Reference as Reference;
use UrbanMonastics\SourceParser\Models\Source as Source;
use UrbanMonastics\SourceParser\Models\Version as Version;
use UrbanMonastics\SourceParser\Parsing\Reference as ReferenceParsing;


class SourceText extends SourceParser{
	/* -- Variables for Source Texts -- */
	protected $Source;		// This is the source.json file as an Object
	protected $Reference;		// What portion (Chapter, Chapter/Verse, Abbv) are we parsing
	protected $sourceText;	// This is to store the source text before we use it.
	protected $allowedSourceTypes = array( 'Book', 'Letter', 'Dictionary');
	protected $allowedSourceSegments = array( 'Chapters', 'Abbreviations', 'None');

	/* -- Store Controllers -- */
	protected $IndexController;
	protected $LanguagesController;
	protected $VersionsController;
	protected $TextsController;

	function __construct( array $Source = array() ){
		// Create Controllers
		$this->IndexController = new IndexController( $this );
		$this->VersionsController = new VersionController( $this );
		$this->TextsController = new TextController( $this );
		$this->LanguagesController = new LanguageController( $this );
		

		if( !empty( $Source ) ){
			$this->setSource( $Source );
		}
	}


	/**
	 * Load a local JSON resource and respond with an associative array.
	 *
	 * @param  string		$PathToTexts	the path to the directory holding the actual texts (absolute paths preferred)
	 * @return array
	 */
	function _loadJson( string $PathToFile ){
		try{
			if( !file_exists( $PathToFile ) ){
				return false;	// This book/text does not exist
			}
			$Response = json_decode( file_get_contents($PathToFile), true );
		}catch( Exception $e){
			echo $e->getMessage();
			exit(1);
		}

		return $Response;
	}



	/* -- LanguageController Passthrough Functions -- */
	public function addLanguage( string $Abbreviation, array $RawVersion ){
		$newVersion = new Version( $this, $RawVersion );	// Convert the Raw version data into a version model.
		$this->LanguagesController->addLanguage( $Abbreviation, $newVersion );

		return $this;
	}
	public function getFamily( string $FamilyName = NULL ){
		return $this->LanguagesController->getFamily( $FamilyName );
	}
	public function getFamilies(){
		return $this->LanguagesController->getFamily();
	}
	public function getLanguage( string $Abbreviation = NULL ){
		return $this->LanguagesController->getLanguage( $Abbreviation );
	}
	public function getLanguages(){
		return $this->LanguagesController->getLanguages();
	}
	public function loadLanguages( string $PathToLanguagesFile ){
		$this->LanguagesController->loadLanguages( $PathToLanguagesFile );

		return $this;
	}
	/* -- END: LanguageController Passthrough Functions -- */



	/* -- TextController Passthrough Functions -- */
	public function setTextsPath( String $PathToTexts, Bool $HasVersionFolders = false ){
		$this->TextsController->setTextsPath( $PathToTexts, $HasVersionFolders );

		return $this;
	}
	/* -- END: TextController Passthrough Functions -- */


	/* -- VersionController Passthrough Functions -- */ 
	public function addVersion( string $Abbreviation, array $RawVersion ){
		$newVersion = new Version( $this, $RawVersion );	// Convert the Raw version data into a version model.
		$this->VersionsController->addVersions( $Abbreviation, $newVersion );

		return $this;
	}
	public function checkVersion( string $Abbreviation = NULL ){
		return $this->VersionsController->checkVersion( $Abbreviation );
	}
	public function getVersion( string $Abbreviation = NULL ){
		return $this->VersionsController->getVersion( $Abbreviation );
	}
	public function getVersions(){
		return $this->VersionsController->getVersions();
	}
	public function getVersionsByLanguage( string $LanguageFamily = NULL ){
		return $this->VersionsController->getByLanguageFamily( $LanguageFamily );
	}
	public function loadVersionLanguages(){
		$this->VersionsController->loadLanguages();

		return $this;
	}
	public function loadVersions( string $PathToVersionsFile ){
		$this->VersionsController->loadVersions( $PathToVersionsFile );

		return $this;
	}
	public function setVersion( string $VersionAbbreviation){
		$this->VersionsController->setVersion( $VersionAbbreviation );
		
		return $this;
	}
	/* -- END: VersionController Passthrough Functions -- */ 


	/* -- IndexController Passthrough Functions -- */ 
	public function indexLoadLibrary( string $libraryPath ){
		return $this->IndexController->loadLibrary( $libraryPath );
	}
	public function indexImport( array $ExportedIndex ){
		return $this->IndexController->importIndex( $ExportedIndex );
	}
	public function indexExport(){
		// This will export out the current Index so it can be cached for later use
		return $this->IndexController->exportIndex();
	}
	public function indexGetIssues(){
		// Return the array of issues found when building the current Index
		return $this->IndexController->getIssues();
	}
	/* -- END: IndexController Passthrough Functions -- */ 




	public function autoloadSource(){
		if( is_null( $this->TextsController->getSourceFilePath() ) ){
			var_dump('Fail 1');
			return false;
		}

		try{
			$Source = $this->_loadJson( $this->TextsController->getSourceFilePath() );
			var_dump( $Source );
		}catch( Exception $e){
			var_dump('Fail 2');
			return false; // This is fine to fail.
		}

		$this->setSource( $Source );
		
		return true;
	}

	/**
	 * Set the Source details from a relevant source-texts source. You can overload it to simplify the steps involved.
	 *
	 * @param  array		$Source	array of lines that need to be processed
	 * @param  string		$PathToTexts	the path to the directory holding the actual texts (absolute paths preferred)
	 * @param  array		$Version	array of the version being fetched
	 * @return this
	 */
	public function setSource( array $Source, string $PathToTexts = null, array $Version = null ){
		if( is_array( $Source ) )
			$this->Source = new Source( $Source );

		// Are we speeding up the process
		if( !is_null( $PathToTexts ) && is_string( $PathToTexts ) ){
			$this->setTextsPath( $PathToTexts );
		}

		if( is_array( $Version ) ){
			$this->Version = new Version( $this, $Version );
		}

		// var_dump( 'Small Caps Now:', $this->smallCapsText, $this->selahHTML, $this->Version->getLicense() );

		return $this;
	}




	/*
	 * Source Text Setters
	 */
	public function setReference(string $Reference, bool $getReference = false){
		$this->textReference = null;	// Clearn any cached reference

		// Before we can set a reference we must have the TextsPath, and the Source information defined so we know how to look for files in that directory
		if( !is_a( $this->Source, 'UrbanMonastics\SourceParser\Models\Source' ) ){
			return false;	// We can only set references if we know how the source is structured
		}
		$Reference = trim( $Reference );

		$this->Reference = new ReferenceParsing( $Reference, $this->Source );
		var_dump( $this->Reference );

		// if( $this->Source->getSegments() === true ){
		// 	$Store = array( $Reference ); // This reference can really be anything since it's in segments.
		// }else if( $this->Source->getChapters() === true && $this->Source->getVerses()  === true ){
		// 	// This reference is stored as a key => array(); An empty array means the whole chapter
		// 	// We need to expect ranges here, and commas, it's gonna be a shit show
		// 	$Store = array();
		// 	$tmp = new ReferenceParsing( $Reference, $this->Source );
		// 	// var_dump( $tmp );
		// }else if( $this->Source->getChapters() === true ){
		// 	$Store = array();
		// }else if( $this->Source->getVerses() === true ){
		// 	$Store = array();
		// 	$tmp = explode( ',', $Reference );
		// 	foreach( $tmp as $v ){
		// 		$v = trim( $v );
		// 		if( strpos( $v, '-' ) ){
		//
		// 		}else{
		// 			$Store[] = $v;
		// 		}
		// 	}
		//
		// }else
		// 	$Store = NULL;


		$this->textReference = $Store;

		if( $getReference === true ){
			return $this->getReference( true );
		}

		return $this;
	}

	private function fetchReference(){
		// Get the reference and prepare to style it

		$ProcessingText = NULL;

		// Title
		// Chapter
		// Description
		// FormatAs
		// InlineTitles
		// Footnotes
		$Filename = 'text.json';
		$Filename = 'chapter-0001.json';
		$Filename = 'SEGMENT.json';




		// TODO: Add support for Footnotes and InlineTitles
		
		// Text
		// Verses


		$this->sourceText = NULL;
	}

	public function getReference( bool $fetchFirst = false ){
		if( $fetchFirst )
			$this->fetchReference();	// to cache the text

		return $this->text( $this->sourceText );
	}



	/**
	 * Get the value of the allowedSourceSegments array stored in the SourceText class
	 *
	 * @return array
	 */
	public function getAllowedSourceSegments(){
		return $this->allowedSourceSegments;
	}

	/**
	 * Get the value of the allowedSourceTypes array stored in the SourceText class
	 *
	 * @return array
	 */
	public function getAllowedSourceTypes(){
		return $this->allowedSourceTypes;
	}


	/**
	 * Clear out all of the source information, and update the options to match.
	 *
	 * @param  array		$lines	array of lines that need to be processed
	 * @return array
	 */
	public function resetSource(){
		$this->sourceObject = null;
		$this->versionObject = null;
		$this->TextsController->reset();

		$this->setBreaksEnabled( false );
		$this->setPreserveIndentations( false );

		// These are defined when the version is set. So reset them to their default [false]
		$this->setSelahHTML( false );
		$this->setSmallCapsText( false );
	}
	/*
	 * END: Source Text Setters
	 */
}