mw.hook( 'htmlform.enhance' ).add( function ( $root ) {
	var srcUrlChecker, categoryChecker;

	function checkUrl( url ) {
		var d = $.Deferred(), ret = { valid: true, messages: [] };
		if ( url !== '' ) {
			const supported = mw.config.get( 'wgFileExtensions' ).join( '|' );
			if ( new RegExp( '\.(?:' + supported + ')$', 'i' ).test( url ) ) {
				ret = { valid: false, messages: [ mw.msg( 'moeupload-PageInsteadOfImg' ) ] };
			} else if ( $root.find(  '#wpUploadFileURL:enabled' ).val() === url ) {
				ret = { valid: false, messages: [ mw.msg( 'moeupload-SameAsSourceURL' ) ] };
			}
		}
		d.resolve( ret );
		return d.promise();
	}
	srcUrlChecker = new mw.htmlform.Checker( $root.find( '#wpSrcUrl' ), checkUrl );
	srcUrlChecker.attach();

	const regexp_title = new RegExp( '[^' + mw.config.get( 'wgLegalTitleChars' ) + ']|' +
		mw.message( 'moeupload-symbols-regexp' ).plain() );
	function checkCategory( category ) {
		var d = $.Deferred(), ret = { valid: true, messages: [] };
		if ( category !== '' ) {
			if ( regexp_title.test( category ) ) {
				ret = { valid: false, messages: [ mw.msg( 'moeupload-HaveSymbol' ) ] };
			}
		}
		d.resolve( ret );
		return d.promise();
	}
	categoryChecker = new mw.htmlform.Checker( $root.find( '#wpCharName' ), checkCategory );
	categoryChecker.attach();
	categoryChecker = new mw.htmlform.Checker( $root.find( '#wpAuthor' ), checkCategory );
	categoryChecker.attach();
} );

$( function() {
	var descBox = $( '#wpUploadDescription' );
	if ( descBox.length > 0 ) {
		var toggleLink = $( '<a/>' ).attr( 'href', 'javascript:void(0);' );
		toggleLink.text( mw.msg( 'moeupload-ForManagementOnly' ) ).on( 'click', function() {
			descBox.slideToggle();
			return false;
		} );
		descBox.hide();
		descBox.before( toggleLink );
	}
	var fieldBox = $( '#mw-htmlform-description' );
	fieldBox.find( 'tr:last-child' ).after( '<tr><td class="moeupload-warning" colspan=2></td></tr>' );
	var warningBox = fieldBox.find( '.moeupload-warning' );
	/* XpAhH同学写的上传页面检测，未写注释禁止上传 */
	$( '#mw-upload-form' ).on( 'submit', function() {
		var ifHaveFile = $( '#wpUploadFile:enabled, #wpUploadFileURL:enabled' ).val() !== '';
		if ( !ifHaveFile ) {
			warningBox.addClass( 'warningbox' ).text( mw.msg( 'moeupload-NoFile' ) );
			$( '#wpUploadFileURL, #wpUploadFile' ).one( 'change', function() {
				warningBox.removeClass( 'warningbox' ).text( '' );
			} );
			return false;
		}
		if (mw.config.get( 'wgMoeUploadSkipWarning' ) || mw.util.getParamValue( 'disableUploadCheck' ) === 'true' ) {
			return true;
		}
		//三选一
		var haveNoDetail = fieldBox.find( '#wpCharName, #wpAuthor, #wpSrcUrl' ).filter( function () {
			return this.innerText.length === 0;
		});
		if ( haveNoDetail.length === 3 ) {
			$( '#wpDestFile-warning' ).addClass( 'warningbox' ).text( mw.msg( 'moeupload-NoDetail' ) );
			$( '#wpCharName, #wpAuthor, #wpSrcUrl' ).one( 'change', function() {
				warningBox.removeClass( 'warningbox' ).text( '' );
			} );
			return false;
		}
		// Errors populated by mw.htmlform.Checker
		if ( fieldBox.find( '.errorbox' ).length > 0 ) {
			return false;
		}
		return true;
	} );
} );
