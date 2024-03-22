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

		//���۵� �̹��� ���� Ȯ��
		If (!Empty($Result[0]['FILE_NAME'])) {

			//�̹��� ��ü��θ� ������ �̹�����
			$imagePath = __ROOT__ . _GLOBAL_FILE_PATH_ . '/' . $Result[0]['BOARD_ID'] . '/' . $Result[0]['FILE_NAME'];

			//�Ѿ�� �̹�������� ���翩�ο� ���Ͽ��� Ȯ��
			If (File_Exists($imagePath) && is_file($imagePath)) {

				//�Ѿ�� ���� Ȯ���� ����
				$tmp_name = pathinfo($imagePath);
				$ext = strToLower($tmp_name['extension']);

				//������ Ȯ���ڸ� �����ֵ��� ���͸�
				if($ext == 'jpeg' || $ext == 'jpg' || $ext='gif' || $ext='png' || $ext='bmp') {
					$status = True;

					//�̹��� ũ�������� ����� ����
					$img_info = getimagesize($imagePath);
					$filesize = filesize($imagePath);

					//�̹��� ������ ���� �������
					header("Content-Type: {$img_info['mime']}\n");
					header("Content-Disposition: inline;filename='$FILE_NAME'\n");
					header("Content-Length: $filesize\n");

					ob_clean();
					flush();

					//�̹��� ������ �о����
					readfile($imagePath);

				}
			}
		}
	}

	If ($status == False) {
		//�̹��� ��ü��θ� ������ �̹�����
		$imagePath = __ROOT__ . '/res/img/noimg.png';

		//�̹��� ũ�������� ����� ����
		$img_info = getimagesize($imagePath);
		$filesize = filesize($imagePath);

		//�̹��� ������ ���� �������
		header("Content-Type: {$img_info['mime']}\n");
		header("Content-Disposition: inline;filename='noimg.png'\n");
		header("Content-Length: $filesize\n");
		
		ob_clean();
		flush();

		//�̹��� ������ �о����
		readfile($imagePath);
	}
?>