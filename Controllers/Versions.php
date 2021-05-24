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
use UrbanMonastics\SourceParser\Models\Version as Version;


class Versions{
	// Load up and manage all the versions
	private $AllVersions = array();
	private $CurrentVersion;
	private $LanguagesLoaded = false;


	/* -- Reference to Parent -- */
	private $SourceText;


	function __construct( SourceText &$SourceText ){
		$this->SourceText = $SourceText;
	}


	/**
	 * Set the Source details from a relevant source-texts source. You can overload it to simplify the steps involved.
	 *
	 * @param  array		$Source	array of lines that need to be processed
	 * @param  string		$PathToTexts	the path to the directory holding the actual texts (absolute paths preferred)
	 * @param  array		$Version	array of the version being fetched
	 * @return this
	 */
	public function addVersion( string $Abbreviation, Version $newVersion ){
		$this->AllVersions[$Abbreviation] = $newVersion;
		$this->LanguagesLoaded = false;	// Reset this flag

		return $this;
	}


	/**
	 * Return all of the stored versions listed alphabeticaly by their language family
	 *
	 * @return array
	 */
	public function getByLanguageFamily( string $LanguageFamily = NULL ){
		if( $this->LanguagesLoaded )
			$this->loadLanguages();

		$Response = array();
		foreach( $this->AllVersions as $abv => $v ){
			if( is_null( $v->getLanguage() ) ){
				continue;	// There is not a defined language for this version
			}

			if( !is_null( $LanguageFamily ) && $LanguageFamily != $v->getLanguage()->getFamily() ){
				continue;
			}

			if( !array_key_exists( $v->getLanguage()->getFamily(), $Response ) ){
				$Response[$v->getLanguage()->getFamily()] = array();
			}

			$Response[$v->getLanguage()->getFamily()][$v->getAbbreviation()] = $v->getTitle();

			asort( $Response[$v->getLanguage()->getFamily()] );
		}
		ksort( $Response );

		// Replace the version Titles with the actual versions
		foreach( $Response as $family => $vs ){
			foreach( $vs as $abv => $t ){
				$Response[$family][$abv] = $this->AllVersions[$abv];
			}
		}


		if( !is_null( $LanguageFamily ) ){
			if( !array_key_exists( $LanguageFamily, $Response ) || empty( $Response[$LanguageFamily])){
				return array();
			}

			return $Response[$LanguageFamily];
		}

		return $Response;
	}


	/**
	 * Set the Source details from a relevant source-texts source. You can overload it to simplify the steps involved.
	 *
	 * @param  array		$Source	array of lines that need to be processed
	 * @param  string		$PathToTexts	the path to the directory holding the actual texts (absolute paths preferred)
	 * @param  array		$Version	array of the version being fetched
	 * @return this
	 */
	public function checkVersion( string $Abbreviation ){
		if( is_null( $Abbreviation ) ){
			return false;
		}

		if( !array_key_exists( $Abbreviation, $this->AllVersions ) ){
			return false;
		}

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
	public function getVersion( string $Abbreviation = NULL ){
		if( is_null( $Abbreviation ) ){
			if( is_null( $this->CurrentVersion ) ){
				throw new \Exception('Unable to getVersion for the CurrentVersion, as no current version has been defined.');
			}
			if( !array_key_exists( $this->CurrentVersion, $this->AllVersions ) ){
				throw new \Exception('Unable to getVersion for the CurrentVersion, as that version does not exist');
			}

			return $this->AllVersions[$this->CurrentVersion];
		}

		if( !array_key_exists( $Abbreviation, $this->AllVersions ) ){
			throw new \Exception('Unable to getVersion as version does not exist: ' . $Abbreviation );
		}

		return $this->AllVersions[$Abbreviation];
	}

	/**
	 * Return all of the stored versions
	 *
	 * @return this
	 */
	public function getVersions(){
		return $this->AllVersions;
	}

	/**
	 * Grab languages from the Language Controller and store them with their versions
	 *
	 * @param  array		$Source	array of lines that need to be processed
	 * @param  string		$PathToTexts	the path to the directory holding the actual texts (absolute paths preferred)
	 * @param  array		$Version	array of the version being fetched
	 * @return this
	 */
	public function loadLanguages(){
		foreach( array_keys( $this->AllVersions ) as $abv ){
			if( empty( $this->AllVersions[$abv]->getLanguageAbbreviation() )){
				continue;
			}

			try{
				$Language = $this->SourceText->getLanguage( $this->AllVersions[$abv]->getLanguageAbbreviation() );
				$this->AllVersions[$abv]->setLanguage( $Language );
			}catch( Exception $e){
				// This is fine to fail.
			}
		}

		return $this;
	}


	/**
	 * Set the Source details from a relevant source-texts source. You can overload it to simplify the steps involved.
	 *
	 * @param  array		$Source	array of lines that need to be processed
	 * @param  string		$PathToTexts	the path to the directory holding the actual texts (absolute paths preferred)
	 * @param  array		$Version	array of the version being fetched
	 * @return this
	 */
	public function loadVersions( string $VersionsFilePath ){
		try{
			$VersionList = $this->SourceText->_loadJson( $VersionsFilePath );
		}catch( Exception $e){
			echo $e->getMessage();
			exit(1);
		}

		foreach( $VersionList as $abv => $v ){
			$tmpVersion = new Version( $this->SourceText, $v );
			$this->addVersion( $abv, $tmpVersion );
		}

		return $this;
	}

	/**
	 * Set the current version to be used
	 *
	 * @param  string		$Abbreviation	This should be the abbreviation for the desired version
	 * @return this
	 */
	public function setVersion( string $Abbreviation ){
		if( !array_key_exists( $Abbreviation, $this->AllVersions ) ){
			throw new \Exception('Unable to setVersion as version does not exist: ' . $Abbreviation );
		}

		$this->CurrentVersion = $Abbreviation;

		return $this;
	}
}