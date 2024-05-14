<?php
/**
 * This script fetches and displays fillable documents based on the state and category of the ticket.
 */

// Get the state and category from the global row. If they are not set, default to -1.
$state_id = $globalrow['state'] ?? -1;
$category_id = $globalrow['category'] ?? -1;

// Prepare a SQL query to fetch the title and path of fillable documents based on the state and category.
$query = "SELECT `title`,`path` FROM `tfillable_documents_by_state` WHERE `state_id`=:state_id
              UNION
              SELECT `title`,`path` FROM `tfillable_documents_by_category` WHERE `category_id`=:category_id";
$stmt = $db->prepare($query);

// Bind the state and category parameters to the SQL query.
$stmt->bindParam(':state_id', $state_id,);
$stmt->bindParam(':category_id', $category_id);

// Execute the SQL query.
$stmt->execute();

// If the SQL query returned one or more rows, display the fillable documents.
if ($stmt->rowCount() > 0) {
    echo '<div class="form-group row">
                            <div class="col-sm-2 col-form-label text-sm-right pr-0">
        <label class="mb-0" for="attachment">' . T_('Documents à remplir') . ' :</label>
       </div>
       <div class="col-sm-5">
        <table border="1" style="border:1px solid #D8D8D8; min-width:265px;" >
         <tr>
             <td style="padding:15px;">';

    // Fetch each fillable document and display it.
    while ($document = $stmt->fetch()) {
        echo '
        <div class="p-1"></div>
        <a target="_blank" title="' . $document['title'] . '" href="' . $document['path'] . '" style="text-decoration:none">
            <i class="fa fa-file text-info text-120"></i> ' .
        $document['title'] . '
        </a>';
    }

    echo '
             </td>
         </tr>
        </table>
       </div>
       </div>';
}
