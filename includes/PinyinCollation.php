<?php

namespace PinyinSort;

use Collation;

class PinyinCollation extends Collation {
	private ?array $conversionTable = null;

	protected function loadConversionTable(): array {
		if ( $this->conversionTable === null ) {
			$this->conversionTable = require __DIR__ . '/ConversionTable.data.php';
		}
		return $this->conversionTable;
	}

	public function convertZhToPinyin( $string ) {
		$convTable = $this->loadConversionTable();

		$len = mb_strlen( $string, 'UTF-8' );
		$builder = '';
		for ( $i = 0; $i < $len; $i++ ) {
			$char = mb_substr( $string, $i, 1, 'UTF-8' );
			$charLen = strlen( $char );
			if ( ord( $char[0] ) < 128 ) {
				$builder .= $char;
			} else if ( isset( $convTable[$char] ) ) {
				$builder .= ucfirst( $convTable[$char] );
			} else {
				$builder .= '?';
			}
		}

		return $builder;
	}

	public function getSortKey( $string ) {
		if ( strpos( $string, "\n" ) === false ) {
			$key = $string;
			$original = $string;
		} else {
			$parts = explode( "\n", $string, 2 );
			$key = $parts[0];
			$original = $parts[1];
		}

		$key = ucfirst( $this->convertZhToPinyin( $key ) );

		return "$key\n$original";
	}

	public function getFirstLetter( $string ) {
		$firstChar = mb_substr( $string, 0, 1, 'UTF-8' );
		$pinyin = $this->convertZhToPinyin( $firstChar );
		return ucfirst( $pinyin[0] );
	}
}
