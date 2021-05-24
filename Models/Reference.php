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

class Reference{
	protected $Reference;
	protected $SourceAbberviation;
	private $Segments = false;
	private $Verses = false;
	// private $hasChapters;

	/* -- Data Validation -- */
	private $Segments_Allowed = array('Chapters', 'Abbreviations', 'None');


	public function __construct(){
		var_dump('Made Reference Model');
	}




	/*
	 *  Getters
	 */
	function getReference(){
		
	}
	function getSegments(){
		return $this->Segments;
	}
	function getVerses(){
		return $this->Verses;
	}
	
	


	/*
	 *  Setters
	 */
	function setSegments( string $Segments ){
		if( in_array( $Segments, $this->Segments_Allowed ) ){
			$this->Segments = $Segments;
		}

		return $this;
	}
	function setVerses( bool $Verses ){
		if( is_bool( $Verses ) ){
			$this->Verses = $Verses;
		}

		return $this;
	}

}
?>