<div>
    Bonjour <?php echo $request->getGuestName(); ?>,<br>
    Vous avez été invité par <?php echo $request->getOwner()->getFullName(); ?> pour une mission.
    Toutes les informations sont disponibles sur le lien suivant:
    <a href="http://<?php echo $_SERVER['SERVER_NAME'] ?>/index.php?token=<?php echo $request->getInvitationToken()?>"><?php echo T_('Demandes I2M'); ?></a><br>
    Voici votre jeton d'invité : <b><?php echo $request->getInvitationToken(); ?></b>
</div>
<br>
-----------------------------------------
<br>
<div>
    Hello <?php echo $request->getGuestName(); ?>,<br>
    You have been invited by <?php echo $request->getOwner()->getFullName(); ?> for a mission.
    All the information is available on the following link:
    <a href="http://<?php echo $_SERVER['SERVER_NAME'] ?>/index.php?token=<?php echo $request->getInvitationToken()?>"><?php echo T_('I2M Requests'); ?></a><br>
    Here is your guest token: <b><?php echo $request->getInvitationToken(); ?></b>
</div>