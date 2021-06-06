<?php

use UrbanMonastics\SourceParser\SourceParser as SourceParser;

class TestSourceParser extends SourceParser{
	public function getTextLevelElements(){
		return $this->textLevelElements;
	}
}
?>