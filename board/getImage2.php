<?php
	header("Content-Type: text/html; charset=utf-8;");
	
	Define('__ROOT__', $_SERVER['DOCUMENT_ROOT']);
	Require_Once __ROOT__ . '/inc/DBConnection.php';

	$IDX = (!Empty($_GET['idx'])) ? IntVal($_GET['idx']) : Null;
	$SEQ = (!Empty($_GET['seq'])) ? IntVal($_GET['seq']) : Null;
	$temp = $_GET['temp'];
	
	$status = False;

	If (!Empty($IDX) && !Empty($SEQ)) {
		
		$DB->setProcName('SP_WEB_BOARD_FILE_DETAIL_SELECT');
		$DB->bind_param('P_IDX', 				$IDX,				_INT, 		20,			_PARAM_IN);
		$DB->bind_param('P_SEQ', 			$SEQ,				_INT, 		11,			_PARAM_IN);
		
		If ($DB->ExecuteProc()) {
			$Result = $DB->get_fetch_assoc();
		}

		//전송된 이미지 여부 확인
		If (!Empty($Result[0]['FILE_NAME'])) {

			//이미지 전체경로를 포함한 이미지명
			$imagePath = __ROOT__ . _GLOBAL_FILE_PATH_ . '/' . $Result[0]['BOARD_ID'] . '/' . $Result[0]['FILE_NAME'];

			//넘어온 이미지경로의 존재여부와 파일여부 확인
			If (File_Exists($imagePath) && is_file($imagePath)) {

				//넘어온 파일 확장자 추출
				$tmp_name = pathinfo($imagePath);
				$ext = strToLower($tmp_name['extension']);

				//지정된 확장자만 보여주도록 필터링
				if($ext == 'jpeg' || $ext == 'jpg' || $ext='gif' || $ext='png' || $ext='bmp') {
					$status = True;

					//이미지 크기정보와 사이즈를 얻어옴
					$img_info = getimagesize($imagePath);
					$filesize = filesize($imagePath);

					//이미지 전송을 위한 헤더설정
					header("Content-Type: {$img_info['mime']}\n");
					header("Content-Disposition: inline;filename='$FILE_NAME'\n");
					header("Content-Length: $filesize\n");

					ob_clean();
					flush();

					//이미지 내용을 읽어들임
					readfile($imagePath);

				}
			}
		}
	}

	If ($status == False) {
		//이미지 전체경로를 포함한 이미지명
		$imagePath = __ROOT__ . '/res/img/noimg.png';

		//이미지 크기정보와 사이즈를 얻어옴
		$img_info = getimagesize($imagePath);
		$filesize = filesize($imagePath);

		//이미지 전송을 위한 헤더설정
		header("Content-Type: {$img_info['mime']}\n");
		header("Content-Disposition: inline;filename='noimg.png'\n");
		header("Content-Length: $filesize\n");
		
		ob_clean();
		flush();

		//이미지 내용을 읽어들임
		readfile($imagePath);
	}
?>