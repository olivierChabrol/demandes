<?php
################################################################################
# @Name : infos.php
# @Description :  admin infos
# @Call : admin.php
# @Parameters : 
# @Author : Flox
# @Create : 12/01/2011
# @Update : 17/08/2020
# @Version : 3.2.4
################################################################################

//generate name of current version
$dedicated=substr_count($rparameters['version'], '.'); // check dedicated version
if($dedicated==2) //case current branch
{
	$vactuname=explode('.',$rparameters['version']);
	if($vactuname[2]==0) $vactuname=''; else $vactuname="($vactuname[0].$vactuname[1] patch $vactuname[2])";
} elseif($dedicated==3) { //case dedicated branch
	$vactuname=explode('.',$rparameters['version']);
	if($vactuname[2]==0) $vactuname=''; else $vactuname="($vactuname[0].$vactuname[1].$vactuname[2] patch $vactuname[3])";
} else {
	$vactuname=$rparameters['version'];
}
?>
<div class="page-header position-relative">
	<h1 class="page-title text-primary-m2">
		<i class="fa fa-info-circle text-primary-m2"></i>  <?php echo T_('Informations sur '); ?>GestSup
	</h1>
</div>
<div class="mt-4 mt-lg-0 card bcard h-auto overflow-hidden shadow border-0">
    <div class="card-body p-0 table-responsive-xl">
		<table class="table text-dark-m1 brc-black-tp10 mb-0">
			<tbody>
				<tr>
					<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-tag text-blue-m3 pr-1"></i><?php echo T_('Version'); ?> </td>
					<td class="text-95 text-default-d3"><a href="./index.php?page=changelog"><?php echo ''.$rparameters['version'].' <span style="font-size: x-small;">'.$vactuname.'</span>';?></a></td>
				</tr>
				<tr>
					<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-gavel text-blue-m3 pr-1"></i><?php echo T_('Licence'); ?></td>
					<td class="text-95 text-default-d3"><a target="_blank" href="https://fr.wikipedia.org/wiki/Licence_publique_g%C3%A9n%C3%A9rale_GNU">GPL v3</a></td>
				</tr>
				<tr>
					<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-globe text-blue-m3 pr-1"></i><?php echo T_('Site officiel'); ?></td>
					<td class="text-95 text-default-d3"><a target="_blank" href="https://gestsup.fr">GestSup.fr</a></td>
				</tr>
				<tr>
					<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-users text-blue-m3 pr-1"></i><?php echo T_('Communauté'); ?></td>
					<td class="text-95 text-default-d3"><a target="_blank" href="https://gestsup.fr/index.php?page=forum"><?php echo T_('Forum'); ?></a> (<?php echo T_("Pour toutes vos questions d'installation, de bugs, de mises à jour"); ?>.)</td>
				</tr>
				<tr>
					<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-envelope text-blue-m3 pr-1"></i><?php echo T_('Contact'); ?></td>
					<td class="text-95 text-default-d3"><a target="_blank" href="https://gestsup.fr/index.php?page=contact"><?php echo T_('Mail'); ?></a></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>