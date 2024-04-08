<?php 
################################################################################
# @Name : ticket_mandatory.php
# @Description : modify input color for mandatory field
# @call : ticket.php
# @parameters : 
# @Author : Flox
# @Create : 12/02/2020
# @Update : 12/02/2020
# @Version : 3.2.0
################################################################################

echo '
<script type="text/javascript">
	function CheckMandatory(){
';
		if($rright['ticket_service_mandatory'])
		{
			echo '
				var service=document.getElementById("u_service").value;
				if(service){document.getElementById("u_service").style.borderColor = "#73bd73";} else {document.getElementById("u_service").style.borderColor = "#dd6a57";}
			';
		}
		if($rright['ticket_type_mandatory'])
		{
			echo '
				var type=document.getElementById("type").value;
				if(type!=0){document.getElementById("type").style.borderColor = "#73bd73";} else {document.getElementById("type").style.borderColor = "#dd6a57";}
			';
		}
		if($rright['ticket_tech_mandatory'])
		{
			echo '
				var technician=document.getElementById("technician").value;
				if(technician!=0){document.getElementById("technician").style.borderColor = "#73bd73";} else {document.getElementById("technician").style.borderColor = "#dd6a57";}
			';
		}
		if($rright['ticket_asset_mandatory'])
		{
			echo '
				var asset_id=document.getElementById("asset_id").value;
				if(asset_id!=0){document.getElementById("asset_id").style.borderColor = "#73bd73";} else {document.getElementById("asset_id").style.borderColor = "#dd6a57";}
			';
		}
		if($rright['ticket_cat_mandatory'])
		{
			echo '
				var category=document.getElementById("category").value;
				if(category!=0){document.getElementById("category").style.borderColor = "#73bd73";} else {document.getElementById("category").style.borderColor = "#dd6a57";}
				var subcat=document.getElementById("subcat").value;
				if(subcat!=0){document.getElementById("subcat").style.borderColor = "#73bd73";} else {document.getElementById("subcat").style.borderColor = "#dd6a57";}
			';
		}
		if($rright['ticket_agency_mandatory'])
		{
			echo '
				var u_agency=document.getElementById("u_agency").value;
				if(u_agency!=0){document.getElementById("u_agency").style.borderColor = "#73bd73";} else {document.getElementById("u_agency").style.borderColor = "#dd6a57";}
			';
		}
		if($rright['ticket_title_mandatory'])
		{
			echo '
				var title=document.getElementById("title").value;
				if(title!=0){document.getElementById("title").style.borderColor = "#73bd73";} else {document.getElementById("title").style.borderColor = "#dd6a57";}
			';
		}
		if($rright['ticket_description_mandatory'])
		{
			echo '
				var editor=document.getElementById("editor");
				var editor=editor.innerHTML;
				if(editor!=0){document.getElementById("description").style.borderColor = "#73bd73";} else {document.getElementById("description").style.borderColor = "#dd6a57";}
			';
		}
		if($rright['ticket_date_hope_mandatory'])
		{
			echo '
			var date_hope=document.getElementById("date_hope").value;
			if(date_hope){document.getElementById("date_hope").style.borderColor = "#73bd73";} else {document.getElementById("date_hope").style.borderColor = "#dd6a57";}
			';
		}
		if($rright['ticket_criticality_mandatory'])
		{
			echo '
			var criticality=document.getElementById("criticality").value;
			if(criticality){document.getElementById("criticality").style.borderColor = "#73bd73";} else {document.getElementById("criticality").style.borderColor = "#dd6a57";}
			';
		}
		if($rright['ticket_priority_mandatory'])
		{
			echo '
			var priority=document.getElementById("priority").value;
			if(priority){document.getElementById("priority").style.borderColor = "#73bd73";} else {document.getElementById("priority").style.borderColor = "#dd6a57";}
			';
		}
echo '
	}
	window.onload = CheckMandatory;
</script>
';


?>