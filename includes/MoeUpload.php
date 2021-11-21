<?php

use MediaWiki\MediaWikiServices;

//在上传界面增加三个字段
class MoeUploadHooks {
	public static function onUploadForm_initial( $specialPage ) {
		$specialPage->getOutput()->addModules( 'ext.MoeUpload' );
		return true;
	}

	public static function onUploadFormInitDescriptor( &$descriptor ) {
		$descriptor += [
			'CharName' => [
				'type' => 'text',
				'section' => 'description',
				'id' => 'wpCharName',
				'label-message' => 'moeupload-CharName',
				'size' => 60,
				//'default' => $this->mCharName,
			],
			'Author' => [
				'type' => 'text',
				'section' => 'description',
				'id' => 'wpAuthor',
				'label-message' => 'moeupload-Author',
				'size' => 60,
				//'default' => $this->mAuthor,
			],
			'SrcUrl' => [
				'type' => 'text',
				'section' => 'description',
				'id' => 'wpSrcUrl',
				'label-message' => 'moeupload-SrcUrl',
				'size' => 60,
				//'default' => $this->mSrcUrl,
			]
		];
		return true;
	}

	public static function onBeforeProcessing( &$uploadFormObj ) {
		$request = $uploadFormObj->getRequest();
		if( $request->getFileName( 'wpUploadFile' ) !== null ||
			$request->getText( 'wpUploadFileURL' ) !== null
		) {
			$authors = $request->getText( 'wpAuthor' );
			$srcUrl = $request->getText( 'wpSrcUrl' );
			$charNames = $request->getText( 'wpCharName' );
			$suffix = '';
			if ($uploadFormObj->mUploadDescription != '' && $uploadFormObj->mComment == '') {
				if ($srcUrl != '') {
					$suffix = ' ';
				}
				$suffix .= $uploadFormObj->mUploadDescription;
			}

			foreach (explode(' ', $authors) as $author) {
				if ($author != '') {
					$uploadFormObj->mComment .= "[[分类:作者:$author]]";
				}
			}

			foreach (explode(' ', $charNames) as $catagory) {
				if ($catagory != '') {
					$uploadFormObj->mComment .= "[[分类:$catagory]]";
				}
			}
			if ($srcUrl != '') {
				$uploadFormObj->mComment .= '源地址：' . $srcUrl;
			}
			$uploadFormObj->mComment .= $suffix;
		}

		return true;
	}

	public static function onMakeGlobalVariablesScript( array &$vars, OutputPage $out ) {
		if ( $out->getTitle()->getPrefixedDBkey() === 'Special:Upload' ) {
			$config = MediaWikiServices::getInstance()->getMainConfig();
			$vars['wgFileExtensions'] = $config->get( 'wgFileExtensions' );
		}
	}
}
