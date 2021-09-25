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

class Source{
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
	
	/* -- Reference to Parent -- */
	private $SourceText;

	public function __construct( Array $RawSource = array() ){

		if( array_key_exists( 'Abbreviation', $RawSource ) ){
			$this->setAbbreviation( $RawSource['Abbreviation'] );
		}

		if( array_key_exists( 'Title', $RawSource ) ){
			$this->setTitle( $RawSource['Title'] );
		}

		if( array_key_exists( 'Description', $RawSource ) ){
			$this->setDescription( $RawSource['Description'] );
		}

		if( array_key_exists( 'Type', $RawSource ) ){
			$this->setType( $RawSource['Type'] );
		}

		if( array_key_exists( 'SecondaryType', $RawSource ) ){
			$this->setSecondaryType( $RawSource['SecondaryType'] );
		}

		if( array_key_exists( 'Language', $RawSource ) ){
			$this->setLanguage( $RawSource['Language'] );
		}

		if( array_key_exists( 'SecondaryLanguage', $RawSource ) ){
			$this->setSecondaryLanguage( $RawSource['SecondaryLanguage'] );
		}

		if( array_key_exists( 'Version', $RawSource ) ){
			$this->setVersion( $RawSource['Version'] );
		}

		if( array_key_exists( 'Segments', $RawSource ) ){
			$this->setSegments( $RawSource['Segments'] );
		}

		if( array_key_exists( 'SegmentTitles', $RawSource ) ){
			$this->setSegmentTitles( $RawSource['SegmentTitles'] );
		}

		if( array_key_exists( 'Verses', $RawSource ) ){
			$this->setVerses( $RawSource['Verses'] );
		}

		if( array_key_exists( 'Notes', $RawSource ) ){
			$this->setNotes( $RawSource['Notes'] );
		}

		if( array_key_exists( 'Extra', $RawSource ) ){
			$this->setExtra( $RawSource['Extra'] );
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