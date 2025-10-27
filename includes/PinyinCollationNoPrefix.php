<?php

namespace PinyinSort;

class PinyinCollationNoPrefix extends PinyinCollation {
	private function preprocess( $string ) {
		if ( strpos( $string, "\n" ) !== false ) {
			return $string;
		}

		$parts = explode( ':', $string, 2 );
		if ( !( $parts[1] ?? null ) ) {
			return $string;
		} else {
			return "{$parts[1]}\n$string";
		}
	}

	public function getSortKey( $string ) {
		return parent::getSortKey( $this->preprocess( $string ) );
	}

	public function getFirstLetter( $string ) {
		return parent::getFirstLetter( $this->preprocess( $string ) );
	}
}
