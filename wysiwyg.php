<?php
################################################################################
# @Name : wysiwyg.php
# @Description : text editor
# @Call : /ticket.php
# @Parameters :
# @Author : Flox
# @Create : 06/03/2013
# @Update : 08/09/2020
# @Version : 3.2.4 p2
################################################################################
//!\ MODIFIER PAR NOS SOINS

//initialize variables
if(!isset($rright['ticket_description'])) {$rright['ticket_description'] = '';}
if(!isset($rright['ticket_thread_add'])) {$rright['ticket_thread_add'] = '';}
if(!isset($rright['ticket_description_insert_image'])) {$rright['ticket_description_insert_image'] = '';}

?>
<script type="text/javascript" src="./components/jquery-hotkeys/jquery.hotkeys.js"></script>
<script type="text/javascript" src="./components/bootstrap-wysiwyg/js/bootstrap-wysiwyg.min.js"></script>
<script type="text/javascript">
	//load text from editor to input value
	function loadVal(){
		<?php
		///!\ - OLD if($_GET['page']=='ticket'){
		/*/!\ - NEW */if($_GET['page']=='ticket' || $_GET['page']=='request') {
			if ($rright['ticket_description']!=0 || $_GET['action']=='new')
			{
				echo '
				text = $("#editor").html();
				document.myform.text.value = text;
				';
			}
		}
		if($rright['ticket_thread_add'])
		{
			echo '
			text2 = $("#editor2").html();
			document.myform.text2.value = text2;
			';
		}

		if($_GET['page']=='procedure')
		{
			echo '
			text = $("#editor").html();
			document.myform.text.value = text;
			';
		}
		?>
	}

jQuery(function($) {
	$('#editor').aceWysiwyg({
		toolbarStyle: 2,
		toolbar:
		[
			<?php
				//display a light font function if mobile is detected
				if(!$mobile)
				{
					echo '
						{
							name:\'font\',
							title:\''.T_('Police').'\',
							values:[\'Some Special Font!\',\'Arial\',\'Verdana\',\'Comic Sans MS\',\'Custom Font!\']
						},
						null,
						{
							name:\'fontSize\',
							title:\''.T_('Taille').'\',
							values:{1 : \'Size#1 Text\' , 2 : \'Size#1 Text\' , 3 : \'Size#3 Text\' , 4 : \'Size#4 Text\' , 5 : \'Size#5 Text\'}
						},
						null,
						{name:\'bold\', title:\''.T_('Gras').'\'},
						{name:\'italic\', title:\''.T_('Italique').'\'},
						{name:\'underline\', title:\''.T_('Souligner').'\'},
						null,
						{name:\'insertunorderedlist\', title:\''.T_('Liste à puces').'\'},
						{name:\'insertorderedlist\', title:\''. T_('Liste numéroté').'\'},
						{name:\'outdent\', title:\''.T_('Diminuer le retrait').'\'},
						{name:\'indent\', title:\''.T_('Augmenter le retrait').'\'},
						null,
						{name:\'justifyleft\',title:\''.T_('Aligner à gauche').'\'},
						{name:\'justifycenter\',title:\''.T_('Centrer').'\'},
						{name:\'justifyright\',title:\''.T_('Aligner à droite').'\'},
						{name:\'justifyfull\',title:\''.T_('Justifier').'\'},
						null,
						{
							name:\'createLink\',
							title:\''.T_('Insérer un lien').'\',
							placeholder:\''.T_('Insérer un lien').'\',
							button_text:\''.T_('Ajouter').'\'
						},
						null,
						';
							if($rright['ticket_description_insert_image']!=0)
							{
								echo '
								{
									name:\'insertImage\',
									title:\''.T_('Insérer une image').'\',
									placeholder:\''.T_('Insérer une image').'\',
									button_class:\'btn-inverse\',
									//choose_file:false,//hide choose file button
									button_text:\''.T_('Sélectionner une image').'\',
									button_insert_class:\'btn-pink\',
									button_insert:\''.T_('Insérer une image').'\'
								},
								null,
								';
							}
						echo '
						{name:\'foreColor\',title:\''.T_('Couleur').'\',values:[\'red\',\'green\',\'blue\',\'orange\',\'black\'],},
						null,
						{name:\'undo\',title:\''.T_('Annuler la modification').'\'},
						{name:\'redo\',title:\''.T_('Rétablir').'\'}
					';
				} else {
					echo '
						{
							name:\'font\',
							title:\''.T_('Police').'\',
							values:[\'Some Special Font!\',\'Arial\',\'Verdana\',\'Comic Sans MS\',\'Custom Font!\']
						},
						null,
						{
							name:\'fontSize\',
							title:\''.T_('Taille').'\',
							values:{1 : \'Size#1 Text\' , 2 : \'Size#1 Text\' , 3 : \'Size#3 Text\' , 4 : \'Size#4 Text\' , 5 : \'Size#5 Text\'}
						},
						null,
						{name:\'bold\', title:\''.T_('Gras').'\'},
						{name:\'italic\', title:\''.T_('Italique').'\'},
						{name:\'underline\', title:\''.T_('Souligner').'\'},
					';
				}
			?>
		],
	});
	$('#editor2').aceWysiwyg({
		toolbarStyle: 2,
		toolbar:
		[
			<?php
				//display a light font function if mobile is detected
				if(!$mobile)
				{
					echo '
						{
							name:\'font\',
							title:\''.T_('Police').'\',
							values:[\'Some Special Font!\',\'Arial\',\'Verdana\',\'Comic Sans MS\',\'Custom Font!\']
						},
						null,
						{
							name:\'fontSize\',
							title:\''.T_('Taille').'\',
							values:{1 : \'Size#1 Text\' , 2 : \'Size#1 Text\' , 3 : \'Size#3 Text\' , 4 : \'Size#4 Text\' , 5 : \'Size#5 Text\'}
						},
						null,
						{name:\'bold\', title:\''.T_('Gras').'\'},
						{name:\'italic\', title:\''.T_('Italique').'\'},
						{name:\'underline\', title:\''.T_('Souligner').'\'},
						null,
						{name:\'insertunorderedlist\', title:\''.T_('Liste à puces').'\'},
						{name:\'insertorderedlist\', title:\''. T_('Liste numéroté').'\'},
						{name:\'outdent\', title:\''.T_('Diminuer le retrait').'\'},
						{name:\'indent\', title:\''.T_('Augmenter le retrait').'\'},
						null,
						{name:\'justifyleft\',title:\''.T_('Aligner à gauche').'\'},
						{name:\'justifycenter\',title:\''.T_('Centrer').'\'},
						{name:\'justifyright\',title:\''.T_('Aligner à droite').'\'},
						{name:\'justifyfull\',title:\''.T_('Justifier').'\'},
						null,
						{
							name:\'createLink\',
							title:\''.T_('Insérer un lien').'\',
							placeholder:\''.T_('Insérer un lien').'\',
							button_text:\''.T_('Ajouter').'\'
						},
						null,
						';
							if($rright['ticket_description_insert_image']!=0)
							{
								echo '
								{
									name:\'insertImage\',
									title:\''.T_('Insérer une image').'\',
									placeholder:\''.T_('Insérer une image').'\',
									button_class:\'btn-inverse\',
									//choose_file:false,//hide choose file button
									button_text:\''.T_('Sélectionner une image').'\',
									button_insert_class:\'btn-pink\',
									button_insert:\''.T_('Insérer une image').'\'
								},
								null,
								';
							}
						echo '
						{name:\'foreColor\',title:\''.T_('Couleur').'\',values:[\'red\',\'green\',\'blue\',\'orange\',\'black\'],},
						null,
						{name:\'undo\',title:\''.T_('Annuler la modification').'\'},
						{name:\'redo\',title:\''.T_('Rétablir').'\'}
					';
				} else {
					echo '
						{
							name:\'font\',
							title:\''.T_('Police').'\',
							values:[\'Some Special Font!\',\'Arial\',\'Verdana\',\'Comic Sans MS\',\'Custom Font!\']
						},
						null,
						{
							name:\'fontSize\',
							title:\''.T_('Taille').'\',
							values:{1 : \'Size#1 Text\' , 2 : \'Size#1 Text\' , 3 : \'Size#3 Text\' , 4 : \'Size#4 Text\' , 5 : \'Size#5 Text\'}
						},
						null,
						{name:\'bold\', title:\''.T_('Gras').'\'},
						{name:\'italic\', title:\''.T_('Italique').'\'},
						{name:\'underline\', title:\''.T_('Souligner').'\'},
					';
				}
			?>
		],
	});
    $('.editor').aceWysiwyg({
        toolbarStyle: 2,
        toolbar:
            [
                <?php
                //display a light font function if mobile is detected
                if(!$mobile)
                {
                    echo '
						{
							name:\'font\',
							title:\''.T_('Police').'\',
							values:[\'Some Special Font!\',\'Arial\',\'Verdana\',\'Comic Sans MS\',\'Custom Font!\']
						},
						null,
						{
							name:\'fontSize\',
							title:\''.T_('Taille').'\',
							values:{1 : \'Size#1 Text\' , 2 : \'Size#1 Text\' , 3 : \'Size#3 Text\' , 4 : \'Size#4 Text\' , 5 : \'Size#5 Text\'}
						},
						null,
						{name:\'bold\', title:\''.T_('Gras').'\'},
						{name:\'italic\', title:\''.T_('Italique').'\'},
						{name:\'underline\', title:\''.T_('Souligner').'\'},
						null,
						{name:\'insertunorderedlist\', title:\''.T_('Liste à puces').'\'},
						{name:\'insertorderedlist\', title:\''. T_('Liste numéroté').'\'},
						{name:\'outdent\', title:\''.T_('Diminuer le retrait').'\'},
						{name:\'indent\', title:\''.T_('Augmenter le retrait').'\'},
						null,
						{name:\'justifyleft\',title:\''.T_('Aligner à gauche').'\'},
						{name:\'justifycenter\',title:\''.T_('Centrer').'\'},
						{name:\'justifyright\',title:\''.T_('Aligner à droite').'\'},
						{name:\'justifyfull\',title:\''.T_('Justifier').'\'},
						null,
						{
							name:\'createLink\',
							title:\''.T_('Insérer un lien').'\',
							placeholder:\''.T_('Insérer un lien').'\',
							button_text:\''.T_('Ajouter').'\'
						},
						null,
						';
                    if($rright['ticket_description_insert_image']!=0)
                    {
                        echo '
								{
									name:\'insertImage\',
									title:\''.T_('Insérer une image').'\',
									placeholder:\''.T_('Insérer une image').'\',
									button_class:\'btn-inverse\',
									//choose_file:false,//hide choose file button
									button_text:\''.T_('Sélectionner une image').'\',
									button_insert_class:\'btn-pink\',
									button_insert:\''.T_('Insérer une image').'\'
								},
								null,
								';
                    }
                    echo '
						{name:\'foreColor\',title:\''.T_('Couleur').'\',values:[\'red\',\'green\',\'blue\',\'orange\',\'black\'],},
						null,
						{name:\'undo\',title:\''.T_('Annuler la modification').'\'},
						{name:\'redo\',title:\''.T_('Rétablir').'\'}
					';
                } else {
                    echo '
						{
							name:\'font\',
							title:\''.T_('Police').'\',
							values:[\'Some Special Font!\',\'Arial\',\'Verdana\',\'Comic Sans MS\',\'Custom Font!\']
						},
						null,
						{
							name:\'fontSize\',
							title:\''.T_('Taille').'\',
							values:{1 : \'Size#1 Text\' , 2 : \'Size#1 Text\' , 3 : \'Size#3 Text\' , 4 : \'Size#4 Text\' , 5 : \'Size#5 Text\'}
						},
						null,
						{name:\'bold\', title:\''.T_('Gras').'\'},
						{name:\'italic\', title:\''.T_('Italique').'\'},
						{name:\'underline\', title:\''.T_('Souligner').'\'},
					';
                }
                ?>
            ],
    });
});
</script>

