<?php
	// URL 접근 거부
	If(!Defined('webSite')) Header('Location: /');
	
	// 해당 동아리의 활동결과보고서를 조회한다.
	$DB->setProcName('SP_WEB_ACTIVITY_REPORT_DETAIL_SELECT');
	$DB->bind_param('P_YEAR',		 			$YEAR,														_VARCHAR,	4,			_PARAM_IN);
	$DB->bind_param('P_HAGGI',		 			$HAGGI,													_VARCHAR,	6,			_PARAM_IN);
	$DB->bind_param('P_TEAM_CD', 			$TEAM_CD,												_VARCHAR,	10,		_PARAM_IN);
	$DB->bind_param('P_USER_NO',			$USER_NO,												_VARCHAR,	20,		_PARAM_IN);

	$stat = false;

	If ($DB->ExecuteProc()) {
		$Result = Array();
		$Result = $DB->get_fetch_assoc();

		If($Result) {
			$stat = true;

			$SUBMIT_YN		= $Result[0]['REGIST_YN'];
			
			$CLUB_NM			= $Result[0]['CLUB_NM'];

			$REG_NAME			= $Result[0]['REG_NAME'];
			$REG_MAJOR		= $Result[0]['REG_MAJOR'];
			$REG_GRADE		= $Result[0]['REG_GRADE'];

			$SEND_DT			= $Result[0]['SEND_DT'];
			$RESULT				= $Result[0]['RESULT'];
			$NON_RESULT		= $Result[0]['NON_RESULT'];
			$PLAN_NOTE		= $Result[0]['PLAN_NOTE'];

			$FILE_CNT			= $Result[0]['FILE_CNT'];
			
			$LEADER_YN		= $Result[0]['LEADER_YN'];

		}
	}
	If(!$stat) location(2, '활동결과보고서 조회에 실패하였습니다. 관리자에게 문의하십시오.', '/club/monthly/');
?>
						<form name="monthlyForm" id="monthlyForm" method="post" action="?" enctype="multipart/form-data" kongjang-validate>
							<input type="hidden" name="mode" id="mode" value="WR" />
							<input type="hidden" name="TEAM_CD" id="TEAM_CD" value="<?php Echo $TEAM_CD; ?>" />

                            <div class="table_view2">
                                <table border="0" cellspacing="0" cellpadding="0">
                                    <colgroup>
                                        <col class="w15">
                                        <col class="w16">
                                        <col class="w15">
                                        <col class="w16">
                                        <col class="w15">
                                        <col class="w23">
                                    </colgroup>
                                    <tbody>
										<tr>
											<th colspan="6">신청자 정보</th>
										</tr>
										<tr>
											<th>학번</th>
											<td><?php Echo $USER_NO; ?></td>
											<th>성명</th>
											<td><?php Echo $REG_NAME; ?></td>
											<th>학과/학년</th>
											<td><?php Echo $REG_MAJOR; ?> / <?php Echo $REG_GRADE; ?>학년</td>
										</tr>
									</tbody>
								</table>
							</div>

                            <div class="margin_t_20 margin_b_10 table_view2">
                                <table border="0" cellspacing="0" cellpadding="0">
                                    <colgroup>
                                        <col class="w25">
                                        <col class="w75">
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <th>동아리명</th>
                                            <td><?php Echo $CLUB_NM; ?></td>
                                        </tr>
<?php
	If($SUBMIT_YN == 'Y') {
?>
										<tr>
											<th>제출일자</th>
											<td><?php Echo $SEND_DT; ?></td>
										</tr>
<?php
	}
?>
                                        <tr>
                                            <th>창업활동 계획대비 실행결과<br />(일정 및 상세내역 포함)</th>
                                            <td>
<?php
	If($SUBMIT_YN == 'Y') Echo '<div style="min-height:150px">' . nl2br(HtmlSpecialChars($RESULT)) . '</div>';
	Else Echo '<textarea name="RESULT" id="RESULT" class="w99" style="height:300px;" ></textarea>';
?>
											</td>
                                        </tr>
                                        <tr>
                                            <th>학기 계획대비<br />미 실행 상황</th>
                                            <td>
<?php
	If($SUBMIT_YN == 'Y') Echo '<div style="min-height:150px">' . nl2br(HtmlSpecialChars($NON_RESULT)) . '</div>';
	Else Echo '<textarea name="NON_RESULT" id="NON_RESULT" class="w99" style="height:300px;" ></textarea>';
?>
											</td>
                                        </tr>
                                        <tr>
                                            <th>창업 활동 성과 및<br />방학 중 활동 계획</th>
                                            <td>
<?php
	If($SUBMIT_YN == 'Y') Echo '<div style="min-height:150px">' . nl2br(HtmlSpecialChars($PLAN_NOTE)) . '</div>';
	Else Echo '<textarea name="PLAN_NOTE" id="PLAN_NOTE" class="w99" style="height:300px;" ></textarea>';
?>
											</td>
                                        </tr>
										<tr>
											<th>첨부문서<br />(사진 및 성과물)</th>
											<td>
<?php
	If($SUBMIT_YN == 'Y') {
		If($FILE_CNT > 0) {
			// 파일 조회
			$DB->setProcName('SP_WEB_ACTIVITY_REPORT_FILE_LIST_SELECT');
			$DB->bind_param('P_YEAR', 				$YEAR,						_VARCHAR,	4,			_PARAM_IN);
			$DB->bind_param('P_HAGGI', 			$HAGGI,					_VARCHAR,	6,			_PARAM_IN);
			$DB->bind_param('P_TEAM_CD', 		$TEAM_CD,				_VARCHAR,	10,		_PARAM_IN);

			If ($DB->ExecuteProc()) {
				$Result = $DB->get_fetch_assoc();

				If($Result) {
					Foreach($Result AS $file) {
?>
											<p>
												<a href="/inc/download.php?type=ActivityReport&year=<?php Echo $file['YEAR']; ?>&haggi=<?php Echo UrlEncode($file['HAGGI']); ?>&teamCd=<?php Echo $file['TEAM_CD']; ?>&fileNo=<?php Echo $file['UPLOAD_NO']; ?>">
													<?php Echo ($file['FILE_DESCRIPTION'] != "") ? '<span class="bold">[' . $file['FILE_DESCRIPTION'] . ']</span> ' : ""; ?><?php Echo $file['FILE_NAME']; ?>
												</a>
											</p>
<?php
					}
				}
			}
		}
	} Else {
?>
												<label for="FILE_DESCRIPTION1">문서명: </label><input type="text" name="FILE_DESCRIPTION1" id="FILE_DESCRIPTION1" maxlength="100" style="width:130px" />&nbsp;<input type="file" name="FILE_1" id="FILE_1"style="width:350px;" /><br />
												<label for="FILE_DESCRIPTION2">문서명: </label><input type="text" name="FILE_DESCRIPTION2" id="FILE_DESCRIPTION2" maxlength="100" style="width:130px" />&nbsp;<input type="file" name="FILE_2" id="FILE_2"style="width:350px;" /><br />
												<label for="FILE_DESCRIPTION3">문서명: </label><input type="text" name="FILE_DESCRIPTION3" id="FILE_DESCRIPTION3" maxlength="100" style="width:130px" />&nbsp;<input type="file" name="FILE_3" id="FILE_3"style="width:350px;" /><br />
												<label for="FILE_DESCRIPTION4">문서명: </label><input type="text" name="FILE_DESCRIPTION4" id="FILE_DESCRIPTION4" maxlength="100" style="width:130px" />&nbsp;<input type="file" name="FILE_4" id="FILE_4"style="width:350px;" /><br />
												<label for="FILE_DESCRIPTION5">문서명: </label><input type="text" name="FILE_DESCRIPTION5" id="FILE_DESCRIPTION5" maxlength="100" style="width:130px" />&nbsp;<input type="file" name="FILE_5" id="FILE_5"style="width:350px;" /><br />
												예) 문서명 : 단체사진1, 첨부파일 : 첨부사진1.jpg
<?php
	}
?>
											</td>
										</tr>
                                    </tbody>
                                </table>
                            </div>

<?php
	If($LEADER_YN == 'Y') {
?>
							<div class="center margin_t_20">
<?php
		If($SUBMIT_YN != 'Y') {
?>
								<input class="submit_1" type="submit" onclick="return formSubmit(this.form);" value="제출" />
<?php
		}
?>
								<a href="/club/result/" class="submit">이전</a>
							</div>
<?php
	} Else {
?>
							<div class="alert alert-info">
								창업동아리 활동결과보고서는 팀장만 제출가능합니다.
							</div>
<?php
	}
?>
						</form>

						<script>
							function formSubmit(frm) {
								var msg = '';
								if(frm.RESULT.value == '') {
									alert('창업활동 계획대비 실행결과를 입력하세요!');
									frm.RESULT.focus();
									return false;
								}
								if(frm.NON_RESULT.value == '') {
									alert('학기 계획대비 미 실행 상황을 입력하세요!');
									frm.NON_RESULT.focus();
									return false;
								}
								if(frm.PLAN_NOTE.value == '') {
									alert('창업 활동 성과 및 방학 중 활동 계획을 입력하세요!');
									frm.PLAN_NOTE.focus();
									return false;
								}
								var msg = '제출 후 수정하실 수 없으므로 제출하려는 동아리를 다시 한번 확인해주세요.\r\n';
								if(!confirm(msg + '제출 하시겠습니까?')) return false;
							}
						</script>