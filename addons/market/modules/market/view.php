<?php
if (!defined('FLUX_ROOT')) exit;

//$this->loginRequired();

$title = 'Viewing Shop';

require_once 'Flux/TemporaryTable.php';

try {
    # Merge item_db and item_db2
	$fromItemTables = ["{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2"];
	$tableItemName = "{$server->charMapDatabase}.items";
	$tempItemTable = new Flux_TemporaryTable($server->connection, $tableItemName, $fromItemTables);

    $tableVendingName = "{$server->charMapDatabase}.vending_stat";

    $character = $params->get('character');

    // Statement parameters, joins and conditions.
    $bind        = [$character];
    $sqlpartial  = " JOIN $tableItemName ON $tableItemName.id = $tableVendingName.nameid";
    $sqlpartial .= " WHERE $tableVendingName.owner = ?";


    $col = "$tableVendingName.*, $tableItemName.name_japanese, $tableItemName.slots, $tableItemName.type";

    $sth = $server->connection->getStatement("SELECT $col FROM $tableVendingName $sqlpartial");
    $sth->execute($bind);

    $items = $sth->fetchAll();

    # Set Cards
    if ($items) {
        $cardIDs = [];

        foreach ($items as $item) {
            $item->cardsOver = -$item->slots;

            if ($item->card0) {
                $cardIDs[] = $item->card0;
                $item->cardsOver++;
            }
            if ($item->card1) {
                $cardIDs[] = $item->card1;
                $item->cardsOver++;
            }
            if ($item->card2) {
                $cardIDs[] = $item->card2;
                $item->cardsOver++;
            }
            if ($item->card3) {
                $cardIDs[] = $item->card3;
                $item->cardsOver++;
            }

            if ($item->card0 == 254 || $item->card0 == 255 || $item->card0 == -256 || $item->cardsOver < 0) {
                $item->cardsOver = 0;
            }
        }

        if ($cardIDs) {
            $ids = implode(',', array_fill(0, count($cardIDs), '?'));

            $sql = "SELECT id, name_japanese FROM $tableItemName WHERE id IN ($ids)";
            $sth = $server->connection->getStatement($sql);

            $sth->execute($cardIDs);
            $temp = $sth->fetchAll();
            if ($temp) {
                foreach ($temp as $card) {
                    $cards[$card->id] = $card->name_japanese;
                }
            }
        }
    }

}
catch (Exception $e) {
    if (isset($tempItemTable) && $tempItemTable) {
        // Ensure table gets dropped.
        $tempItemTable->drop();
    }

    // Raise the original exception.
    $class = get_class($e);
    throw new $class($e->getMessage());
}
?>
