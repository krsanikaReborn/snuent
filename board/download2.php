<?php
	Define('__ROOT__', $_SERVER['DOCUMENT_ROOT']);

	Require_Once __ROOT__ . '/inc/conn.php';

	Function CheckLeaveIE() {
		If(IsSet($_SERVER['HTTP_USER_AGENT']) && Preg_Match('/MSIE [0-8]{1}[^0-9]/i', $_SERVER['HTTP_USER_AGENT'])) Return True;
		Else Return False;
	}

	If(StrCmp($_SERVER['REQUEST_METHOD'], 'GET') === 0) {
		$IDX = (IsSet($_GET['idx']) && !Empty($_GET['idx'])) ? IntVal($_GET['idx']) : 0;
		$BID = (IsSet($_GET['id']) && !Empty($_GET['id'])) ? $_GET['id'] : Null;

		If ($IDX == 0 || Empty($BID)) Header('Location: /');

		$r = mysql_query("SELECT `FILE_NAME` FROM `T_BOARD` WHERE `IDX` = '{$IDX}' AND `BOARD_ID` = '{$BID}' ");
		$l = mysql_fetch_row($r);
		$filePath = __ROOT__ . '/files/' . $BID . '/' . $l[0];

		If(Is_File($filePath)) {
			$pass = True;

			If(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off');

			$name = (CheckLeaveIE() === True) ? RawURLEncode($l[0]) : $l[0];

			Header('Pragma: no-cache');
			Header('Expires: 0');
			Header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			Header('Last-Modified: '.GMDate('D, d M Y H:i:s', Filemtime($filePath)).' GMT');
			Header('Cache-Control: private', False);
			Header('Content-Type: application/force-download');
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