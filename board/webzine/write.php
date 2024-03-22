<?php
	If($MODE == 'M') {
		$IDX = (!Empty($_GET['idx'])) ? IntVal($_GET['idx']) : Null;
		
		If($_SESSION['valid_level'] != 1){
			If(Empty($_POST['passwd'])) {
				Echo "<meta http-equiv=\"refresh\" content=\"0; url='?id={$BID}&amp;mode=PW&amp;idx={$IDX}&amp;rmode=M'\">";
				Exit;
			} Else {
				$sql = "SELECT `PASSWD`, `TITLE`, `CONTENTS`, `WRITER`, `LEVEL`, `FILE_NAME` FROM `T_BOARD` WHERE `IDX` = '{$IDX}' ";
				$r = mysql_query($sql);
				$c = mysql_fetch_assoc($r);
				If ($c){
					If ($_POST['passwd'] == $c['PASSWD']){ 
						$MODE = $MODE . 'R';  // MR 또는 RR로 변경
						$TITLE = $c['TITLE'];
						$CONTENTS = $c['CONTENTS'];
						$WRITER = $c['WRITER'];
						$LEVEL = IntVal($c['LEVEL']);
						$FILE_NAME = $c['FILE_NAME'];
					} Else {
						Echo "<script>alert('비밀번호가 일치하지 않습니다.');</script>";
						Echo "<meta http-equiv=\"refresh\" content=\"0; url='?id={$BID}&amp;mode=PW&amp;idx={$IDX}&amp;rmode=M'\">";
					}
				} Else location(2, "삭제되었거나 존재하지 않는 게시글입니다.", "/board/?id={$BID}");
			}
		} Else {
			$sql = "SELECT `TITLE`, `CONTENTS`, `WRITER`, `LEVEL` FROM `T_BOARD` WHERE `IDX` = '{$IDX}' ";
			$r = mysql_query($sql);
			$c = mysql_fetch_assoc($r);
			If ($c){
				$MODE = $MODE . 'R';  // MR 또는 RR로 변경
				$TITLE = $c['TITLE'];
				$CONTENTS = $c['CONTENTS'];
				$WRITER = $c['WRITER'];
				$LEVEL = IntVal($c['LEVEL']);
			} Else location(2, "삭제되었거나 존재하지 않는 게시글입니다.", "/board/?id={$BID}");
		}
		mysql_free_result($r);

	}ElseIf ($MODE == 'R') {
		$IDX = (!Empty($_GET['idx'])) ? IntVal($_GET['idx']) : Null;
		$sql = "SELECT `CONTENTS` FROM `T_BOARD` WHERE `IDX` = '{$IDX}' ";
		$r = mysql_query($sql);
		$c = mysql_fetch_assoc($r);
		If ($c){
			$MODE = $MODE . 'R';  // MR 또는 RR로 변경
			$CONTENTS = $c['CONTENTS'];
			$CONTENTS = "<br /><br /><br /><br />=============== 이전글 ===============<br /><br /><br />" . $CONTENTS;
		} Else location(2, "원본글이 삭제되었거나 존재하지 않습니다.", "/board/?id={$BID}");
		mysql_free_result($r);

	}Else $MODE = 'WR';

	$page = (IsSet($_GET['p']) && !Empty($_GET['p'])) ? IntVal($_GET['p']) : 1;
	$srhctgr = $_GET['srhctgr'];
	$keyword = $_GET['keyword'];
?>
						<div id="CDIBoardWrite">
							* 표시는 필수 입력사항입니다.<br />
							<form method="post" id="write_form" name="write_form" enctype="multipart/form-data" action="/board/" onsubmit="return uploading(this);" >
								<input type="hidden" name="idx" value="<?php Echo htmlspecialchars($IDX); ?>" />
								<input type="hidden" name="id" value="<?php Echo htmlspecialchars($BID); ?>" />
								<input type="hidden" name="mode" value="<?php Echo htmlspecialchars($MODE); ?>" />

								<fieldset>
									<legend><?php Echo $BOARD_NAME; ?>게시판에 새로운 글 작성</legend>
									<dl class="t">
										<dt><span>* <b><label for="frm_subject">책자</label></b></span></dt>
										<dd>
											<span>
												<input type="text" id="frm_subject" name="title" style="width:300px;" value="<?php Echo $TITLE; ?>" required="required" />
<?php
	$sql = "SELECT COUNT(*) FROM T_BOARD WHERE `GROUP` = '{$IDX}' ";
	$r = mysql_query($sql);
	$c = mysql_fetch_row($r);
	$replyCount = $c[0];
	If($_SESSION['valid_level'] == 1 && $replyCount  <= 1 && $MODE != 'RR') {
?>
												<!--input type="checkbox" name="noti" id="noti" value="Y" /><label for="noti">공지</label-->
<?php
	}
?>
											</span>
										</dd>
									</dl>
									<dl>
										<dt><span>* <b><label for="frm_name">저자</label></b></span></dt>
										<dd><span><input type="text" size="24" id="frm_name" name="writer" maxlength="150" value="<?php Echo $WRITER; ?>" class="wf_txtbox1" /></span></dd>
									</dl>
<?php
	If($_SESSION['valid_level'] != 1 && $MODE != 'MR') {
?>

									<dl>
										<dt><span>* <b><label for="frm_passwd">비밀번호</label></b></span></dt>
										<dd><span><input type="password" size="16" id="frm_passwd" name="passwd" maxlength="50" class="wf_txtbox1" /></span></dd>
									</dl>
<?php
	}
?>
									<dl class="content" style="width:100%">
										<dt<?php If ( IntVal($EDITOR_LEVEL) >= IntVal($_SESSION['valid_level']) ) Echo ' class="access"'; ?>>* <b><label for="frm_content">내용</label></b></dt>
										<dd<?php If ( IntVal($EDITOR_LEVEL) >= IntVal($_SESSION['valid_level']) ) Echo ' style="width:100%"'; ?>><span><textarea type="text" id="frm_content" name="contents" rows="16" class="w100" required="required"><?php Echo $CONTENTS; ?></textarea></span></dd>
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
	If($FILE_COUNT > 0) {
		If ( $MODE == 'MR' ) {
			$sql = "SELECT `SEQ`, `FILE_NAME` FROM `T_BOARD_FILE` WHERE `IDX` = '{$IDX}' ORDER BY IDX ASC ";
			$r = mysql_query($sql);
			$fileCnt = mysql_num_rows($r);
			If ($fileCnt > 0) {
				$i = 1;
				$fileList = Array();
				While($l = mysql_fetch_assoc($r)){
					$fileList[$i]['SEQ'] = IntVal($l['SEQ']);
					$fileList[$i]['FILE_NAME'] = $l['FILE_NAME'];
					$i++;
				}
			}
			mysql_free_result($r);
		}

		For ( $i = 1; $i <= $FILE_COUNT; $i++ ) {
			$disabled = "";
			If ($i <= $fileCnt) $disabled = " disabled = 'disabled'";
?>
									<dl>
										<dt><b><label for="file<?php Echo $i; ?>">파일첨부<?php Echo $i; ?></label></b></dt>
										<dd>
											<span>
												<input type="file" name="file[]" id="file<?php Echo $i; ?>"<?php Echo $disabled; ?> />
<?php
			If ( $MODE == 'MR' && !Empty($fileList[$i]['SEQ']) ) {
?>
												<input type="checkbox" name="del_file<?php Echo $i; ?>" id="del_file<?php Echo $i; ?>" value="<?php Echo $fileList[$i]['SEQ']; ?>" />
												<label for="del_file<?php Echo $i; ?>">삭제</label>
												&nbsp;
												<a href="download.php?idx=<?php Echo $IDX; ?>&amp;seq=<?php Echo $fileList[$i]['SEQ']; ?>"><?php Echo $fileList[$i]['FILE_NAME']; ?></a>
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

									<input type="hidden" value="<?php Echo $IDX; ?>" name="idx" />
									<input type="hidden" value="" name="ref" />
									<input type="hidden" value="" name="re_step" />
									<input type="hidden" value="" name="re_level" />
									<input type="hidden" value="<?php Echo $page; ?>" name="p" />
									<input type="hidden" value="<?php Echo $srhctgr; ?>" name="srhctgr" />
									<input type="hidden" value="<?php Echo $keyword; ?>" name="keyword" />

									<!-- 버튼 -->
                                    <div id="CNPDBoardBtn">
                                        <div>
                                        <input type="submit" value="확인" />&nbsp; <a href="javascript:window.history.go(-1);">취소</a>
                                        </div>
                                	</div>
                                    
								</fieldset>
							</form>
						</div>

						<script type="text/javascript">
<?php
	If ( $EDITOR_LEVEL >= $_SESSION['valid_level'] ) {
?>
							$('textarea#frm_content').ckeditor();

							function uploading(f){
								var  contents = CKEDITOR.instances.frm_content.getData();
								if(f.title.value == '') { alert('책자를 입력하십시오.'); f.title.focus(); return false; }
								else if(f.writer.value == '') { alert('저자를 입력하십시오.'); f.writer.focus(); return false; }
								else if(contents == '') { alert('내용을 입력하십시오.'); return false; }
								return true;
							}
<?php
	} Else {
?>
							function uploading(f){
								if(f.title.value == '') { alert('책자를 입력하십시오.'); f.title.focus(); return false; }
								else if(f.writer.value == '') { alert('저자를 입력하십시오.'); f.writer.focus(); return false; }
								else if(f.contents.value == '') { alert('내용을 입력하십시오.'); f.contents.focus(); return false; }
								return true;
							}
<?php
	}
?>
						</script>