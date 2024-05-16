<?php
/*************************************************************************************/
/** Permet de gérer l'input des valideur différent au MIO et dans les autres labo*****/
/** la version standard est un select libre ******************************************/
/*************************************************************************************/
?>

<select autofocus style="display:inline; <?php if($mobile) {echo 'max-width:240px;';} else {if($rright['ticket_user_company']) {echo 'width:auto;';} else {echo 'max-width:269px;';}}?>" class="form-control chosen-select select2" id="validators" name="validators[]" multiple="multiple" <?php echo ($disabled) ? 'disabled' : '' ?>>
<?php
  foreach($users as $validator) {
    $isSelected = $request->hasValidator($validator->getId());
?>
    <option value="<?php echo $validator->getId() ?>" <?php if ($isSelected) { echo 'selected'; } ?> ><?php echo $validator->getFullName()." (".$validator->getLabo().")"; ?></option>
<?php
  }
?>

<!-- Si option de valideur fixe -->
<?php
// Load extra parameters for request
require_once('models/tool/parameters.php');
use Models\Tool\Parameters;
$parameters = Parameters::getInstance();
if($parameters->isFixedValidators()){
	echo "<script>
		$( '#budget-data' ).change(function() {
			var idSubCat=$(this).val();
			//alert(idSubCat);
			findValidators(idSubCat);
		});

		function findValidators(idSubCat) {
			var url = '/template/request/findValidator.php?idSubCat='+idSubCat;
			console.log ('[findValidators] Calling '+url);
			$.ajax({
				url: url,
				async: true,
				dataType: 'json',
				success: function (jasonReturn) {
				if (jasonReturn.nbPoints[0].nb!=0) {
					$('#validators').attr('disabled',true);
					$('#validators').val(jasonReturn.nom).change();
				} else {
					$('#validators').attr('disabled',false);
					$('#validators').val(jasonReturn.nom).change();
				}
			}
			});
		}
	</script>";
}
?>
<!-- Fin partie valideur fixe -->

</select>
