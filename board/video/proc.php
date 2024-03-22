<?php
	Header("Cache-Control:no-cache");
	ini_set("gd.jpeg_ignore_warning", 1);
	/*
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
	*/

	Require_Once __ROOT__ . '/inc/form.class.php';
	Require_Once __ROOT__ . '/inc/thumbnail.class.php';

	$form = new form;
	$_POST = $form->cleaning($_POST);
	$_POST = array_map("mysql_real_escape_string", $_POST);
	$_GET = $form->cleaning($_GET);
	$_GET = array_map("mysql_real_escape_string", $_GET);

	$page = (IsSet($_POST['p']) && !Empty($_POST['p'])) ? IntVal($_POST['p']) : 1;
	$srhctgr = $_POST['srhctgr'];
	$keyword = $_POST['keyword'];

	$filePath = $_SERVER['DOCUMENT_ROOT'] . "/files/{$BID}/";
	If(!Is_Dir($filePath)) mkdir($filePath, 0777);

	If ( $MODE != 'DR' ) {
		$WRITER = (!Empty($_POST['writer'])) ? $_POST['writer'] : null;
		$TITLE = (!Empty($_POST['title'])) ? $_POST['title'] : null;
		$CONTENTS = (!Empty($_POST['contents'])) ? $_POST['contents'] : null;

		If(Empty($WRITER)) { Echo "<script>alert('이름을 입력해주세요.');history.back();</script>"; Exit; }
		If(Empty($TITLE)) { Echo "<script>alert('게시물 제목을 입력해주세요.');history.back();</script>"; Exit; }
		If(Empty($CONTENTS)) { Echo "<script>alert('게시물 내용을 입력해주세요.');history.back();</script>"; Exit; }
	}

	If ( $MODE == 'WR' ) {

		//표지이미지 파일등록
		$upload = False;
		ForEach($_FILES['m_file']['name'] AS $N => $NM) {
			If(!Empty($NM) && StrStr($NM, '.') !== False) {
				$fn = StripSlashes(Trim($NM));
				$array = Explode('.', $fn);
				$fe = Array_Pop($array);

				Array_Splice($array, Count($array));

				$fn_tmp = Implode('.', $array);
				$furl = $filePath.$fn;

				If(File_Exists($furl)) {
					$dup = 0;
					While(File_Exists($furl)) {
						$dup++;
						$fn = $fn_tmp.'['.$dup.'].'.$fe;
						$furl = $filePath.$fn;
					}
				}

				If(Move_Uploaded_File($_FILES['m_file']['tmp_name'][$N], $furl)) {
					$gd = new ImageGD($furl);

					// 포토게시판 리스트용 썸네일 생성
					$gd->thumbnailImage(170, 110);
					$gd->write($filePath . 'm_thumb_' . $fn);

					// 메인페이지용 썸네일
					$gd->thumbnailImage(136, 103);
					$gd->write($filePath . 'i_thumb_' . $fn);

					$upload = True;
				} Else {
					@Unlink($_FILES['m_file']['tmp_name'][$N]);
				}
			} Else {
				Echo "<script>alert('표지 이미지파일을 첨부해주세요.'); history.back();</script>"; Exit;
			}
		}

		If ($upload === False) { Echo "<script>alert('표지 이미지파일 업로드에 실패하였습니다.'); history.back();</script>"; Exit; }

		$sql = "SELECT IFNULL(MAX(IDX), 0) + 1 FROM `T_BOARD` ";
		$r = mysql_query($sql);
		$c = mysql_fetch_row($r);
		mysql_free_result($r);
		$new_idx = $c[0];

		if(mysql_query("INSERT INTO `T_BOARD` (`IDX`, `GROUP`, `BOARD_ID`, `TITLE`, `CONTENTS`, `WRITER`, `REGDATE`, `FILE_NAME`, `CREATE_ID`, `CREATE_IP`) VALUES ('{$new_idx}', '{$new_idx}', '{$BID}', '{$TITLE}', '{$CONTENTS}', '{$WRITER}', NOW(), '{$fn}', '{$_SESSION['valid_id']}', '{$_SERVER['REMOTE_ADDR']}') ")) {
			$fileExtCheck = True;
			If(Is_Array($_FILES['file']['name'])) {
				// 첨부파일등록
				ForEach($_FILES['file']['name'] AS $N => $NM) {
					If(!Empty($NM) && StrStr($NM, '.') !== False) {
						$fn = StripSlashes(Trim($NM));
						$array = Explode('.', $fn);
						$fe = Array_Pop($array);

						If(strToLower($fe) == 'wmv' || strToLower($fe) == 'mp4') {
							Array_Splice($array, Count($array));

							$fn_tmp = Implode('.', $array);
							$furl = $filePath.$fn;

							If(File_Exists($furl)) {
								$dup = 0;
								While(File_Exists($furl)) {
									$dup++;
									$fn = $fn_tmp.'['.$dup.'].'.$fe;
									$furl = $filePath.$fn;
								}
							}

							If(Move_Uploaded_File($_FILES['file']['tmp_name'][$N], $furl)) {
								if(!mysql_query("insert into `T_BOARD_FILE` (`IDX`, `BOARD_ID`, `FILE_NAME`, `FILE_SIZE`, `FILE_TYPE`) values ('{$new_idx}', '{$BID}', '{$fn}', '{$_FILES['file']['size'][$N]}', '{$fe}')")) @Unlink($furl);
							} Else  @Unlink($_FILES['file']['tmp_name'][$N]);
						} Else {
							$fileExtCheck = False;
							@Unlink($_FILES['file']['tmp_name'][$N]);
						}
					}
				}
			}
			If($fileExtCheck == False) {
				alert("등록되었습니다. 첨부파일 중 확장자가 wmv, mp4가 아닌 파일은 업로드되지 않습니다.");
			} Else {
				alert("등록되었습니다.");
			}
			Echo "<meta http-equiv=\"refresh\" content=\"0; url=/board/?id={$BID}\">";
			Exit;
		} Else {
			alert("오류가 발생하였습니다.");
			Echo "<meta http-equiv=\"refresh\" content=\"0; url=/board/?id={$BID}\">";
			Exit;
		}
	}

	If ( $MODE == 'MR' ) {
		$IDX = (!Empty($_POST['idx'])) ? IntVal($_POST['idx']) : Null;

		If (Empty($IDX)) location(2, "존재하지 않는 게시글입니다.", "/board/?id={$BID}");

		If (mysql_query("UPDATE `T_BOARD` SET `TITLE` = '{$TITLE}', `CONTENTS` = '{$CONTENTS}', `WRITER` = '{$WRITER}', `UPDATE_ID` = '{$_SESSION['valid_id']}', `UPDATE_DT` = NOW(), `UPDATE_IP` = '{$_SERVER['REMOTE_ADDR']}' WHERE `IDX` = '{$IDX}' ")) {
			//표지이미지 파일삭제
			If($_POST['del_m_file'] == 'Y') {
				$r = mysql_query("SELECT `FILE_NAME` FROM `T_BOARD` WHERE `IDX` = '{$IDX}' ");
				while($l = mysql_fetch_assoc($r)) {
					$file = $filePath . $l['FILE_NAME'];
					$thumb = $filePath . 'm_thumb_' . $l['FILE_NAME'];		// 리스트용 썸네일
					$thumb2 = $filePath . 'i_thumb_' . $l['FILE_NAME'];
					
					If(File_Exists($file) && Is_File($file)) {
						If(Unlink($file)) {
							If(File_Exists($thumb) && Is_File($thumb)) {
								@Unlink($thumb);
								@Unlink($thumb2);
							}
						}
					}

					mysql_query("UPDATE `T_BOARD` SET `FILE_NAME` = '' WHERE `IDX` = '{$IDX}' ");
				}
			}

			If(Is_Array($_FILES['m_file']['name'])) {
				//표지이미지 파일등록
				ForEach($_FILES['m_file']['name'] AS $N => $NM) {
					If(!Empty($NM) && StrStr($NM, '.') !== False) {
						//기존 표지이미지 파일삭제
						$r = mysql_query("SELECT `FILE_NAME` FROM `T_BOARD` WHERE `IDX` = '{$IDX}' ");
						while($l = mysql_fetch_assoc($r)) {
							$file = $filePath . $l['FILE_NAME'];
							$thumb = $filePath . 'm_thumb_' . $l['FILE_NAME'];		// 리스트용 썸네일
							$thumb2 = $filePath . 'i_thumb_' . $l['FILE_NAME'];		// 리스트용 썸네일

							If(File_Exists($file) && Is_File($file)) {
								If(Unlink($file)) {
									If(File_Exists($thumb) && Is_File($thumb)) {
										@Unlink($thumb);
										@Unlink($thumb2);
									}
								}
							}

							mysql_query("UPDATE `T_BOARD` SET `FILE_NAME` = '' WHERE `IDX` = '{$IDX}' ");
						}

						$fn = StripSlashes(Trim($NM));
						$array = Explode('.', $fn);
						$fe = Array_Pop($array);

						Array_Splice($array, Count($array));

						$fn_tmp = Implode('.', $array);
						$furl = $filePath.$fn;

						If(File_Exists($furl)) {
							$dup = 0;
							While(File_Exists($furl)) {
								$dup++;
								$fn = $fn_tmp.'['.$dup.'].'.$fe;
								$furl = $filePath.$fn;
							}
						}

						If(Move_Uploaded_File($_FILES['m_file']['tmp_name'][$N], $furl)) {
							if(!mysql_query("UPDATE `T_BOARD` SET `FILE_NAME` = '{$fn}' WHERE `IDX` = '{$IDX}' ")) {
								@Unlink($furl);
							} Else {
								$gd = new ImageGD($furl);

								// 포토게시판 리스트용 썸네일 생성
								$gd->thumbnailImage(170, 110);
								$gd->write($filePath . 'm_thumb_' . $fn);

								$gd->thumbnailImage(136, 103);
								$gd->write($filePath . 'i_thumb_' . $fn);
							}
						} Else @Unlink($_FILES['m_file']['tmp_name'][$N]);
					}
				}

			}

			If(Is_Array($_FILES['file']['name'])) {

				//파일삭제
				For ( $i = 1; $i <= $FILE_COUNT; $i++ ) {
					$SEQ = Null;
					If(!Empty($_POST['del_file'.$i])) {
						$SEQ = $_POST['del_file'.$i];
						$r = mysql_query("SELECT `SEQ`, `FILE_NAME` FROM `T_BOARD_FILE` WHERE `IDX` = '{$IDX}' AND `SEQ` = '{$SEQ}' ");
						while($l = mysql_fetch_assoc($r)) {

							$file = $filePath . $l['FILE_NAME'];
							//$thumb = $filePath . 'thumb_' . $l['FILE_NAME'];

							If(File_Exists($file) && Is_File($file)) {
								If(Unlink($file)) {
									//If(File_Exists($thumb) && Is_File($thumb)) @Unlink($thumb);
								}
							}

							mysql_query("DELETE FROM `T_BOARD_FILE` WHERE `IDX` = '{$IDX}' AND `SEQ` = '{$l['SEQ']}' ");
						}
					}
				}

				//파일등록
				$i = 1;
				ForEach($_FILES['file']['name'] AS $N => $NM) {
					If(!Empty($NM) && StrStr($NM, '.') !== False) {
						// 기존파일삭제
						$r = mysql_query("SELECT `FILE_NAME` FROM T_BOARD_FILE WHERE `IDX` = '{$IDX}' AND `SEQ` = '{$i}' ");
						$fileCnt = mysql_num_rows($r);
						If( $fileCnt > 0 ){
							$c = mysql_fetch_assoc($r);

							$file = $filePath . $c['FILE_NAME'];
							//$thumb = $filePath . 'thumb_' . $c['FILE_NAME'];

							If(File_Exists($file) && Is_File($file)) {
								If(Unlink($file)) {
									//If(File_Exists($thumb) && Is_File($thumb)) @Unlink($thumb);
								}
							}

							mysql_query("DELETE FROM `T_BOARD_FILE` WHERE `IDX` = '{$IDX}' AND `SEQ` = '{$i}' ");
						}
						mysql_free_result($r);

						$fn = StripSlashes(Trim($NM));
						$array = Explode('.', $fn);
						$fe = Array_Pop($array);

						Array_Splice($array, Count($array));

						$fn_tmp = Implode('.', $array);
						$furl = $filePath.$fn;

						If(File_Exists($furl)) {
							$dup = 0;
							While(File_Exists($furl)) {
								$dup++;
								$fn = $fn_tmp.'['.$dup.'].'.$fe;
								$furl = $filePath.$fn;
							}
						}

						If(Move_Uploaded_File($_FILES['file']['tmp_name'][$N], $furl)) {
							if(!mysql_query("insert into `T_BOARD_FILE` (`IDX`, `BOARD_ID`, `FILE_NAME`, `FILE_SIZE`, `FILE_TYPE`) values ('{$IDX}', '{$BID}', '{$fn}', '{$_FILES['file']['size'][$N]}', '{$fe}')")) @Unlink($furl);
						} Else @Unlink($_FILES['file']['tmp_name'][$N]);
					}

					$i++;
				}
			}

			alert("수정되었습니다.");
			Echo "<meta http-equiv=\"refresh\" content=\"0; url=/board/?id={$BID}&amp;mode=V&amp;idx={$IDX}&amp;srhctgr={$srhctgr}&amp;keyword={$keyword}&amp;p={$page}\">";
			Exit;
		} Else {
			alert("오류가 발생하였습니다.");
			Echo "<meta http-equiv=\"refresh\" content=\"0; url=/board/?id={$BID}&amp;mode=V&amp;idx={$IDX}&amp;srhctgr={$srhctgr}&amp;keyword={$keyword}&amp;p={$page}\">";
			Exit;
		}

	}

	If ( $MODE == 'RR' ) {

	}

	If ( $MODE == 'DR' ) {
		$IDX = (!Empty($_GET['idx'])) ? IntVal($_GET['idx']) : Null;

		If (Empty($IDX)) location(2, "게시글 번호가 존재하지 않습니다.", "/board/id={$BID}");

		// 표지이미지 파일삭제
		$r = mysql_query("select `FILE_NAME` FROM `T_BOARD` WHERE `IDX` = '{$IDX}' ");
		while($l = mysql_fetch_assoc($r)) {
			$del = False;
			$file = $filePath . $l['FILE_NAME'];
			$thumb = $filePath . 'm_thumb_' . $l['FILE_NAME'];		// 리스트용 썸네일
			$thumb2 = $filePath . 'i_thumb_' . $l['FILE_NAME'];		// 리스트용 썸네일

			If(File_Exists($file) && Is_File($file)) {
				If(Unlink($file)) {
					If(File_Exists($thumb) && Is_File($thumb)) @Unlink($thumb);
					@Unlink($thumb2);
				}
			}
		}

		// 첨부파일 삭제
		$r = mysql_query("select `SEQ`, `FILE_NAME` from `T_BOARD_FILE` where `IDX` = '{$IDX}'");
		while($l = mysql_fetch_assoc($r)) {
			$del = False;
			$file = $filePath . $l['FILE_NAME'];
			//$thumb = $filePath . $l['FILE_NAME'];
			If(File_Exists($file) && Is_File($file)) {
				If(Unlink($file)) {
					//If(File_Exists($thumb) && Is_File($thumb)) @Unlink($thumb);
					$del = True;
				}
			} Else $del = True;

			If($del === True) mysql_query("delete from `T_BOARD_FILE` where `IDX` = '{$IDX}' and `SEQ` = '{$l['SEQ']}' ");
		}

		mysql_free_result($r);

		$r = mysql_query("select count(*) from `T_BOARD_FILE` where `IDX` = '{$IDX}' ");
		$c = mysql_fetch_row($r);

		if($c[0] == 0) {
			mysql_query("DELETE FROM `T_BOARD` WHERE `IDX` = '{$IDX}'");
		}

		Echo "<script>alert('삭제되었습니다.');</script>";
		Echo "<meta http-equiv=\"refresh\" content=\"0; url=/board/?id={$BID}\">";
		Exit;
	}
?>