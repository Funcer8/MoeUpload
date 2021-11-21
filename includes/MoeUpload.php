<?php
//在上传界面增加三个字段

class MoeUploadHooks {
	public static function MoeUploadUploadForminitial ( $outputPage ) {
		global $wgOut;
		$wgOut -> addModules( 'ext.MoeUpload' );
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

	public static function BeforeProcessing( &$uploadFormObj ) {
		if( $uploadFormObj->mRequest->getFileName( 'wpUploadFile' ) !== null || $uploadFormObj->mRequest->getFileName( 'wpUploadFileURL' ) !== null) {
			$uploadFormObj->mAuthor = $uploadFormObj->mRequest->getText( 'wpAuthor' );
			$uploadFormObj->mSrcUrl = $uploadFormObj->mRequest->getText( 'wpSrcUrl' );
			$uploadFormObj->mCharName = $uploadFormObj->mRequest->getText( 'wpCharName' );
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
}
