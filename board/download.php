<?php
	Define('__ROOT__', $_SERVER['DOCUMENT_ROOT']);

	Require_Once __ROOT__ . '/inc/DBConnection.php';
	mb_internal_encoding('UTF-8');

	$uploadPath = $_SERVER['DOCUMENT_ROOT'] . _GLOBAL_FILE_PATH_ . '/';

	If(StrCmp($_SERVER['REQUEST_METHOD'], 'GET') === 0) {
		$IDX = (IsSet($_GET['idx']) && !Empty($_GET['idx'])) ? IntVal($_GET['idx']) : 0;
		$SEQ = (IsSet($_GET['seq']) && !Empty($_GET['seq'])) ? IntVal($_GET['seq']) : 0;
		
		$DB->setProcName('SP_BOARD_FILE_DOWNLOAD_UPDATE');
		$DB->bind_param('P_IDX', 				$IDX,				_INT, 		20,			_PARAM_IN);
		$DB->bind_param('P_SEQ', 			$SEQ,				_INT, 		11,			_PARAM_IN);
		$DB->ExecuteProc();
		
		$DB->setProcName('SP_WEB_BOARD_FILE_DETAIL_SELECT');
		$DB->bind_param('P_IDX', 				$IDX,				_INT, 		20,			_PARAM_IN);
		$DB->bind_param('P_SEQ', 			$SEQ,				_INT, 		11,			_PARAM_IN);

		If ($DB->ExecuteProc()) {
			$Result = $DB->get_fetch_assoc();

			If ($Result) {
				$FILE_NAME 				= $Result[0]['FILE_NAME'];
				$FILE_REALNAME 		= $Result[0]['FILE_REALNAME'];
				$BOARD_ID				= $Result[0]['BOARD_ID'];
			}
		}

		$filePath = $uploadPath . $BOARD_ID . '/' . $FILE_NAME;

		If(Is_File($filePath)) {
			$pass = True;

			If(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off');

			$name = (CheckLeaveIE() === True) ? RawURLEncode($FILE_REALNAME) : $FILE_REALNAME;

			if ( CheckLeaveIE() ) {
				$name = iconv('UTF-8', 'EUC-KR', $name);
			}

			Header('Pragma: no-cache');
			Header('Expires: 0');
			Header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			Header('Last-Modified: '.GMDate('D, d M Y H:i:s', Filemtime($filePath)).' GMT');
			Header('Cache-Control: private', False);
			Header('Content-Type: application/octet-stream');
			Header('Content-Disposition: attachment; filename="'.$name.'"');
			Header('Content-Transfer-Encoding: binary');
			Header('Content-Length: '.FileSize($filePath));
			Header('Connection: close');

			ob_clean();
			flush();

			ReadFile($filePath);
		}
	}

	If($pass <> True) Header('Location: /');
?>