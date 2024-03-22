<?php
	If($MODE == 'M' || $MODE == 'R') {
		$IDX = (!Empty($_GET['idx'])) ? IntVal($_GET['idx']) : Null;
		$sql = "SELECT `TITLE`, `CONTENTS`, `WRITER`, `FILE_NAME` FROM `T_BOARD` WHERE `IDX` = '{$IDX}' ";
		$r = mysql_query($sql);
		$c = mysql_fetch_assoc($r);
		If ($c){
			$MODE = $MODE . 'R';  // MR 또는 RR로 변경
			$TITLE = $c['TITLE'];
			$CONTENTS = $c['CONTENTS'];
			$WRITER = $c['WRITER'];
			$FILE_NAME = $c['FILE_NAME'];
		} Else location(2, "삭제되었거나 존재하지 않는 게시글입니다.", "/board/?id={$BID}");
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
										<dt><span>* <b><label for="frm_subject">제목</label></b></span></dt>
										<dd><span><input type="text" id="frm_subject" name="title" style="width:400px;" value="<?php Echo $TITLE; ?>" required="required" /></span></dd>
									</dl>
									<dl>
										<dt><span>* <b><label for="frm_name">작성자</label></b></span></dt>
										<dd><span><input type="text" size="16" id="frm_name" name="writer" maxlength="50" value="<?php Echo $WRITER; ?>" class="wf_txtbox1" /></span></dd>
									</dl>
									<dl class="content">
										<dt class="access">* <b><label for="frm_content">내용</label></b></dt>
										<dd style="width:100%; padding-left:5px">
											<span><textarea type="text" id="frm_content" name="contents" cols="100" rows="10" class="w100" required="required" /><?php Echo $CONTENTS; ?></textarea></span>
										</dd>
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
			$sql = "SELECT `SEQ`, `FILE_NAME` FROM `T_BOARD_FILE` WHERE `IDX` = '{$IDX}' ORDER BY SEQ ASC ";
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
?>
									<dl>
										<dt>
											<b><label for="file<?php Echo $i; ?>">파일첨부<?php Echo ($i); ?></label></b>
										</dt>
										<dd>
											<span>
												<input type="file" name="file[]" id="file<?php Echo $i; ?>" />
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
							$('textarea#frm_content').ckeditor();

							function uploading(f){
								var  contents = CKEDITOR.instances.frm_content.getData();
								if(f.title.value == '') { alert('제목을 입력하십시오.'); f.title.focus(); return false; }
								else if(f.writer.value == '') { alert('작성자를 입력하십시오.'); f.writer.focus(); return false; }
								else if(contents == '') { alert('내용 입력하십시오.'); return false; }
								return true;
							}
						</script>