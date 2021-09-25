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

namespace UrbanMonastics\SourceTextParser\Parsing;

use UrbanMonastics\SourceTextParser\Models\Source as Source;
use UrbanMonastics\SourceTextParser\Models\Reference as theReference;

class Reference{
	/* -- Reference Variables -- */
	private $Reference;
	private $Source;
	
	public function __construct( string $ReferenceString, Source $Source = null){
		$this->Reference = new theReference();
		$this->Source = $Source;

		// Copy some Source values into the Reference
		$this->Reference->setSegments( $this->Source->getSegments() );
		$this->Reference->setVerses( $this->Source->getVerses() );

		$tmp = $this->parse( $ReferenceString, $this->Reference->getSegments() == 'Chapters', $this->Reference->getVerses() );
		
		
		

var_dump( $ReferenceString, $tmp, $this->Reference );
		return $this->Reference;
	}


	private function parse( string $Reference, bool $WithChapters = false, bool $WithVerses = false ){
		$Response = array();
		if( ($WithVerses === true && $WithChapters === false) ){
			// This only has Verrses

			if( strpos( $Reference, ',' ) !== false ){
				$segments = explode( ',', $Reference );
			}else{
				$segments = array( $Reference );
			}

			foreach( $segments as $s ){
				$s = trim( $s );
				if( strpos( $s, '-') ){
					$tmp = explode( '-', $s );
					for ($i = $tmp[0]; $i <= $tmp[1]; $i++) {
						$Response[] =  (int) $i;
					}
				}else{
					$Response[] = (int) $s;
				}
			}
			asort( $Response );
			
		}else if( ($WithChapters === true && $WithVerses === false) ){
			// This only has chapters

			$tmp = $this->parse( $Reference, false, true );	// Same as verses, but we just need to set the response as the keys
			$Response = array_fill_keys( $tmp, array() );

		}else if( $WithVerses === true && $WithChapters === true ){
			// There are Chapters and Verses

			$FirstBreak = strpos( $Reference, ':' );
			if( $FirstBreak === false && strpos( $Reference, '-' ) === false && strpos( $Reference, ',' ) === false ){
				// This is just simply a chapter number
				$Response[$Reference] = array();
			}
			else if( $FirstBreak === false ){
				// This is a list of chapters to include without any verse references
				$Response = $this->parse( $Reference, true, false );
				ksort( $Response );
			}
			else if( $FirstBreak !== false && substr_count( $Reference, ':') == 1 ){
				// A Single chapter with verses
				$tmp = explode( ':', $Reference );
				$Response[$tmp[0]] = $this->parse( $tmp[1], false, true );
			}else{
				// These have verses multiple chapters with verses

				// Split the request in half at the `-CHAPTER:` mark
				$segments =  preg_split("/(-[0-9]*:)/i", $Reference, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);


				$StartTmp = explode( ':', $segments[0], 2 );
				$FirstVerses = $StartTmp[1] . '-300';	// Up to 300 verses, since I do not know of any chapters/segments beyond 180 verses. This 2/3rds buffer should be sufficant.
				$EndTmp = substr( $segments[1], 1, -1 );
				$LastVerses = '1-' . $segments[2];

				// Fill the response keys with all the chapters
				$Response = array_fill( $StartTmp[0], ((int)$EndTmp - (int)$StartTmp[0] + 1), array());

				// Ensure we only get the portion of the first chapter that is requested
				$Response[$StartTmp[0]] = $this->parse( $FirstVerses, false, true );

				// Ensure we only get the portion of the last chapter that is requested
				$Response[$EndTmp] = $this->parse( $LastVerses, false, true );
			}
		}

		return $Response;
	}
}
?>