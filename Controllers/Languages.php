<?php

/***********
 *	
 *	Source Text Parser
 *	https://developers.urbanmonastic.org/
 *	
 *	© Paul Prins
 *	https://paulprins.net https://paul.build/
 *	
 *	Licensed under MIT - For full license, view the LICENSE distributed with this source.
 *	
 ***********/

namespace UrbanMonastics\SourceTextParser\Controllers;

use UrbanMonastics\SourceTextParser\Models\Language as Language;
use UrbanMonastics\SourceTextParser\SourceText as SourceText;

class Languages{
	// Manage the location, and loading of the various files from the location
	private $Languages = array();
	private $Families = array();

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
	public function addLanguage( string $Abbreviation, Language $newLanguage ){
		$this->Languages[$Abbreviation] = $newLanguage;
		if( !array_key_exists( $newLanguage->getFamily(), $this->Families ) )
			$this->Families[$newLanguage->getFamily()] = array();

		/* -- Add to the family index -- */
		$this->Families[$newLanguage->getFamily()][] = $newLanguage;
		// Fancy sort the family by the age of the related entries with the newest at the top of the list
		usort( $this->Families[$newLanguage->getFamily()], function($a, $b) {
			if( is_null( $a->getEndDate() ) ){
				if( $a->getEndDate() == $b->getEndDate() ){
					// Both end dates are open, so which ever one started first should be listed first.
					 return $a->getStartDate() > $b->getStartDate();
				}

				return -1;
			}else if( is_null( $b->getEndDate() ) )
				return 1;

			return -($a->getEndDate() <=> $b->getEndDate());
		});
	}


	/**
	 * Retreive a specific language family
	 *
	 * @param  string		$FamilyName	The name of the language family you are requesting
	 * @return array of Languages
	 */
	public function getFamily( string $FamilyName = NULL ){
		if( is_null( $FamilyName ) || $FamilyName == ''){
			throw new \Exception('Unable to getFamily as no current family name has been defined.');
		}

		if( !array_key_exists( $FamilyName, $this->Families ) ){
			throw new \Exception('Unable to getFamily as the language family does not exist: \'' . $FamilyName . '\'' );
		}

		return $this->Families[$FamilyName];
	}

	/**
	 * Return all of the languages grouped by language family
	 *
	 * @return array of languages
	 */
	public function getFamilies(){
		return $this->Families;
	}


	/**
	 * Retreive a specific language
	 *
	 * @param  string		$Abbreviation	array of lines that need to be processed
	 * @return Language
	 */
	public function getLanguage( string $Abbreviation = NULL ){
		if( is_null( $Abbreviation ) || $Abbreviation == '' ){
			throw new \Exception('Unable to getLanguage as no current abbreviation has been defined.');
		}

		if( !array_key_exists( $Abbreviation, $this->Languages ) ){
			throw new \Exception('Unable to getLanguage as language does not exist: \'' . $Abbreviation . '\'' );
		}

		return $this->Languages[$Abbreviation];
	}

	/**
	 * Return all of the languages that are current loaded
	 *
	 * @return array of languages
	 */
	public function getLanguages(){
		return $this->Languages;
	}


	/**
	 * Set the Source details from a relevant source-texts source. You can overload it to simplify the steps involved.
	 *
	 * @param  array		$Source	array of lines that need to be processed
	 * @param  string		$PathToTexts	the path to the directory holding the actual texts (absolute paths preferred)
	 * @param  array		$Version	array of the version being fetched
	 * @return this
	 */
	public function loadLanguages( string $LanguagesFilePath ){
		try{
			$LanguageList = $this->SourceText->_loadJson( $LanguagesFilePath );
		}catch( Exception $e){
			echo $e->getMessage();
			exit(1);
		}

		// var_dump( $LanguageList );

		foreach( $LanguageList as $Family => $Varraints ){
			foreach( $Varraints as $abv => $v ){
				$tmpLanguage = new Language( $abv, $v, $Family );
				$this->addLanguage( $abv, $tmpLanguage );
			}
		}

		return $this;
	}
}