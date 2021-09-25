<?php

use UrbanMonastics\SourceTextParser\SourceTextParser as SourceTextParser;

class TestSourceTextParser extends SourceTextParser{
	public function getTextLevelElements(){
		return $this->textLevelElements;
	}
}
?>