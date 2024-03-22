<?php
	
	// 권한체크
	If($MODE == 'W' || $MODE == 'M') { If(isBoardAuth($_SESSION['HOME']['USER_AUTH'], $WRITE_LEVEL_ARRAY) == False) location(2, '권한이 없습니다.', '?id=' . $BID); }	// 등록 및 수정
	If($MODE == 'R') { If(isBoardAuth($_SESSION['HOME']['USER_AUTH'], $REPLY_LEVEL_ARRAY) == False) location(2, '권한이 없습니다.', '?id=' . $BID); }	// 답변
	
	If($MODE == 'M') {
		$IDX = (!Empty($_GET['idx'])) ? IntVal($_GET['idx']) : Null;

		$status = False;
		$DB->setProcName('SP_WEB_BOARD_DETAIL_SELECT');
		$DB->bind_param('IDX', 			$IDX,				_INT, 		11,			_PARAM_IN);

		If ($DB->ExecuteProc()) {
			$Result = $DB->get_fetch_assoc();

			If ($Result) {
				$IDX							= $Result[0]['IDX'];
				$GRP						= $Result[0]['GRP'];
				$SEQ						= $Result[0]['SEQ'];
				$LVL							= $Result[0]['LVL'];
				$UP_IDX					= $Result[0]['UP_IDX'];
				$NOTI 						= $Result[0]['NOTI'];
				$TITLE 						= $Result[0]['TITLE'];
				$CATEGORY				= $Result[0]['CATEGORY'];
				$CONTENTS 				= $Result[0]['CONTENTS'];
				$WRITER	 				= $Result[0]['WRITER'];
				$PASSWD 					= $Result[0]['PASSWD'];
				$READ 						= $Result[0]['READ'];
				$FILE_NAME 				= $Result[0]['FILE_NAME'];
				$REALFILE_NAME 		= $Result[0]['REALFILE_NAME'];
				$SECRET_YN 				= $Result[0]['SECRET_YN'];
				$DEL_YN 					= $Result[0]['DEL_YN'];
				$CREATE_ID 				= $Result[0]['CREATE_ID'];
				$CREATE_DT 				= $Result[0]['CREATE_DT'];
				$CREATE_IP 				= $Result[0]['CREATE_IP'];
				$UPDATE_ID 				= $Result[0]['UPDATE_ID'];
				$UPDATE_DT 			= $Result[0]['UPDATE_DT'];
				$UPDATE_IP 				= $Result[0]['UPDATE_IP'];

				$status = True;
			}
		}

		If($status == False) location(2, '이미 삭제되었거나 존재하지 않는 게시글입니다.', '/');

		If($_SESSION['valid_level'] != 1){
			If(Empty($_POST['passwd'])) {
				Echo "<meta http-equiv=\"refresh\" content=\"0; url='?id={$BID}&amp;mode=PW&amp;idx={$IDX}&amp;rmode=M'\">";
				Exit;
			} Else {
				If ($_POST['passwd'] != $Result[0]['PASSWD']){ 
					Echo "<script>alert('비밀번호가 일치하지 않습니다.');</script>";
					Echo "<meta http-equiv=\"refresh\" content=\"0; url='?id={$BID}&amp;mode=PW&amp;idx={$IDX}&amp;rmode=M'\">";
					Exit;
				}
			}
		}

	}ElseIf ($MODE == 'R') {
		$IDX = (!Empty($_GET['idx'])) ? IntVal($_GET['idx']) : Null;
		$DB->setProcName('SP_WEB_BOARD_DETAIL_SELECT');
		$DB->bind_param('IDX', 			$IDX,				_INT, 		11,			_PARAM_IN);

		If ($DB->ExecuteProc()) {
			$Result = $DB->get_fetch_assoc();
			$CONTENTS = $Result[0]['CONTENTS'];
			$CONTENTS = "<br /><br /><br /><br />=============== 이전글 ===============<br /><br /><br />" . $CONTENTS;
		}
		
		If($Result[0]['IDX'] == "") location(2, "원본글이 삭제되었거나 존재하지 않습니다.", "/board/?id={$BID}");
		
		$WRITER = $_SESSION['HOME_USER_NM'];
	}Else{
		$WRITER = $_SESSION['HOME_USER_NM'];
	}
?>
						<div id="CDIBoardWrite">
							* 표시는 필수 입력사항입니다.<br />
							<form method="post" id="write_form" name="write_form" enctype="multipart/form-data" action="?id=<?php Echo htmlspecialchars($BID); ?>&mode=<?php Echo htmlspecialchars($MODE); ?>R" onsubmit="return uploading(this);" >
								<input type="hidden" name="idx" value="<?php Echo htmlspecialchars($IDX); ?>" />

								<fieldset>
									<legend><?php Echo $BOARD_NAME; ?>게시판에 새로운 글 작성</legend>
									<dl class="t">
										<dt><span>* <b><label for="frm_subject">제목</label></b></span></dt>
										<dd>
											<span>
												<input type="text" id="frm_subject" name="title" style="width:400px;" value="<?php Echo $TITLE; ?>" required="required" />
<?php
	$noticeYn = False;
	
	// 공지는 관리자만 등록 가능
	If(In_Array($_SESSION['HOME']['USER_AUTH'], Array('0001', '0002'))){
		If($MODE == 'W') $noticeYn = True;
		ElseIf($MODE == 'M' && ($IDX == $GRP)) $noticeYn = True;
	}

	If($noticeYn == True) {
?>
												<input type="checkbox" name="noti" id="noti" value="Y"<?php If($NOTI == 1) Echo ' checked="checked"'; ?> /><label for="noti">공지</label>
<?php
	}
?>
											</span>
										</dd>
									</dl>
<?php
	If($MASTER[0]['USE_CATEGORY'] == 'Y') {
?>
									<dl>
										<dt><span>* <b><label for="frm_category">분류</label></b></span></dt>
										<dd><span>
<?php
		Echo '<select name="category" id="frm_category" kongjang-required>';
		Echo '	<option value="">- 선택 -</option>';
		Foreach($categoryArr AS $list){
			$selected = "";
			If($list == $CATEGORY) $selected = ' selected = "selected"';
			Echo '<option value="'.$list.'"'.$selected.'>'.$list.'</option>';
		}

		Echo '</select>';
?>
										</span></dd>
									</dl>
<?php
	}
?>
									<dl>
										<dt><span>* <b><label for="frm_name">작성자</label></b></span></dt>
										<dd><span><input type="text" size="16" id="frm_name" name="writer" maxlength="50" value="<?php Echo $WRITER; ?>" class="wf_txtbox1" /></span></dd>
									</dl>
<?php
	If($_SESSION['valid_level'] != 1 && $MODE != 'M') {
?>

									<dl>
										<dt><span>* <b><label for="frm_passwd">비밀번호</label></b></span></dt>
										<dd><span><input type="password" size="16" id="frm_passwd" name="passwd" maxlength="50" class="wf_txtbox1" /></span></dd>
									</dl>
<?php
	}
?>
									<dl class="content" style="width:100%">
										<dt<?php If ( IntVal($EDITOR_LEVEL) >= IntVal($_SESSION['valid_level']) ) Echo ' class="access"'; ?>>* <b><label for="CONTENTS">내용</label></b></dt>
										<dd<?php If ( IntVal($EDITOR_LEVEL) >= IntVal($_SESSION['valid_level']) ) Echo ' style="width:100%"'; ?>><span><textarea type="text" id="CONTENTS" name="CONTENTS" rows="16" class="w100"><?php Echo $CONTENTS; ?></textarea></span></dd>
									</dl>
									<dl>
										<dt>* <b><label for="m_file">표지</label></b></dt>
										<dd>
											<input type="file" name="m_file[]" id="mfile" />
<?php
			If ( $MODE == 'MR' && !Empty($FILE_NAME) ) {
?>
												<input type="checkbox" name="del_m_file" id="del_m_file" value="Y" />
												<label for="del_m_file">삭제</label>
												&nbsp;
												<a href="download2.php?idx=<?php Echo $IDX; ?>&amp;id=<?php Echo $BID; ?>"><?php Echo $FILE_NAME; ?></a>
<?php
			}
?>
										</dd>
									</dl>
<?php
	If($MASTER[0]['FILE_COUNT'] > 0) {
		$fileList = Array();
		If ( $MODE == 'M' ) {
			$DB->setProcName('SP_BOARD_FILE_LIST_SELECT');
			$DB->bind_param('IDX', 						$IDX,								_INT, 		20,			_PARAM_IN);
			If ($DB->ExecuteProc()) {
				$result = $DB->get_fetch_assoc();
				
				If($result){
					$i = 1;
					Foreach($result as $l) {
						$fileList[$i]['SEQ'] = IntVal($l['SEQ']);
						$fileList[$i]['FILE_REALNAME'] = $l['FILE_REALNAME'];
						$i++;
					}
				}
			}
		}
		
		$fileCnt = Count($fileList);
		
		For ( $i = 1; $i <= $MASTER[0]['FILE_COUNT']; $i++ ) {
			$disabled = "";
			If ($i <= $fileCnt) $disabled = " disabled = 'disabled'";
?>
									<dl>
										<dt><b><label for="file<?php Echo $i; ?>">파일첨부<?php Echo $i; ?></label></b></dt>
										<dd>
											<span>
												<input type="file" name="FILE[]" id="FILE<?php Echo $i; ?>"<?php Echo $disabled; ?> />
<?php
			If ( $MODE == 'M' && !Empty($fileList[$i]['SEQ']) ) {
?>
												<input type="checkbox" name="del_file<?php Echo $i; ?>" id="del_file<?php Echo $i; ?>" value="<?php Echo $fileList[$i]['SEQ']; ?>" />
												<label for="del_file<?php Echo $i; ?>">삭제</label>
												&nbsp;
												<a href="download.php?idx=<?php Echo $IDX; ?>&amp;seq=<?php Echo $fileList[$i]['SEQ']; ?>"><?php Echo $fileList[$i]['FILE_REALNAME']; ?></a>
<?php
			}
?>
											</span>
										</dd>
									</dl>
<?php
		}
	}
?>
<?php
			If($MODE != 'M') {		// 신규등록 및 답글 시 자동등록방지 활성화
?>
									<dl>
										<dt><span>* <b><label for="captcha">자동등록방지</label></b></span></dt>
										<dd>
											<p><img id="captchaImage" src="/inc/captcha.php?v=<?php echo ((Float)rand() / (Float)getrandmax()); ?>"> <a href="javascript:void(0)" style="color:blue; vertical-align:bottom" onclick="captchaRefresh();">새로고침</a></p>
											<p style="margin-top:5px"><input type="text" name="captchaString" id="captchaString" /> ※ 위 이미지에서 보이는 문자를 입력하세요.</p>
										</dd>
									</dl>
<?php
			}
?>
									<!-- 버튼 -->
                                    <div id="boardBtn">
                                        <div>
                                        <input type="submit" value="확인" />&nbsp; <a href="javascript:window.history.go(-1);">취소</a>
                                        </div>
                                	</div>
                                    
								</fieldset>
							</form>
						</div>

						<script type="text/javascript" src="<?php Echo _GLOBAL_BASE_URL;?>modules/smartEditor/js/HuskyEZCreator.js" charset="utf-8"></script>
						<script>
							var oEditors = [];
							nhn.husky.EZCreator.createInIFrame({
								oAppRef: oEditors,
								elPlaceHolder: "CONTENTS",
								sSkinURI: "<?php Echo _GLOBAL_BASE_URL; ?>modules/smartEditor/SmartEditor2Skin.html",
								fCreator: "createSEditor2"
							});
							
							function uploading(f){
								oEditors.getById["CONTENTS"].exec("UPDATE_CONTENTS_FIELD", []);

								if(f.title.value == '') { alert('제목을 입력하십시오.'); f.title.focus(); return false; }
								else if(f.writer.value == '') { alert('작성자를 입력하십시오.'); f.writer.focus(); return false; }
								return true;
							}
/*
							function uploading(f){
								if(f.title.value == '') { alert('제목을 입력하십시오.'); f.title.focus(); return false; }
								else if(f.writer.value == '') { alert('작성자를 입력하십시오.'); f.writer.focus(); return false; }
								else if(f.contents.value == '') { alert('내용 입력하십시오.'); f.contents.focus(); return false; }
								return true;
							}
*/
						</script>