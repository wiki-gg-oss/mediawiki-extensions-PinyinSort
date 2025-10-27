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

	public function convertZhToPinyin( $string ): string {
		$convTable = $this->loadConversionTable();

        $chrs = mb_str_split( $string, 1, 'UTF-8' );
        $chrs = array_map( static function ( $chr ) use ( &$convTable ) {
			if ( ord( $chr[0] ) < 128 ) {
				return $chr;
			} else if ( array_key_exists( $chr, $convTable ) ) {
				return ucfirst( $convTable[$chr] );
			}
			return '?';
        }, $chrs );

		return implode( '', $chrs );
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
