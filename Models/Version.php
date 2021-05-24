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

namespace UrbanMonastics\SourceParser\Models;

use UrbanMonastics\SourceParser\Models\Language as Language;
use UrbanMonastics\SourceParser\SourceText as SourceText;

class Version{
	/* -- Version Members -- */
	protected $Abbreviation;	// Required
	protected $Title;	// Required
	protected $Description;
	protected $License;
	protected $LicenseHTML;
	protected $PublicDomain;
	protected $Permission;
	protected $Language;	// Required
	protected $LanguageAbbreviation;	// Required
	protected $Date;
	protected $SmallCaps;
	protected $Selah;
	protected $Notes;


	/* -- Reference to Parent -- */
	private $SourceText;


	public function __construct( SourceText $SourceText, Array $RawVersion = array() ){
		$this->SourceText = $SourceText;	// Passed by Reference

		if( array_key_exists( 'Abbreviation', $RawVersion ) ){
			$this->setAbbreviation( $RawVersion['Abbreviation'] );
		}

		if( array_key_exists( 'Title', $RawVersion ) ){
			$this->setTitle( $RawVersion['Title'] );
		}

		if( array_key_exists( 'Description', $RawVersion ) ){
			$this->setDescription( $RawVersion['Description'] );
		}

		if( array_key_exists( 'License', $RawVersion ) ){
			$this->setLicense( $RawVersion['License'] );
		}

		if( array_key_exists( 'PublicDomain', $RawVersion ) ){
			$this->setPublicDomain( $RawVersion['PublicDomain'] );
		}

		if( array_key_exists( 'Permission', $RawVersion ) ){
			$this->setPermission( $RawVersion['Permission'] );
		}

		if( array_key_exists( 'Language', $RawVersion ) ){
			$this->setLanguageAbbreviation( $RawVersion['Language'] );
		}

		if( array_key_exists( 'Date', $RawVersion ) ){
			$this->setDate( $RawVersion['Date'] );
		}


		if( array_key_exists( 'Options', $RawVersion ) ){
			if( array_key_exists( 'SmallCaps', $RawVersion['Options'] ) ){
				$this->setSmallCaps( $RawVersion['Options']['SmallCaps'] );
				$this->SourceText->setSmallCapsText( $this->getSmallCaps() );
			}

			if( array_key_exists( 'Selah', $RawVersion['Options'] ) ){
				$this->setSelah( $RawVersion['Options']['Selah'] );
				$this->SourceText->setSelahHTML( $this->getSelah() );
			}
		}

		if( array_key_exists( 'Notes', $RawVersion ) ){
			$this->setNotes( $RawVersion['Notes'] );
		}

		return $this;
	}

	/*
	 *  Getters
	 */
	function getAbbreviation(){
		return $this->Abbreviation;
	}
	function getTitle(){
		return $this->Title;
	}
	function getDescription(){
		return $this->Description;
	}
	function getLicense( $AsMarkDown = false ){
		if( $AsMarkDown ){
			return $this->License;
		}

		return $this->LicenseHTML;
	}
	function getPublicDomain(){
		return $this->PublicDomain;
	}
	function getPermission(){
		return $this->Permission;
	}
	function getLanguage(){
		return $this->Language;
	}
	function getLanguageAbbreviation(){
		return $this->LanguageAbbreviation;
	}
	function getDate(){
		return $this->Date;
	}
	function getSmallCaps(){
		return $this->SmallCaps;
	}
	function getSelah(){
		return $this->Selah;
	}
	function getNotes(){
		return $this->Notes;
	}

	/*
	 * Setters
	 */
	function setAbbreviation( string $Abbreviation ){
		$this->Abbreviation = $Abbreviation;

		return $this;
	}
	function setTitle( string $Title ){
		$this->Title = $Title;

		return $this;
	}
	function setDescription( string $Description ){
		$this->Description = $Description;

		return $this;
	}
	function setLicense( string $License ){
		$this->License = $License;
		$this->LicenseHTML = $this->SourceText->line( $License );
// This should be in MD, so we should also define an HTML version

		return $this;
	}
	function setPublicDomain( bool $PublicDomain ){
		if( is_bool( $PublicDomain ) ){
			$this->PublicDomain = $PublicDomain;
		}

		return $this;
	}
	function setPermission( string $Permission ){
		$this->Permission = $Permission;

		return $this;
	}
	function setLanguageAbbreviation( string $LanguageAbbreviation ){
		$this->LanguageAbbreviation = $LanguageAbbreviation;

		return $this;
	}
	function setLanguage( Language $Language ){
		$this->Language = $Language;

		return $this;
	}
	function setDate( string $Date ){
		$this->Date = $Date;

		return $this;
	}
	function setSmallCaps( bool $SmallCaps ){
		if( is_bool( $SmallCaps ) ){
			$this->SmallCaps = $SmallCaps;
		}

		return $this;
	}
	function setSelah( bool $Selah ){
		if( is_bool( $Selah ) ){
			$this->Selah = $Selah;
		}

		return $this;
	}
	function setNotes( string $Notes ){
		$this->Notes = $Notes;

		return $this;
	}
}