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

namespace UrbanMonastics\SourceTextParser\Models;

use UrbanMonastics\SourceTextParser\SourceText as SourceText;

class Language{
	/* -- Version Members -- */
	protected $Abbreviation;	// Required
	protected $Title;	// Required
	protected $Family;	// Required
	protected $StartDate;
	protected $EndDate;



	public function __construct( String $Abbreviation, Array $RawLanguage = array(), String $FamilyName ){
		if( !is_null( $Abbreviation ) ){
			$this->setAbbreviation( $Abbreviation );
		}

		if( array_key_exists( 0, $RawLanguage ) ){
			$this->setTitle( $RawLanguage[0] );
		}

		if( array_key_exists( 1, $RawLanguage ) && is_array( $RawLanguage[1] ) ){
			if( array_key_exists( 0, $RawLanguage[1] ) && is_numeric( $RawLanguage[1][0] ) )
				$this->setStartDate( $RawLanguage[1][0] );

			if( array_key_exists( 1, $RawLanguage[1] ) && is_numeric( $RawLanguage[1][1] ) )
				$this->setEndDate( $RawLanguage[1][1] );
		}

		if( !is_null( $FamilyName ) ){
			$this->setFamily( $FamilyName );
		}

		return $this;
	}

	
}