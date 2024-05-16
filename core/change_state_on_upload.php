<?php
    $ticketId = $_GET['id'];
    $RETURN_STATE = 9;
    $LIQUIDATION_TO_ESTABLISH_STATE = 10;
    $query = 'SELECT * FROM `tincidents` WHERE id=:id';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $ticketId);
    $stmt->execute();
    $ticket = $stmt->fetch();
    if($ticket['state'] == $RETURN_STATE){
        $query = 'UPDATE `tincidents` SET state=:state WHERE id=:id';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':state', $LIQUIDATION_TO_ESTABLISH_STATE);
        $stmt->bindParam(':id', $ticketId);
        $stmt->execute();
    }