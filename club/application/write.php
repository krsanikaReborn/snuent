<?php
	// URL 접근 거부
	If(!Defined('webSite')) Header('Location: /');

	$stat = false;

	// 수강생정보 조회
	$DB->setProcName('SP_WEB_USER_INFO');
	$DB->bind_param('P_USER_NO', 		$USER_NO,							_VARCHAR,	20,		_PARAM_IN);

	If ($DB->ExecuteProc()) {
		$Result = $DB->get_fetch_assoc();
		If($Result) {
			$stat = true;
			$SOSOG_NM	= $Result[0]['SOSOG_NM'];
			$HP_NO			= $Result[0]['HP_NO'];
			$NAME				= $Result[0]['NAME'];
		}
	}
	
	If(!$stat) location(2, '수강생 정보 조회에 실패하였습니다. 관리자에게 문의하세요.', '/club/application/');
	
	If($MODE == 'M') {
		$DB->setProcName('SP_WEB_NON_MAJOR_CLUB_APPLICATION_SELECT');
		$DB->bind_param('P_YEAR', 				$YEAR,									_VARCHAR,	4,			_PARAM_IN);
		$DB->bind_param('P_HAGGI', 			$HAGGI,								_VARCHAR,	6,			_PARAM_IN);
		$DB->bind_param('P_TEAM_CD', 		$TEAM_CD,							_VARCHAR,	10,		_PARAM_IN);
		$DB->bind_param('P_USER_NO', 		$USER_NO,							_VARCHAR,	20,		_PARAM_IN);

		If($DB->ExecuteProc()) {
			$Result = $DB->get_fetch_assoc();
			If($Result){
				$ITEM_NM = $Result[0]['ITEM_NM'];
				$CORE_TECH = $Result[0]['CORE_TECH'];
				$CLUB_NM = $Result[0]['CLUB_NM'];
				$REG_HAGBEON = $Result[0]['REG_HAGBEON'];
				$REG_NAME = $Result[0]['REG_NAME'];
				$REG_GROUP = $Result[0]['REG_GROUP'];
				$REG_DEPART = $Result[0]['REG_DEPART'];
				$REG_MAJOR = $Result[0]['REG_MAJOR'];
				$REG_GRADE = $Result[0]['REG_GRADE'];
				$REG_STATE = $Result[0]['REG_STATE'];
				$REG_TEL_NO = $Result[0]['REG_TEL_NO'];
				$REG_HP_NO = $Result[0]['REG_HP_NO'];
				$REG_EMAIL = $Result[0]['REG_EMAIL'];
				$PROFESSOR_NM = $Result[0]['PROFESSOR_NM'];
				$BIZ_NAME = $Result[0]['BIZ_NAME'];
				$BIZ_NUM = $Result[0]['BIZ_NUM'];
				$BIZ_TYPE = $Result[0]['BIZ_TYPE'];
				$BIZ_START_DT = $Result[0]['BIZ_START_DT'];
				$BIZ_SALES = $Result[0]['BIZ_SALES'];
				$BIZ_EMP_CNT = $Result[0]['BIZ_EMP_CNT'];
				$IPR_APPLY_YN = $Result[0]['IPR_APPLY_YN'];
				$IPR_ING_CNT = $Result[0]['IPR_ING_CNT'];
				$IPR_PATENT_CNT = $Result[0]['IPR_PATENT_CNT'];
				$IPR_PATENT_NAME = $Result[0]['IPR_PATENT_NAME'];
				$FILE_NAME = $Result[0]['FILE_NAME'];
				$REALFILE_NAME = $Result[0]['REALFILE_NAME'];
				$STATUS = $Result[0]['STATUS'];
				
				If($STATUS == '승인') location(2, '승인된 신청서는 수정할 수 없습니다.', '/club/application/');
			}
		} Else location(2, '팀 정보 조회에 실패하였습니다.', '/club/application/');

	}

	Else {

		$schedule_stat = false;
		// 일정에 등록된 최종 년도/학기를 조회한다.
		$DB->setProcName('SP_WEB_LAST_YEAR_HAGGI_SELECT');
		If ($DB->ExecuteProc()) {
			$Result = $DB->get_fetch_assoc();

			If($Result) {
				$schedule_stat = true;

				$YEAR = $Result[0]['YEAR'];
				$HAGGI = $Result[0]['HAGGI'];
			}
		}

		If(!$schedule_stat) {
			location(2, '등록된 일정이 없습니다.\r\n관리자에게 문의하십시오.', '/club/application/');
		}
	}
?>
				<script type="text/javascript" src="application.js"></script>
						<form name="applicationForm" id="applicationForm" method="post" action="?" enctype="multipart/form-data" kongjang-validate>
							<input type="hidden" name="YEAR" id="YEAR" value="<?php Echo $YEAR; ?>" />
							<input type="hidden" name="HAGGI" id="HAGGI" value="<?php Echo $HAGGI; ?>" />
							<input type="hidden" name="TEAM_CD" id="TEAM_CD" value="<?php Echo $TEAM_CD; ?>" />
							<input type="hidden" name="mode" id="mode" value="<?php Echo $MODE; ?>R" />

							<p class="blue_dot_small hfont bold"><?php Echo $YEAR;?>년도 <?php Echo $HAGGI; ?></p>
                            <div class="table_view2">
                                <table border="0" cellspacing="0" cellpadding="0">
                                    <colgroup>
                                        <col class="w15">
                                        <col class="w31">
                                        <col class="w15">
                                    </colgroup>
                                    <tbody>
										<tr>
											<th colspan="6">신청자 정보 (팀장)</th>
										</tr>
										<tr>
											<th>학번</th>
											<td><?php Echo ($MODE == 'W') ? $USER_NO : $REG_HAGBEON; ?></td>
											<th>성명</th>
											<td><input type="text" name="REG_NAME" id="REG_NAME" size="12" maxlength="30" value="<?php Echo ($MODE == 'W') ? $NAME : $REG_NAME; ?>" readonly /></td>
										</tr>
										<tr>
											<th>소속학과/학년</th>
											<td>
												<input type="text" name="REG_MAJOR" id="REG_MAJOR" size="18" maxlength="100" value="<?php Echo ($MODE == 'W') ? $SOSOG_NM : $REG_MAJOR; ?>" readonly />
												/
												<input type="text" name="REG_GRADE" id="REG_GRADE" size="3" maxlength="1" value="<?php Echo ($MODE == 'W') ? '' : $REG_GRADE; ?>" kongjang-required kongjang-maxlength="1" kongjang-errors-container="#clubNm-error-message-box"<?php If($MODE == 'M') Echo ' readonly'; ?> />학년
												<span id="clubNm-error-message-box"></span>
											</td>
											<th>연락처</th>
											<td><input type="text" name="REG_HP_NO" id="REG_HP_NO" size="20" maxlength="14" value="<?php Echo ($MODE == 'W') ? $HP_NO : $REG_HP_NO; ?>" kongjang-required kongjang-maxlength="14" /></td>
										</tr>
									</tbody>
								</table>
							</div>

							<div class="margin_t_10 table_view2">
								<table border="0" cellspacing="0" cellpadding="0">
                                    <colgroup>
                                        <col class="w15" />
                                        <col />
                                    </colgroup>
                                    <tbody>
										<tr>
											<th colspan="2">창업동아리 정보</th>
										</tr>
										<tr>
                                            <th>팀명</th>
                                            <td><input type="text" name="CLUB_NM" id="CLUB_NM" size="50" value="<?php Echo $CLUB_NM; ?>" maxlength="150" kongjang-required kongjang-maxlength="150"></td>
										</tr>
                                        <tr>
                                            <th>창업아이템명</th>
                                            <td><input type="text" name="ITEM_NM" id="ITEM_NM" size="70" value="<?php Echo $ITEM_NM; ?>" maxlength="300" kongjang-required kongjang-maxlength="300" /></td>
                                        </tr>
                                        <tr>
                                            <th>핵심기술</th>
                                            <td><input type="text" name="CORE_TECH" id="CORE_TECH" size="70" value="<?php Echo $CORE_TECH; ?>" maxlength="300" kongjang-required kongjang-maxlength="300" /></td>
                                        </tr>
										<tr>
											<th>제출문서</th>
											<td><input type="file" name="FILE" id="FILE"<?php If($MODE == 'W') Echo ' kongjang-required'; ?> /><?php If($MODE == 'M') Echo ' ※첨부하시는 경우 기존 문서는 삭제됩니다.'; ?></td>
										</tr>
									</tbody>
								</table>
							</div>

							<div class="margin_t_10 table_view2">
								<table border="0" cellspacing="0" cellpadding="0">
                                    <colgroup>
                                        <col class="w15" />
                                        <col class="w31" />
                                        <col class="w15" />
                                        <col />
                                    </colgroup>
                                    <tbody>
										<tr>
											<th colspan="4">창업 현황 (사업자등록 또는 법인등록한 동아리에 한해 작성)</th>
										</tr>
                                        <tr>
                                            <th>기업명</th>
                                            <td><input type="text" name="BIZ_NAME" id="BIZ_NAME" size="30" value="<?php Echo $BIZ_NAME; ?>" maxlength="150" kongjang-maxlength="150" /></td>
                                            <th>사업자등록번호</th>
                                            <td><input type="text" name="BIZ_NUM" id="BIZ_NUM" size="20" value="<?php Echo $BIZ_NUM; ?>" maxlength="15" kongjang-maxlength="15" /></td>
                                        </tr>
                                        <tr>
                                            <th>창업일</th>
                                            <td><input type="text" name="BIZ_START_DT" id="BIZ_START_DT" value="<?php Echo $BIZ_START_DT; ?>" class="text-center" size="15" maxlength="10" maxlength="10" kongjang-maxlength="10" /></td>
                                            <th>업종</th>
                                            <td><input type="text" name="BIZ_TYPE" id="BIZ_TYPE" size="30" value="<?php Echo $BIZ_TYPE; ?>" maxlength="150" kongjang-maxlength="150" /></td>
                                        </tr>
                                        <tr>
                                            <th>매출<br />(전년도기준)</th>
                                            <td><input name="BIZ_SALES" id="BIZ_SALES" type="text" size="14" value="<?php Echo $BIZ_SALES; ?>" maxlength="12" kongjang-maxlength="12" /> 원</td>
                                            <th>고용인원수</th>
                                            <td><input type="text" name="BIZ_EMP_CNT" id="BIZ_EMP_CNT" class="text-right" size="12" value="<?php Echo $BIZ_EMP_CNT; ?>" maxlength="12" kongjang-maxlength="12" /> 명</td>
                                        </tr>
									</tbody>
								</table>
							</div>

							<div class="margin_t_10 table_view2">
								<table border="0" cellspacing="0" cellpadding="0">
                                    <colgroup>
                                        <col class="w15">
                                        <col />
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <th colspan="2">지적재산권 출원 및 등록 현황</th>
										</tr>
										<tr>
                                            <th>출원여부</th>
                                            <td>
												<input type="checkbox" name="IPR_APPLY_YN" id="IPR_APPLY_YN" value="Y"<?php If($IPR_APPLY_YN == 'Y') Echo ' checked'; ?>><label for="IPR_APPLY_YN"> 출원</label>
												<span style="margin-left:10px;">출원중 <input type="text" name="IPR_ING_CNT" id="IPR_ING_CNT" size="7" value="<?php If($IPR_ING_CNT <> 0) Echo $IPR_ING_CNT; ?>" maxlength="5" kongjang-type="integer" kongjang-max="32767" kongjang-errors-container="#validate-IPR-message1"<?php If($IPR_APPLY_YN != 'Y') Echo ' readonly'; ?> /> 건</span>
												<span style="margin-left:10px;">등록완료 <input type="text" name="IPR_PATENT_CNT" id="IPR_PATENT_CNT" size="7" value="<?php If($IPR_ING_CNT <> 0) Echo $IPR_PATENT_CNT; ?>" maxlength="5" kongjang-type="integer" kongjang-max="32767" kongjang-errors-container="#validate-IPR-message2"<?php If($IPR_APPLY_YN != 'Y') Echo ' readonly'; ?> /> 건</span>
												<div id="validate-IPR-message1"></div><div id="validate-IPR-message2"></div>
											</td>
                                        </tr>
                                        <tr>
                                            <th>발명의 명칭</th>
                                            <td><input type="text" name="IPR_PATENT_NAME" id="IPR_PATENT_NAME" vlaue="<?php Echo $IPR_PATENT_NAME; ?>" style="width: 98%;" kongjang-maxlength="1000" /></td>
                                        </tr>
									</tbody>
								</table>
							</div>

							<div id="winTable" class="margin_t_10 margin_b_50 table_view2">
								<table border="0" cellspacing="0" cellpadding="0">
                                    <colgroup>
                                        <col width="90">
                                        <col class="w21">
                                        <col width="120">
                                        <col class="w21" />
                                        <col />
                                    </colgroup>
									<thead>
                                        <tr>
                                            <th colspan="5">입상 및 지원사업 선정경력</th>
										</tr>
										<tr>
                                            <th id="btnHeader" style="display:none"><a href="javascript:void(0);" class="btnAddEntry" style="text-decoration:none"><img src="/res/img/sub/cont/98/plus.png" align="middle" alt="추가" title="클릭하시면 새로운 행이 추가됩니다." /> (추가)</a></th>
                                            <th>대회명</th>
                                            <th>수상내역</th>
                                            <th>수상일자</th>
                                            <th>시행기관</th>
                                        </tr>
									</thead>
                                    <tbody>
<?php
	If($MODE == 'W') {
?>
										<noscript>
										<tr id="ENTRY1_ROW_0">
											<td><input type="text" name="CONTEST_NM[]" id="ENTRY1_ROW_0_CONTEST_NM" style="width: 98%;" value=""></td>
											<td><input type="text" name="AWARD[]" id="ENTRY1_ROW_0_AWARD" style="width: 98%;" value=""></td>
											<td><input type="text" name="AWARD_DT[]" id="ENTRY1_ROW_0_AWARD_DT" style="width: 98%;" class="center" value=""></td>
											<td><input type="text" name="AGENCY[]" id="ENTRY1_ROW_0_AGENCY" style="width: 98%;" value=""></td>
										</tr>
										<tr id="ENTRY1_ROW_1">
											<td><input type="text" name="CONTEST_NM[]" id="ENTRY1_ROW_1_CONTEST_NM" style="width: 98%;" value=""></td>
											<td><input type="text" name="AWARD[]" id="ENTRY1_ROW_1_AWARD" style="width: 98%;" value=""></td>
											<td><input type="text" name="AWARD_DT[]" id="ENTRY1_ROW_1_AWARD_DT" style="width: 98%;" class="center" value=""></td>
											<td><input type="text" name="AGENCY[]" id="ENTRY1_ROW_1_AGENCY" style="width: 98%;" value=""></td>
										</tr>
										<tr id="ENTRY1_ROW_2">
											<td><input type="text" name="CONTEST_NM[]" id="ENTRY1_ROW_2_CONTEST_NM" style="width: 98%;" value=""></td>
											<td><input type="text" name="AWARD[]" id="ENTRY1_ROW_2_AWARD" style="width: 98%;" value=""></td>
											<td><input type="text" name="AWARD_DT[]" id="ENTRY1_ROW_2_AWARD_DT" style="width: 98%;" class="center" value=""></td>
											<td><input type="text" name="AGENCY[]" id="ENTRY1_ROW_2_AGENCY" style="width: 98%;" value=""></td>
										</tr>
										<tr id="ENTRY1_ROW_3">
											<td><input type="text" name="CONTEST_NM[]" id="ENTRY1_ROW_3_CONTEST_NM" style="width: 98%;" value=""></td>
											<td><input type="text" name="AWARD[]" id="ENTRY1_ROW_3_AWARD" style="width: 98%;" value=""></td>
											<td><input type="text" name="AWARD_DT[]" id="ENTRY1_ROW_3_AWARD_DT" style="width: 98%;" class="center" value=""></td>
											<td><input type="text" name="AGENCY[]" id="ENTRY1_ROW_3_AGENCY" style="width: 98%;" value=""></td>
										</tr>
										<tr id="ENTRY1_ROW_4">
											<td><input type="text" name="CONTEST_NM[]" id="ENTRY1_ROW_4_CONTEST_NM" style="width: 98%;" value=""></td>
											<td><input type="text" name="AWARD[]" id="ENTRY1_ROW_4_AWARD" style="width: 98%;" value=""></td>
											<td><input type="text" name="AWARD_DT[]" id="ENTRY1_ROW_4_AWARD_DT" style="width: 98%;" class="center" value=""></td>
											<td><input type="text" name="AGENCY[]" id="ENTRY1_ROW_4_AGENCY" style="width: 98%;" value=""></td>
										</tr>
										</noscript>
<?php
	}

	If($MODE == 'M') {
		$career = false;
		$DB->setProcName('SP_WEB_NON_MAJOR_CLUB_CAREER_SELECT');
		$DB->bind_param('P_YEAR', 				$YEAR,									_VARCHAR,	4,			_PARAM_IN);
		$DB->bind_param('P_HAGGI', 			$HAGGI,								_VARCHAR,	6,			_PARAM_IN);
		$DB->bind_param('P_TEAM_CD', 		$TEAM_CD,							_VARCHAR,	10,		_PARAM_IN);

		If($DB->ExecuteProc()) {
			$Result = $DB->get_fetch_assoc();
			If($Result){
				$career = true;
				$i = 0;
				Foreach($Result AS $CAREER_LIST) {
?>
										<tr id="ENTRY1_ROW_<?php Echo $i; ?>">
											<td class="delButton center" style="display:none"><a href="#n" class="btnDelEntry" style="text-decoration:none"><img src="/res/img/sub/cont/98/minus.png" align="middle" alt="삭제" title="클릭하시면 해당 행이 삭제됩니다." /> (삭제)</a></td>
											<td><input type="text" name="CONTEST_NM[]" id="ENTRY1_ROW_<?php Echo $i; ?>_CONTEST_NM" style="width: 98%;" value="<?php Echo $CAREER_LIST['CONTEST_NM']; ?>"></td>
											<td><input type="text" name="AWARD[]" id="ENTRY1_ROW_<?php Echo $i; ?>_AWARD" style="width: 98%;" value="<?php Echo $CAREER_LIST['AWARD']; ?>"></td>
											<td><input type="text" name="AWARD_DT[]" id="ENTRY1_ROW_<?php Echo $i; ?>_AWARD_DT" style="width: 98%;" class="center" value="<?php Echo $CAREER_LIST['AWARD_DT']; ?>"></td>
											<td><input type="text" name="AGENCY[]" id="ENTRY1_ROW_<?php Echo $i; ?>_AGENCY" style="width: 98%;" value="<?php Echo $CAREER_LIST['AGENCY']; ?>"></td>
										</tr>
<?php
					$i++;
				}
			}
		}

		If(!$career) {
?>
										<tr id="ENTRY1_ROW_0">
											<td class="delButton center" style="display:none"><a href="#n" class="btnDelEntry" style="text-decoration:none"><img src="/res/img/sub/cont/98/minus.png" align="middle" alt="삭제" title="클릭하시면 해당 행이 삭제됩니다." /> (삭제)</a></td>
											<td><input type="text" name="CONTEST_NM[]" id="ENTRY1_ROW_0_CONTEST_NM" style="width: 98%;" ></td>
											<td><input type="text" name="AWARD[]" id="ENTRY1_ROW_0_AWARD" style="width: 98%;" ></td>
											<td><input type="text" name="AWARD_DT[]" id="ENTRY1_ROW_0_AWARD_DT" style="width: 98%;" class="center" ></td>
											<td><input type="text" name="AGENCY[]" id="ENTRY1_ROW_0_AGENCY" style="width: 98%;" ></td>
										</tr>
<?php
		}
	}
	

?>
                                    </tbody>
                                </table>
								<div class="center margin_t_20">
									<input class="submit_1" type="submit" value="<?php Echo ($MODE == 'W') ? '신청' : '수정'; ?>">
									<a href="/club/application/" class="submit">목록</a>
								</div>
							</div>
						</form>