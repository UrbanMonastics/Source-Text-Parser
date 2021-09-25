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

class Text{
	/* -- Source Members -- */
	protected $Abbreviation;	// Required
	protected $Title;	// Required
	protected $Description;
	protected $Type;	// Required [Book, Letter, Dictionary]
	protected $SecondaryType;
	protected $Language;	// Required
	protected $SecondaryLanguage;
	protected $Version;	// Required
	protected $Segments = 'None';
	protected $SegmentTitles = false;
	protected $Verses = false;
	protected $Notes;
	protected $Extra;

	/* -- Data Validation -- */
	private $Type_Allowed = array('Book','Letter','Dictionary');
	private $Segments_Allowed = array('Chapters', 'Abbreviations', 'None');


	public function __construct( Array $RawText = array() ){
		if( array_key_exists( 'Abbreviation', $RawText ) ){
			$this->setAbbreviation( $RawText['Abbreviation'] );
		}

		if( array_key_exists( 'Title', $RawText ) ){
			$this->setTitle( $RawText['Title'] );
		}

		if( array_key_exists( 'Description', $RawText ) ){
			$this->setDescription( $RawText['Description'] );
		}

		if( array_key_exists( 'Type', $RawText ) ){
			$this->setType( $RawText['Type'] );
		}

		if( array_key_exists( 'SecondaryType', $RawText ) ){
			$this->setSecondaryType( $RawText['SecondaryType'] );
		}

		if( array_key_exists( 'Language', $RawText ) ){
			$this->setLanguage( $RawText['Language'] );
		}

		if( array_key_exists( 'SecondaryLanguage', $RawText ) ){
			$this->setSecondaryLanguage( $RawText['SecondaryLanguage'] );
		}

		if( array_key_exists( 'Version', $RawText ) ){
			$this->setVersion( $RawText['Version'] );
		}

		if( array_key_exists( 'Segments', $RawText ) ){
			$this->setSegments( $RawText['Segments'] );
		}

		if( array_key_exists( 'SegmentTitles', $RawText ) ){
			$this->setSegmentTitles( $RawText['SegmentTitles'] );
		}

		if( array_key_exists( 'Verses', $RawText ) ){
			$this->setVerses( $RawText['Verses'] );
		}

		if( array_key_exists( 'Notes', $RawText ) ){
			$this->setNotes( $RawText['Notes'] );
		}

		if( array_key_exists( 'Extra', $RawText ) ){
			$this->setExtra( $RawText['Extra'] );
		}

		return $this;
	}

	/*
	 *  Getters
	 */
	// function get(){
	// 	return $this->;
	// }
	function getAbbreviation(){
		return $this->Abbreviation;
	}
	function getTitle(){
		return $this->Title;
	}
	function getDescription(){
		return $this->Description;
	}
	function getType(){
		return $this->Type;
	}
	function getSecondaryType(){
		return $this->SecondaryType;
	}
	function getLanguage(){
		return $this->Language;
	}
	function getSecondaryLanguage(){
		return $this->SecondaryLanguage;
	}
	function getVersion(){
		return $this->Version;
	}
	function getSegmentTitles(){
		return $this->SegmentTitles;
	}
	function getSegments(){
		return $this->Segments;
	}
	function getVerses(){
		return $this->Verses;
	}
	function getNotes(){
		return $this->Notes;
	}
	function getExtra(){
		return $this->Extra;
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
	function setType( string $Type ){
		if( in_array( $Type, $this->Type_Allowed ) )
			$this->Type = $Type;

		return $this;
	}
	function setSecondaryType( string $SecondaryType ){
		$this->SecondaryType = $SecondaryType;

		return $this;
	}
	function setLanguage( string $Language ){
		$this->Language = $Language;

		return $this;
	}
	function setSecondaryLanguage( string $SecondaryLanguage ){
		$this->SecondaryLanguage = $SecondaryLanguage;

		return $this;
	}
	function setVersion( string $Version ){
		$this->Version = $Version;

		return $this;
	}
	function setSegments( string $Segments ){
		if( in_array( $Segments, $this->Segments_Allowed ) ){
			$this->Segments = $Segments;
		}

		return $this;
	}
	function setSegmentTitles( bool $SegmentTitles ){
		if( is_bool( $SegmentTitles ) ){
			$this->SegmentTitles = $SegmentTitles;
		}

		return $this;
	}
	function setVerses( bool $Verses ){
		if( is_bool( $Verses ) ){
			$this->Verses = $Verses;
		}

		return $this;
	}
	function setNotes( string $Notes ){
		$this->Notes = $Notes;

		return $this;
	}
	function setExtra( array $Extra ){
		$this->Extra = $Extra;

		return $this;
	}
}