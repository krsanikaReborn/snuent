<?php
	// URL 접근 거부
	If(!Defined('webSite')) Header('Location: /');

	$stat = false;
	// 등록신청서 조회
	$DB->setProcName('SP_WEB_NON_MAJOR_CLUB_APPLICATION_DETAIL_SELECT');
	$DB->bind_param('P_YEAR', 				$YEAR,									_VARCHAR,	4,			_PARAM_IN);
	$DB->bind_param('P_HAGGI',		 		$HAGGI,								_VARCHAR,	6,			_PARAM_IN);
	$DB->bind_param('P_TEAM_CD',		$TEAM_CD,							_VARCHAR,	10,		_PARAM_IN);
	$DB->bind_param('P_USER_NO', 		$USER_NO,							_VARCHAR,	20,		_PARAM_IN);

	If ($DB->ExecuteProc()) {
		$Result = Array();
		$Result = $DB->get_fetch_assoc();
		If($Result) {
			$stat = true;
			
			$CLUB_NM = $Result[0]['CLUB_NM'];
			$LEADER_YN = $Result[0]['LEADER_YN'];
		}
	}

	If(!$stat) location(2, '동아리 등록신청서 조회에 실패하였습니다.', '/club/member/');
	
	If($LEADER_YN != 'Y') {
?>
		<div class="alert alert-info">
			창업동아리 멤버는 팀장만 관리할 수 있습니다.
		</div>
		<div class="center margin_t_20">
			<a href="/club/member/" class="submit" alt="목록">목록</a>
		</div>
<?php
	} Else {
?>
						<script type="text/javascript" src="clubMember.js"></script>

						<form name="clubMemberForm" id="clubMemberForm" method="post" action="?" enctype="multipart/form-data" kongjang-validate>
							<input type="hidden" name="YEAR" id="YEAR" value="<?php Echo $YEAR; ?>" />
							<input type="hidden" name="HAGGI" id="HAGGI" value="<?php Echo $HAGGI; ?>" />
							<input type="hidden" name="TEAM_CD" id="TEAM_CD" value="<?php Echo $TEAM_CD; ?>" />
							<input type="hidden" name="mode" id="mode" value="WR" />

							<div class="margin_t_10 table_view2">
								<table border="0" cellspacing="0" cellpadding="0">
                                    <colgroup>
                                        <col width="90" />
                                        <col class="w33" />
                                        <col width="90" />
                                        <col />
                                    </colgroup>
                                    <tbody>
										<tr>
											<th colspan="4">창업동아리 정보</th>
										</tr>
										<tr>
											<th>년도/학기</th>
											<td><?php Echo $YEAR . '년도 ' . $HAGGI; ?></td>
                                            <th>동아리명</th>
                                            <td><?php Echo $CLUB_NM; ?></td>
										</tr>
									</tbody>
								</table>
							</div>
							
<?php
		$DB->setProcName('SP_WEB_NON_MAJOR_CLUB_MEMBER_LIST_SELECT');
		$DB->bind_param('P_YEAR',		 			$YEAR,														_VARCHAR,	4,			_PARAM_IN);
		$DB->bind_param('P_HAGGI',		 			$HAGGI,													_VARCHAR,	6,			_PARAM_IN);
		$DB->bind_param('P_TEAM_CD',			$TEAM_CD,												_VARCHAR,	10,		_PARAM_IN);
?>
							<div class="table_view2 margin_t_10">
								<table border="0" cellspacing="0" cellpadding="0">
									<colgroup>
										<col width="90" />
										<col class="w20" />
										<col />
										<col class="w15" />
										<col class="w13">
									</colgroup>
									<thead>
										<tr>
											<th colspan="5">창업동아리 멤버 현황</th>
										</tr>
										<tr>
											<th>학번</th>
											<th>성명</th>
											<th>학과</th>
											<th>학년</th>
											<th>비고</th>
										</tr>
									</thead>
									<tbody>
<?php
		If ($DB->ExecuteProc()) {
			$stat = true;
			$Result = $DB->get_fetch_assoc();

			If($Result) {
				$isLeader = false;
				Foreach($Result AS $LIST) {
					If($LIST['LEADER_YN'] == 'Y') $isLeader = true;
?>
										<tr<?php If($LIST['LEADER_YN'] == 'Y') Echo ' style="background-color:#ddd; font-weight:bold;"'; ?>>
											<td class="text-center"><?php Echo $LIST['USER_NO']; ?></td>
											<td class="text-center"><?php Echo $LIST['REG_NAME']; ?></td>
											<td class="text-center"><?php Echo $LIST['REG_MAJOR']; ?></td>
											<td class="text-center"><?php Echo $LIST['REG_GRADE']; ?></td>
											<td class="text-center"><?php Echo ($LIST['LEADER_YN'] == 'Y') ? '팀장' : '팀원'; ?></td>
										</tr>
<?php
				}
			}
		}
		If (!$stat) {
?>
										<tr><td style="height:40px" colspan="4">팀 조회에 실패하였습니다. 관리자에게 문의하십시오.</td></tr>
<?php
		}
?>
									</tbody>
								</table>
							</div>

							<div id="winTable" class="margin_t_10 margin_b_50 table_view2">
								<table border="0" cellspacing="0" cellpadding="0">
                                    <colgroup>
                                        <col width="90" />
                                        <col class="w20" />
                                        <col class="w25" />
                                        <col class="w30" />
                                        <col />
                                    </colgroup>
									<thead>
                                        <tr>
                                            <th colspan="5">창업동아리 멤버 등록</th>
										</tr>
										<tr>
                                            <th id="btnHeader" style="display:none"><a href="javascript:void(0);" class="btnAddEntry" style="text-decoration:none" title="클릭하시면 새로운 행이 추가됩니다."><img src="/res/img/sub/cont/98/plus.png" align="middle" alt="추가" /> (추가)</a></th>
                                            <th>학번</th>
                                            <th>성명</th>
                                            <th>학과</th>
                                            <th>학년</th>
                                        </tr>
										<noscript>
											<tr><td colspan="5" class="left">입력하신 학번은 유효한 학번 및 현재 년도/학기의 다른 동아리멤버가 아닌 경우에만 동아리멤버로 등록됩니다.</td></tr>
										</noscript>
										<tr id="writeInfo" style="display:none">
											<td colspan="5" class="left">※ 학번을 입력한 후 확인 버튼을 클릭하면, 성명 및 학과가 조회되며 확인된 항목만 등록됩니다.</td>
										</tr>
									</thead>
                                    <tbody>
										<noscript>
										<tr id="ENTRY1_ROW_0">
											<td>
												<input type="text" name="REG_CHECK_YN[]" id="ENTRY1_ROW_0_REG_CHECK_YN" value="Y" />
												<input type="text" name="REG_USER_NO[]" id="ENTRY1_ROW_0_REG_USER_NO" maxlength="20" required style="width: 98%;" value="" />
											</td>
											<td>
												<input type="text" name="REG_CHECK_YN[]" id="ENTRY1_ROW_0_REG_CHECK_YN" value="Y" />
												<input type="text" name="REG_NAME[]" id="ENTRY1_ROW_0_REG_NAME" maxlength="50" required style="width: 98%;" value="" readonly />
											</td>
											<td><input type="text" name="REG_MAJOR[]" id="ENTRY1_ROW_0_REG_MAJOR" maxlength="100" style="width: 98%;" value="" readonly /></td>
											<td><input type="text" name="REG_GRADE[]" id="ENTRY1_ROW_0_REG_GRADE" maxlength="3" style="width: 98%;" class="center" value="" readonly /></td>
										</tr>
										<tr id="ENTRY1_ROW_1">
											<td>
												<input type="text" name="REG_CHECK_YN[]" id="ENTRY1_ROW_1_REG_CHECK_YN" value="Y" />
												<input type="text" name="REG_USER_NO[]" id="ENTRY1_ROW_1_REG_USER_NO" maxlength="20" required style="width: 98%;" value="" />
											</td>
											<td><input type="text" name="REG_NAME[]" id="ENTRY1_ROW_1_REG_NAME" maxlength="50" required style="width: 98%;" value="" readonly /></td>
											<td><input type="text" name="REG_MAJOR[]" id="ENTRY1_ROW_1_REG_MAJOR" maxlength="100" style="width: 98%;" value="" readonly /></td>
											<td><input type="text" name="REG_GRADE[]" id="ENTRY1_ROW_1_REG_GRADE" maxlength="3" style="width: 98%;" class="center" value="" readonly /></td>
										</tr>
										<tr id="ENTRY1_ROW_2">
											<td>
												<input type="text" name="REG_CHECK_YN[]" id="ENTRY1_ROW_2_REG_CHECK_YN" value="Y" />
												<input type="text" name="REG_USER_NO[]" id="ENTRY1_ROW_2_REG_USER_NO" maxlength="20" required style="width: 98%;" value="" />
											</td>
											<td><input type="text" name="REG_NAME[]" id="ENTRY1_ROW_2_REG_NAME" maxlength="50" required style="width: 98%;" value="" readonly /></td>
											<td><input type="text" name="REG_MAJOR[]" id="ENTRY1_ROW_2_REG_MAJOR" maxlength="100" style="width: 98%;" value="" readonly /></td>
											<td><input type="text" name="REG_GRADE[]" id="ENTRY1_ROW_2_REG_GRADE" maxlength="3" style="width: 98%;" class="center" value="" readonly /></td>
										</tr>
										<tr id="ENTRY1_ROW_3">
											<td>
												<input type="text" name="REG_CHECK_YN[]" id="ENTRY1_ROW_3_REG_CHECK_YN" value="Y" />
												<input type="text" name="REG_USER_NO[]" id="ENTRY1_ROW_3_REG_USER_NO" maxlength="20" required style="width: 98%;" value="" />
											</td>
											<td><input type="text" name="REG_NAME[]" id="ENTRY1_ROW_3_REG_NAME" maxlength="50" required style="width: 98%;" value="" readonly /></td>
											<td><input type="text" name="REG_MAJOR[]" id="ENTRY1_ROW_3_REG_MAJOR" maxlength="100" style="width: 98%;" class="center" value="" readonly /></td>
											<td><input type="text" name="REG_GRADE[]" id="ENTRY1_ROW_3_REG_GRADE" maxlength="3" style="width: 98%;" value="" readonly /></td>
										</tr>
										<tr id="ENTRY1_ROW_4">
											<td>
												<input type="text" name="REG_CHECK_YN[]" id="ENTRY1_ROW_4_REG_CHECK_YN" value="Y" />
												<input type="text" name="REG_USER_NO[]" id="ENTRY1_ROW_4_REG_USER_NO" maxlength="20" required style="width: 98%;" value="" />
											</td>
											<td><input type="text" name="REG_NAME[]" id="ENTRY1_ROW_4_REG_NAME" maxlength="50" required style="width: 98%;" value="" readonly /></td>
											<td><input type="text" name="REG_MAJOR[]" id="ENTRY1_ROW_4_REG_MAJOR" maxlength="100" style="width: 98%;" value="" readonly /></td>
											<td><input type="text" name="REG_GRADE[]" id="ENTRY1_ROW_4_REG_GRADE" maxlength="3" style="width: 98%;" class="center" value="" readonly /></td>
										</tr>
										</noscript>
                                    </tbody>
                                </table>
								<div class="center margin_t_20">
									<input class="submit_1" type="submit" value="등록">
									<a href="/club/member/" class="submit" alt="취소">취소</a>
								</div>
							</div>
						</form>
<?php
	}
?>