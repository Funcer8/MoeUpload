<?php

use MediaWiki\MediaWikiServices;

class MoeUploadHooks {
	public static function onUploadForm_initial( $specialPage ) {
		$specialPage->getOutput()->addModules( 'ext.MoeUpload' );
		return true;
	}

	/**
	 * Add three fields on Special:Upload for categories and descriptions.
	 * @param mixed[] $descriptor
	 * @return true
	 */
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

	public static function onUploadCreateFromRequest( $type, &$className ) {
		$uploadHandlers = [ 'Stash', 'File', 'Url' ];
		if ( $className === null && in_array( $type, $uploadHandlers ) ) {
			$className = 'MoeUploadFrom' . $type;
		}
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
					// Canonical namespace should be always preferred
					$uploadFormObj->mComment .= '[[Category:' . $uploadFormObj->msg( 'moeupload-category-author', $author )->inContentLanguage()->text() . ']]';
				}
			}

			foreach (explode(' ', $charNames) as $catagory) {
				if ($catagory != '') {
					$uploadFormObj->mComment .= '[[Category:' . $uploadFormObj->msg( 'moeupload-category-char', $catagory )->inContentLanguage()->text() . ']]';
				}
			}
			if ($srcUrl != '') {
				$uploadFormObj->mComment .= $uploadFormObj->msg( 'moeupload-SrcUrl' )->inContentLanguage()->text() . $srcUrl;
			}
			$uploadFormObj->mComment .= $suffix;
		}

		return true;
	}

	public static function onUploadVerifyUpload( UploadBase $upload, User $user, $props, $comment, $pageText, &$error ) {
		if ( substr( get_class( $upload ), 0, 13 ) === 'MoeUploadFrom' &&
			$upload->mCharNames === '' && $upload->mAuthors === '' && $upload->mSrcUrl === '' &&
			!$user->isAllowed( 'moeupload-skipwarning' )
		) {
			$error = 'moeupload-NoDetail';
		}
	}

	public static function onMakeGlobalVariablesScript( array &$vars, OutputPage $out ) {
		if ( $out->getTitle()->isSpecial( 'Upload' ) ) {
			$vars['wgMoeUploadSkipWarning'] = $out->getUser()->isAllowed( 'moeupload-skipwarning' );
		}
	}
}
