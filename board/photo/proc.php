<?php
	$uploadPath = $_SERVER['DOCUMENT_ROOT'] . _GLOBAL_FILE_PATH_ . '/' . $MASTER[0]['BOARD_ID'] . '/';
	$status = False;

	If ( $MODE != 'DR' ) {
		$WRITER = (!Empty($_POST['writer'])) ? $_POST['writer'] : null;
		$TITLE = (!Empty($_POST['title'])) ? $_POST['title'] : null;
		$CATEGORY = (!Empty($_POST['category'])) ? $_POST['category'] : null;
		$CONTENTS = (!Empty($_POST['CONTENTS'])) ? $_POST['CONTENTS'] : null;
		$PASSWD = (!Empty($_POST['passwd'])) ? $_POST['passwd'] : null;
		$NOTI = (!Empty($_POST['noti'])) ? $_POST['noti'] : null;

		$RETURN_MODE = SubStr($MODE, 0, 1);
		If(Empty($WRITER)) location(1, '이름을 입력해주세요.', "?id={$BID}&mode={$RETURN_MODE}&idx={$_POST['IDX']}");
		If(Empty($TITLE)) location(1, '게시물 제목을 입력해주세요.', "?id={$BID}&mode={$RETURN_MODE}&idx={$_POST['IDX']}");
		If(Empty($CONTENTS)) location(1, '게시물 내용을 입력해주세요.', "?id={$BID}&mode={$RETURN_MODE}&idx={$_POST['IDX']}");
		If(Empty($PASSWD) && $MODE != 'MR') location(1, '비밀번호를 입력해주세요.', "?id={$BID}&mode={$RETURN_MODE}&idx={$_POST['IDX']}");
	}

	If ($NOTI == 'Y') $NOTI = 1;
	Else $NOTI = 2;

	// 자동등록방지 체크
	If($MODE == 'WR' || $MODE == 'RR') {
		If($_SESSION['CAPTCHA_STRING'] != $_POST['captchaString']) {
			location(1, '자동등록방지 문자가 일치하지 않습니다.', '?id=' . $BID);
		}
	}

	
	// 작성자 정보
	$CREATE_ID = $_SESSION['HOME']['USER_NO'];
	$CREATE_IP = $_SERVER['REMOTE_ADDR'];
	If ( $MODE == 'WR' ) {
		If(isBoardAuth($_SESSION['HOME']['USER_AUTH'], $WRITE_LEVEL_ARRAY) == False) location(2, '권한이 없습니다.', '?id=' . $BID);	// 권한체크 - 등록

		// 신규 아이디 조회
		$DB->setProcName('SP_BOARD_NEXT_ID_SELECT');
		If ($DB->ExecuteProc()){
			$Result = $DB->get_fetch_assoc();
			$NEXT_ID = $Result[0]['NEXT_ID'];
		}

		// 신규 등록 초기값 설정
		$newSeq		= 1;
		$newLevel		= 0;
		$newGrp		= $NEXT_ID;
		$newUpIdx	= $NEXT_ID;

		// 표지이미지 파일 등록
		$upload = False;
		If(Is_Array($_FILES['m_file']['name'])) {
			If(!Is_Dir($uploadPath)) mkdir($uploadPath, 0755);

			ForEach($_FILES['m_file']['name'] AS $N => $NM) {
				If(!Empty($NM) && StrStr($NM, '.') !== False) {
					$fileSize = "{$_FILES['m_file']['size'][$N]}";
					$fileType = "{$_FILES['m_file']['type'][$N]}";

					$fn = StripSlashes(Trim($NM));
					$array = Explode('.', $fn);
					$fe = Array_Pop($array);

					Array_Splice($array, Count($array));

					$filename = Date('YmdHms') . '_' . generate_state() . '.' . $fe;
					$furl = $uploadPath . $filename;
					If(File_Exists($furl)) {
						While(File_Exists($furl)) {
							$filename = Date('YmdHms') . '_' . generate_state() . '.' . $fe;
							$furl = $uploadPath . $filename;
						}
					}

					If(Move_Uploaded_File($_FILES['m_file']['tmp_name'][$N], $furl)) {
						$gd = new ImageGD($furl);

						// 포토게시판 리스트용 썸네일 생성
						$gd->thumbnailImage(190, 140);
						$gd->write($uploadPath . 'm_thumb_' . $filename);

						// 메인페이지용 썸네일
						$gd->thumbnailImage(190, 140);
						$gd->write($uploadPath . 'i_thumb_' . $filename);

					} Else @Unlink($_FILES['m_file']['tmp_name'][$N]);
				}
			}
		}

		If ($upload === False) location(2, '표지 이미지파일 업로드에 실패하였습니다.', "?id={$BID}");

		$DB->setProcName('SP_BOARD_SAVE');
		$DB->bind_param('P_IDX',								$NEXT_ID,							_INT, 						20,				_PARAM_IN);
		$DB->bind_param('P_BOARD_ID',					$BID,									_VARCHAR, 				20,				_PARAM_IN);
		$DB->bind_param('P_GRP',								$newGrp,							_INT, 						11,				_PARAM_IN);
		$DB->bind_param('P_SEQ',								$newSeq,							_INT, 						11,				_PARAM_IN);
		$DB->bind_param('P_LVL',								$newLevel,						_INT, 						3,					_PARAM_IN);
		$DB->bind_param('P_UP_IDX',							$newUpIdx,						_INT, 						20,				_PARAM_IN);
		$DB->bind_param('P_NOTI',							$NOTI,								_INT, 						3,					_PARAM_IN);
		$DB->bind_param('P_TITLE',							$TITLE,								_VARCHAR, 				200,				_PARAM_IN);
		$DB->bind_param('P_CATEGORY',					$CATEGORY,						_VARCHAR, 				200,				_PARAM_IN);
		$DB->bind_param('P_CONTENTS',					$CONTENTS,						_VARCHAR, 				4000,			_PARAM_IN);
		$DB->bind_param('P_WRITER',						$WRITER,							_VARCHAR, 				50,				_PARAM_IN);
		$DB->bind_param('P_PASSWD',						$PASSWD,							_VARCHAR, 				50,				_PARAM_IN);
		$DB->bind_param('P_READ',							$READ,								_INT, 						11,				_PARAM_IN);
		$DB->bind_param('P_FILE_NAME',					$filename,						_VARCHAR, 				500,				_PARAM_IN);
		$DB->bind_param('P_REALFILE_NAME',			$fn,									_VARCHAR, 				500,				_PARAM_IN);
		$DB->bind_param('P_SECRET_YN',					$SECRET_YN,						_VARCHAR, 				1,					_PARAM_IN);
		$DB->bind_param('P_DEL_YN',						$DEL_YN,							_VARCHAR, 				1,					_PARAM_IN);
		$DB->bind_param('P_USER_NO',						$CREATE_ID,						_VARCHAR, 				20,				_PARAM_IN);
		$DB->bind_param('P_CREATE_ID',					$CREATE_ID,						_VARCHAR, 				50,				_PARAM_IN);
		$DB->bind_param('P_CREATE_IP',					$CREATE_IP,						_VARCHAR, 				30,				_PARAM_IN);

		If($DB->ExecuteProc()) {
			$status = True;
		}

		If ($MASTER[0]['FILE_COUNT'] > 0) {
			If(Is_Array($_FILES['FILE']['name'])) {
				If(!Is_Dir($uploadPath)) mkdir($uploadPath, 0755);

				ForEach($_FILES['FILE']['name'] AS $N => $NM) {
					If(!Empty($NM) && StrStr($NM, '.') !== False) {
						$fileSize = "{$_FILES['FILE']['size'][$N]}";
						$fileType = "{$_FILES['FILE']['type'][$N]}";

						$fn = StripSlashes(Trim($NM));
						$array = Explode('.', $fn);
						$fe = Array_Pop($array);

						Array_Splice($array, Count($array));

						$filename = Date('YmdHms') . '_' . generate_state() . '.' . $fe;
						$furl = $uploadPath . $filename;
						If(File_Exists($furl)) {
							While(File_Exists($furl)) {
								$filename = Date('YmdHms') . '_' . generate_state() . '.' . $fe;
								$furl = $uploadPath . $filename;
							}
						}

						If(Move_Uploaded_File($_FILES['FILE']['tmp_name'][$N], $furl)) {
							// 업로드 후 파일정보 INSERT
							$DB->setProcName('SP_WEB_BOARD_FILE_SAVE');
							$DB->bind_param('P_IDX', 								$NEXT_ID,									_INT, 			20,		_PARAM_IN);
							$DB->bind_param('P_BOARD_ID', 					$MASTER[0]['BOARD_ID'],			_VARCHAR,	20,		_PARAM_IN);
							$DB->bind_param('P_FILE_NAME', 					$filename,									_VARCHAR, 	500,		_PARAM_IN);
							$DB->bind_param('P_FILE_REALNAME', 			$fn,											_VARCHAR, 	500,		_PARAM_IN);
							$DB->bind_param('P_FILE_SIZE', 						$fileSize,									_VARCHAR, 	255,		_PARAM_IN);
							$DB->bind_param('P_FILE_TYPE', 					$fileType,									_VARCHAR, 	200,		_PARAM_IN);
							$DB->bind_param('P_FILE_DESCRIPTION', 		$fileDescription,							_VARCHAR, 	800,		_PARAM_IN);

							If($DB->ExecuteProc()) {
								$upload = true;
							} Else {
								Echo $DB->get_error();
								Exit;
							}
						} Else  @Unlink($_FILES['FILE']['tmp_name'][$N]);
					}
				}
			}
		}

		If($status == True) location(2, '등록되었습니다.', "?id={$BID}");
		Else location(2, '오류가 발생하였습니다. 지속적으로 오류가 발생되는 경우 관리자에게 문의하십시오.', "?id={$BID}");
	}
	
	If ( $MODE == 'MR' ) {
		If(isBoardAuth($_SESSION['HOME']['USER_AUTH'], $WRITE_LEVEL_ARRAY) == False) location(2, '권한이 없습니다.', '?id=' . $BID);	// 권한체크 - 수정

		$IDX = (!Empty($_POST['idx'])) ? IntVal($_POST['idx']) : Null;

		If(!Empty($IDX)) {
			// 원본글 정보조회
			$DB->setProcName('SP_WEB_BOARD_DETAIL_SELECT');
			$DB->bind_param('P_IDX', 			$IDX,				_INT, 		11,			_PARAM_IN);

			If ($DB->ExecuteProc()) {
				$Result = $DB->get_fetch_assoc();

				If ($Result) {
					$GRP 		= $Result[0]['GRP'];
					$SEQ 		= $Result[0]['GRP'];
					$LVL 		= $Result[0]['LVL'];
					$UP_IDX	= $Result[0]['UP_IDX'];
				}
			}
		}

		If($Result[0]['IDX'] == "") location(2, "존재하지 않는 게시글입니다.", "?id={$BID}");

		// 표지이미지 파일 등록
		$upload = False;
		If(Is_Array($_FILES['m_file']['name'])) {
			If(!Is_Dir($uploadPath)) mkdir($uploadPath, 0755);

			ForEach($_FILES['m_file']['name'] AS $N => $NM) {
				If(!Empty($NM) && StrStr($NM, '.') !== False) {
					$fileSize = "{$_FILES['m_file']['size'][$N]}";
					$fileType = "{$_FILES['m_file']['type'][$N]}";

					$fn = StripSlashes(Trim($NM));
					$array = Explode('.', $fn);
					$fe = Array_Pop($array);

					Array_Splice($array, Count($array));

					$filename = Date('YmdHms') . '_' . generate_state() . '.' . $fe;
					$furl = $uploadPath . $filename;
					If(File_Exists($furl)) {
						While(File_Exists($furl)) {
							$filename = Date('YmdHms') . '_' . generate_state() . '.' . $fe;
							$furl = $uploadPath . $filename;
						}
					}

					If(Move_Uploaded_File($_FILES['m_file']['tmp_name'][$N], $furl)) {
						$gd = new ImageGD($furl);

						// 포토게시판 리스트용 썸네일 생성
						$gd->thumbnailImage(190, 140);
						$gd->write($uploadPath . 'm_thumb_' . $filename);

						// 메인페이지용 썸네일
						$gd->thumbnailImage(190, 140);
						$gd->write($uploadPath . 'i_thumb_' . $filename);

					} Else @Unlink($_FILES['m_file']['tmp_name'][$N]);
				}
			}
		}

		If ($upload === False) location(2, '표지 이미지파일 업로드에 실패하였습니다.', "?id={$BID}");

		$DB->setProcName('SP_BOARD_SAVE');
		$DB->bind_param('P_IDX',								$IDX,								_INT, 						20,				_PARAM_IN);
		$DB->bind_param('P_BOARD_ID',					$BID,									_VARCHAR, 				20,				_PARAM_IN);
		$DB->bind_param('P_GRP',								$GRP,								_INT, 						11,				_PARAM_IN);
		$DB->bind_param('P_SEQ',								$SEQ,								_INT, 						11,				_PARAM_IN);
		$DB->bind_param('P_LVL',								$LVL,								_INT, 						3,					_PARAM_IN);
		$DB->bind_param('P_UP_IDX',							$UP_IDX,							_INT, 						20,				_PARAM_IN);
		$DB->bind_param('P_NOTI',							$NOTI,								_INT, 						3,					_PARAM_IN);
		$DB->bind_param('P_TITLE',							$TITLE,								_VARCHAR, 				200,				_PARAM_IN);
		$DB->bind_param('P_CATEGORY',					$CATEGORY,						_VARCHAR, 				200,				_PARAM_IN);
		$DB->bind_param('P_CONTENTS',					$CONTENTS,						_VARCHAR, 				4000,			_PARAM_IN);
		$DB->bind_param('P_WRITER',						$WRITER,							_VARCHAR, 				50,				_PARAM_IN);
		$DB->bind_param('P_PASSWD',						$PASSWD,							_VARCHAR, 				50,				_PARAM_IN);
		$DB->bind_param('P_READ',							$READ,								_INT, 						11,				_PARAM_IN);
		$DB->bind_param('P_FILE_NAME',					$filename,						_VARCHAR, 				500,				_PARAM_IN);
		$DB->bind_param('P_REALFILE_NAME',			$fn,				_VARCHAR, 				500,				_PARAM_IN);
		$DB->bind_param('P_SECRET_YN',					$SECRET_YN,						_VARCHAR, 				1,					_PARAM_IN);
		$DB->bind_param('P_DEL_YN',						$DEL_YN,							_VARCHAR, 				1,					_PARAM_IN);
		$DB->bind_param('P_USER_NO',						$CREATE_ID,						_VARCHAR, 				20,				_PARAM_IN);
		$DB->bind_param('P_CREATE_ID',					$CREATE_ID,						_VARCHAR, 				50,				_PARAM_IN);
		$DB->bind_param('P_CREATE_IP',					$CREATE_IP,						_VARCHAR, 				30,				_PARAM_IN);

		If($DB->ExecuteProc()) {
			$status = True;
		}

		//파일삭제
		For ( $i = 1; $i <= $MASTER[0]['FILE_COUNT']; $i++ ) {
			$SEQ = Null;
			If(!Empty($_POST['del_file'.$i])) {
				$SEQ = $_POST['del_file'.$i];
				$DB->setProcName('SP_WEB_BOARD_FILE_DETAIL_SELECT');
				$DB->bind_param('P_IDX', 				$IDX,				_INT, 		20,			_PARAM_IN);
				$DB->bind_param('P_SEQ', 			$SEQ,				_INT, 		11,			_PARAM_IN);

				If ($DB->ExecuteProc()) {
					$Result = $DB->get_fetch_assoc();

					If ($Result) {
						$DEL_FILENAME 		= $Result[0]['FILE_NAME'];
						$DEL_FILE = $uploadPath . $DEL_FILENAME;
						$DEL_FILE_M = $uploadPath. 'm_thumb_' . $DEL_FILENAME;
						$DEL_FILE_I = $uploadPath. 'i_thumb_' . $DEL_FILENAME;
						If(file_Exists($DEL_FILE) && Is_File($DEL_FILE)) @Unlink($DEL_FILE);
						If(file_Exists($DEL_FILE_M) && Is_File($DEL_FILE_M)) @Unlink($DEL_FILE_M);
						If(file_Exists($DEL_FILE_I) && Is_File($DEL_FILE_I)) @Unlink($DEL_FILE_I);

						$DB->setProcName('SP_WEB_BOARD_FILE_DELETE');
						$DB->bind_param('P_IDX', 				$IDX,				_INT, 		20,			_PARAM_IN);
						$DB->bind_param('P_SEQ', 			$SEQ,				_INT, 		11,			_PARAM_IN);
						$DB->ExecuteProc();
					}
				}
			}
		}
		
		If ($MASTER[0]['FILE_COUNT'] > 0) {
			If(Is_Array($_FILES['FILE']['name'])) {
				If(!Is_Dir($uploadPath)) mkdir($uploadPath, 0755);

				ForEach($_FILES['FILE']['name'] AS $N => $NM) {
					If(!Empty($NM) && StrStr($NM, '.') !== False) {
						$fileSize = "{$_FILES['FILE']['size'][$N]}";
						$fileType = "{$_FILES['FILE']['type'][$N]}";

						$fn = StripSlashes(Trim($NM));
						$array = Explode('.', $fn);
						$fe = Array_Pop($array);

						Array_Splice($array, Count($array));

						$filename = Date('YmdHms') . '_' . generate_state() . '.' . $fe;
						$furl = $uploadPath . $filename;
						If(File_Exists($furl)) {
							While(File_Exists($furl)) {
								$filename = Date('YmdHms') . '_' . generate_state() . '.' . $fe;
								$furl = $uploadPath . $filename;
							}
						}

						If(Move_Uploaded_File($_FILES['FILE']['tmp_name'][$N], $furl)) {
							// 업로드 후 파일정보 INSERT
							$DB->setProcName('SP_WEB_BOARD_FILE_SAVE');
							$DB->bind_param('P_IDX', 								$IDX,										_INT, 			20,		_PARAM_IN);
							$DB->bind_param('P_BOARD_ID', 					$BID,											_VARCHAR,	20,		_PARAM_IN);
							$DB->bind_param('P_FILE_NAME', 					$filename,									_VARCHAR, 	500,		_PARAM_IN);
							$DB->bind_param('P_FILE_REALNAME', 			$fn,											_VARCHAR, 	500,		_PARAM_IN);
							$DB->bind_param('P_FILE_SIZE', 						$fileSize,									_VARCHAR, 	255,		_PARAM_IN);
							$DB->bind_param('P_FILE_TYPE', 					$fileType,									_VARCHAR, 	200,		_PARAM_IN);
							$DB->bind_param('P_FILE_DESCRIPTION', 		$fileDescription,							_VARCHAR, 	800,		_PARAM_IN);

							$DB->ExecuteProc();
						} Else  @Unlink($_FILES['FILE']['tmp_name'][$N]);
					}
				}
			}
		}

		If($status == True) location(2, '수정되었습니다.', "?id={$BID}&mode=V&idx={$IDX}&srhctgr={$SEARCH_TYPE}&keyword={$SEARCH_KEYWORD}&p={$page}");
		Else location(2, '게시글 수정에 실패하였습니다. 관리자에게 문의하십시오.', "?id={$BID}&mode=V&idx={$IDX}&srhctgr={$SEARCH_TYPE}&keyword={$SEARCH_KEYWORD}&category={$SEARCH_CATEGORY}&p={$page}");
	}

	If ( $MODE == 'RR' ) {}

	If ( $MODE == 'DR' ) {
		$IDX = (!Empty($_GET['idx'])) ? IntVal($_GET['idx']) : Null;

		If (Empty($IDX)) location(2, "게시글 번호가 존재하지 않습니다.", "?id={$BID}");

		If (isBoardAuth($_SESSION['HOME']['USER_AUTH'], $DELETE_LEVEL_ARRAY) == False) {		// 권한체크 - 삭제
			If($_POST['passwd'] == "") {				// 권한이 없다면, 비밀번호 입력 요구, 비밀번호를 입력 받아야 통과
				location(2, '', "?id={$BID}&mode=PW&idx={$IDX}&rmode=DR");
			}
		}

		// 게시글 정보 조회
		$DB->setProcName('SP_BOARD_DETAIL_SELECT');
		$DB->bind_param('P_IDX', 			$IDX,				_INT, 		11,			_PARAM_IN);

		If ($DB->ExecuteProc()) {
			$Result = $DB->get_fetch_assoc();

			If (!$Result) location(2, '이미 삭제되었거나 존재하지 않는 게시글입니다.', "?id={$BID}");

		} Else location(2, '삭제할 게시글 정보 조회에 실패하였습니다. 관리자에게 문의하십시오.', "?id={$BID}");

		If (isBoardAuth($_SESSION['HOME_USER_AUTH'], $DELETE_LEVEL_ARRAY) == False) {		// 권한체크 - 삭제
			// 권한이 없다면 비밀번호를 확인함
			If($_POST['passwd'] != $Result[0]['PASSWD']) {
				location(2, '비밀번호가 일치하지 않습니다.', "?id={$BID}&mode=PW&idx={$IDX}&rmode=DR");
			}
		}

		// 댓글존재여부 확인 (댓글이 존재하는 경우 삭제불가)
		$replyStatus = False;
		$DB->setProcName('SP_BOARD_CHILD_REPLY_SELECT');
		$DB->bind_param('P_IDX', 						$IDX,						_INT, 				11,				_PARAM_IN);
		$DB->bind_param('P_START',					0,								_INT, 				11,				_PARAM_IN);
		$DB->bind_param('P_END', 					0,								_INT, 				11,				_PARAM_IN);

		If ($DB->ExecuteProc()) {
			$Result = $DB->get_fetch_assoc();

			If ($Result) {
				$REPLY_COUNT 	= $Result[0]['COUNT'];

				If ($REPLY_COUNT > 0) location(2, '답변이 등록된 게시글은 삭제하실 수 없습니다.', "?id={$BID}&mode=V&idx={$IDX}&srhctgr={$SEARCH_TYPE}&keyword={$SEARCH_KEYWORD}&category={$SEARCH_CATEGORY}&p={$page}");
			}
		} Else location(2, '삭제할 게시글 정보 조회에 실패하였습니다. 관리자에게 문의하십시오.', "?id={$BID}");

		$DB->setProcName('SP_BOARD_FILE_LIST_SELECT');
		$DB->bind_param('P_IDX', 						$IDX,								_INT, 		20,			_PARAM_IN);
		If ($DB->ExecuteProc()) {
			$result = $DB->get_fetch_assoc();

			Foreach($result as $fileList) {
				$DEL_FILENAME 		= $fileList['FILE_NAME'];
				$DEL_FILE = $uploadPath . $DEL_FILENAME;
				If(file_Exists($DEL_FILE) && Is_File($DEL_FILE)) @Unlink($DEL_FILE);
			}
		} Else location(2, '삭제할 게시글의 첨부파일 정보 조회에 실패하였습니다. 관리자에게 문의하십시오.', "?id={$BID}");

		$DB->setProcName('SP_BOARD_FILE_LIST_DELETE');
		$DB->bind_param('P_IDX', 						$IDX,								_INT, 		20,			_PARAM_IN);
		$DB->ExecuteProc();

		$DB->setProcName('SP_BOARD_DELETE');
		$DB->bind_param('P_IDX', 						$IDX,								_INT, 		20,			_PARAM_IN);

		If($DB->ExecuteProc()) location(2, '삭제되었습니다.', "?id={$BID}");
		Else location(2, '게시글 삭제에 실패하였습니다. 관리자에게 문의하십시오.', "?id={$BID}");
	}
?>