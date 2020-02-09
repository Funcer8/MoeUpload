<?php   
//在上传界面增加三个字段

if (!defined('MEDIAWIKI')) {
	exit;
}

function MoeUploadUploadForminitial ( $outputPage ) {
global $wgOut;
$wgOut -> addModules( 'ext.MoeUpload' );
return true;
}

function onUploadFormInitDescriptor( &$descriptor ) { 
	$descriptor += array(
		'CharName' => array(
			'type' => 'text',
			'section' => 'description',
			'id' => 'wpCharName',
			'label-message' => 'moemoeQCharName',
			'size' => 60,
			//'default' => $this->mCharName,
		),
		'Author' => array(
			'type' => 'text',
			'section' => 'description',
			'id' => 'wpAuthor',
			'label-message' => 'moemoeQAuthor',
			'size' => 60,
			//'default' => $this->mAuthor,
		),
		'SrcUrl' => array(
			'type' => 'text',
			'section' => 'description',
			'id' => 'wpSrcUrl',
			'label-message' => 'moemoeQSrcUrl',
			'size' => 60,
			//'default' => $this->mSrcUrl,
		)
	);
	return true;
}

function BeforeProcessing( &$uploadFormObj ) {
	if( $uploadFormObj->mRequest->getFileName( 'wpUploadFile' ) !== null || $uploadFormObj->mRequest->getFileName( 'wpUploadFileURL' ) !== null) {
	  $uploadFormObj->mAuthor            = $uploadFormObj->mRequest->getText( 'wpAuthor' );
	  $uploadFormObj->mSrcUrl            = $uploadFormObj->mRequest->getText( 'wpSrcUrl' );
	  $uploadFormObj->mCharName          = $uploadFormObj->mRequest->getText( 'wpCharName' );
	  $uploadFormObj->mUploadDescription = $uploadFormObj->mRequest->getText('wpUploadDescription');
	  $suffix = "";
	  if ($uploadFormObj->mUploadDescription != "" && $uploadFormObj->mComment == "") {
	      if ($uploadFormObj->mSrcUrl != "") {
	          $suffix .= " ";
	      }
	      $suffix .= $uploadFormObj->mUploadDescription;
	  }

	  foreach (explode(" ", $uploadFormObj->mAuthor) as $author) {
	      if ($author != "") {
	          $uploadFormObj->mComment .= "[[分类:作者:$author]]";
	      }
	  }

	  foreach (explode(" ", $uploadFormObj->mCharName) as $catagory) {
	      if ($catagory != "") {
	          $uploadFormObj->mComment .= "[[分类:$catagory]]";
	      }
	  }
	  if ($uploadFormObj->mSrcUrl != "") {
	      $uploadFormObj->mComment .= "源地址:".$uploadFormObj->mSrcUrl;
	  }
	  $uploadFormObj->mComment .= $suffix;
	}

	return $uploadFormObj;
}

?>
