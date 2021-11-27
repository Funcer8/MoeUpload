<?php

class MoeUploadFromUrl extends UploadFromUrl {
	public string $mCharNames = '';
	public string $mAuthors = '';
	public string $mSrcUrl = '';

	/**
	 * @inheritdoc
	 */
	public function initializeFromRequest( &$request ) {
		$this->mCharNames = $request->getText( 'wpCharName' );
		$this->mAuthors = $request->getText( 'wpAuthor' );
		$this->mSrcUrl = $request->getText( 'wpSrcUrl' );

		parent::initializeFromRequest( $request );
	}

	/**
	 * @inheritdoc
	 */
	public function checkWarnings( $user = null ) {
		$warnings = parent::checkWarnings( $user );
		if ( $this->mCharNames === '' && $this->mAuthors === '' && $this->mSrcUrl === '' &&
			$user->isAllowed( 'moeupload-skipwarning' )
		) {
			$warnings['moeupload-NoDetail'] = true;
		}
		return $warnings;
	}
}
