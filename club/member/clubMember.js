$(function(){
	// 멤버 추가 이벤트
	$('.btnAddEntry').on('click', function(){
		var tbody = $('#winTable>table>tbody');
		newEntry1(tbody);
	});

	// 멤버 삭제 이벤트
	$(document).on('click', '.btnDelEntry', function(){
		var tbody = $('#winTable>table>tbody');
		$(this).closest('tr').remove();

		var count = $('#winTable>table>tbody>tr').length;
		if (count < 1) newEntry1(tbody);
	});
	
	// 폼 전송 이벤트
	$('#clubMemberForm').on('submit', function(){
		var checkYn = false;
		$('#clubMemberForm').find('input[name="REG_CHECK_YN[]"]').each(function(){
			if($(this).val() == 'Y') checkYn = true;
		});
		
		if(!checkYn) {
			alert('학번을 입력한 후 확인버튼을 클릭하여\r\n확인 상태가 된 항목만 등록되며,\r\n하나 이상의 항목이 확인되어야 등록됩니다.');
			return false;
		}
		
		return true;
	});

	$(document).on('click', '#btnGetUser', function(){
		var targetId = $(this).parents('tr').attr('id');
		var userNo = $('#'+targetId+'_REG_USER_NO').val();
		var year = $('#YEAR').val();
		var haggi = $('#HAGGI').val();
		var teamCd = $('#TEAM_CD').val();
		
		if(year == '' || haggi == '' || teamCd =='') {
			alert('동아리가 선택되지 않았습니다.');
			return false;
		}

		if(userNo == '') {
			alert('학번을 입력하세요!');
			return false;
		}
		if(userNo.length > 20) {
			alert('학번은 20자 이하로 입력하세요!');
			return false;
		}

		var form = $("#subContent");
		var form_offset= form.offset();

		form.remove("#overlay");

		var overlay = "<div id='overlay' style='background-color: #F9FAF8; height: " + form.innerHeight() + "px; width: " + form.innerWidth() + "px; position: absolute; /*left: 0px; */top: "+form_offset.top+"px; z-index: 999; opacity: 0.3; display:none;'>";
		overlay += "<img src='data:image/gif;base64,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' alt='loading' style='display:block; position:absolute; top:50%; left:50%; margin-left:-20px;'/>";
		overlay += "</div>";
		
		form.append(overlay);
		$("#overlay").show();

		// Ajax요청
		jQuery.ajax({
			url : "getUser.php",
			type : "POST",
			data : { 'USER_NO' : userNo, 'YEAR' : year, 'HAGGI' : haggi, 'TEAM_CD' : teamCd },
			timeout : 60000,		// 제한시간 지정(1000 = 1s)
			async : true,			// 요청제한시간을 위해 비동기 통신을 한다.
			beforeSend : function(xhr){
				$("#overlay").show();

				xhr.setRequestHeader("X-Requested-With","XMLHttpRequest"); 
				xhr.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
				xhr.setRequestHeader("Progma","no-cache");
			},
			complete : function(xhr, status) {
				$("#overlay").hide();
			},
			success : function(data) {
				if(data != null && data != '') {
					// 성공 후 서버에서 받은 데이터 처리
					var result = jQuery.parseJSON(data);
					if (result.process != undefined) {
						if (result.process == true) {
							$('#'+targetId+'_REG_NAME').val(result.NAME);						// 성명 바인딩
							$('#'+targetId+'_REG_MAJOR').val(result.MAJOR);					// 학과 바인딩
							$('#'+targetId+'_REG_CHECK_YN').val('Y');								// 체크여부 상태
							$('#'+targetId+'_REG_USER_NO').prop('readonly', true);			// 학번 변경불가하도록 변경
							$('#'+targetId+' #btnGetUser').hide();										// 버튼 숨김
						} else {
							if(result.message != undefined) alert(result.message);
							else alert('유효한 학번이 아닙니다.');
							
							$('#'+targetId+'_REG_USER_NO').val('');
						}
					} else {
						alert('수강생 정보 조회에 실패하였습니다.');
					}
				} else {
					alert('수강생 정보 조회에 실패하였습니다.');
				}
			},
			error : function(xhr, status, error){
				if (error == 'timeout') {
					alert('접속이 지연되고 있습니다. 잠시 후 다시 요청하세요.');		//Request Time out.
					xhr.abort();
				} else if (xhr.status==0) {
					alert('요청을 취소하였습니다.');		//You are offline!!n Please Check Your Network. (요청 중 페이지 새로고침 시 발생)
				} else if (xhr.status == 404) {
					alert('요청페이지가 잘못되었습니다. 관리자에게 문의하세요.');	//Requested URL not found.
				} else if (xhr.status == 500) {
					alert('요청이 취소되었습니다. 잠시 후 다시 요청하세요.');	//Internel Server Error.
				} else if (error == 'parsererror') {
					alert(xhr.responseText + ' 관리자에게 문의하세요.');				//Error.nParsing JSON Request failed.
				} else {
					alert('요청 오류 : '+xhr.responseText);
				}
			}
		});
	});
	
	// 초기설정
	$('#btnHeader').css('display', 'table-cell');			// 자바스크립트 사용시만 추가/삭제 이벤트 가능하도록 설정
	$('#writeInfo').show();
	newEntry1($('#winTable>table>tbody'));
});

function newEntry1(tbody) {
	var maxId = 0;
	tbody.find('tr').each(function(){
		var iVal = parseInt(this.id.replace('ENTRY1_ROW_','')) + 1;
		maxId = iVal > maxId ? iVal : maxId;
	});
	var rowId = 'ENTRY1_ROW_' + maxId;

	var newRow = '';
	newRow += '<tr id="'+rowId+'">';
	newRow += '	<td class="center">';
	newRow += '		<input type="hidden" name="REG_CHECK_YN[]" id="'+rowId+'_REG_CHECK_YN" value="N" />';
	newRow += '		<a href="#n" class="btnDelEntry" style="text-decoration:none" title="클릭하시면 해당 행이 삭제됩니다."><img src="/res/img/sub/cont/98/minus.png" align="middle" alt="삭제" /> (삭제)</a>';
	newRow += '	</td>';
	newRow += '	<td><input name="REG_USER_NO[]" id="'+rowId+'_REG_USER_NO" style="width: 72%;" maxlength="20" type="text" value="" />&nbsp;&nbsp;<input type="button" id="btnGetUser" class="submit_tab_info" value="확인" /></td>';
	newRow += '	<td><input name="REG_NAME[]" id="'+rowId+'_REG_NAME" style="width: 98%;" maxlength="50" type="text" value="" readonly /></td>';
	newRow += '	<td><input name="REG_MAJOR[]" id="'+rowId+'_REG_MAJOR" style="width: 98%;" maxlength="100" type="text" maxlength="10" value="" readonly /></td>';
	newRow += '	<td><input name="REG_GRADE[]" id="'+rowId+'_REG_GRADE" style="width: 98%;" maxlength="3" class="center" type="text" value="" /></td>';
	newRow += '</tr>';

	$(newRow).appendTo(tbody);
}