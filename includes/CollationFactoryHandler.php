<?php

namespace PinyinSort;

class CollationFactoryHandler implements
	\MediaWiki\Hook\Collation__factoryHook
{
	/**
	 * @inheritDoc
	*/
	public function onCollation__factory( $collationName, &$collationObject ) {
		switch ( $collationName ) {
			case 'pinyin':
				$collationObject = new PinyinCollation();
				return false;
			case 'pinyin-noprefix':
				$collationObject = new PinyinCollationNoPrefix();
				return false;
		}

		return true;
	}
}
