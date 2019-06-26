<?php
if (!defined('FLUX_ROOT')) exit;

//$this->loginRequired();

$title = 'Buying Items';

require_once 'Flux/TemporaryTable.php';

try {
    # Merge item_db and item_db2
    $fromItemTables = ["{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2"];
    $tableItemName = "{$server->charMapDatabase}.items";
    $tempItemTable = new Flux_TemporaryTable($server->connection, $tableItemName, $fromItemTables);

    $tableVendingName = "{$server->charMapDatabase}.vending_stat";

    // Statement parameters, joins and conditions.
    $bind        = [];
    $sqlpartial  = "JOIN $tableItemName ON $tableItemName.id = $tableVendingName.nameid ";
    $sqlpartial .= "WHERE $tableVendingName.type = 1 "; // 0 = Selling (Vending) | 1 = Buying

    $itemId = $params->get('item_id');
    $itemName = $params->get('name');
    $itemType = $params->get('type');

    if ($itemId) {
        $sqlpartial .= "AND $tableItemName.id = ? ";
        $bind[]      = $itemId;
    }

    if ($itemName) {
        $sqlpartial .= "AND $tableItemName.name_japanese LIKE ? ";
        $bind[]      = "%$itemName%";
    }

    if ($itemType > -1) {
        if (count($itemTypeSplit = explode('-', $itemType)) == 2) {
            $itemType = $itemTypeSplit[0];
            $itemType2 = $itemTypeSplit[1];
        }
        if (is_numeric($itemType) && (floatval($itemType) == intval($itemType))) {
            $itemTypes = Flux::config('ItemTypes')->toArray();
            if (array_key_exists($itemType, $itemTypes) && $itemTypes[$itemType]) {
                $sqlpartial .= "AND $tableItemName.type = ? ";
                $bind[]      = $itemType;
            } else {
                $sqlpartial .= "AND $tableItemName.type IS NULL ";
            }

            if (count($itemTypeSplit) == 2 && is_numeric($itemType2) && (floatval($itemType2) == intval($itemType2))) {
                $itemTypes2 = Flux::config('ItemTypes2')->toArray();
                if (array_key_exists($itemType, $itemTypes2) && array_key_exists($itemType2, $itemTypes2[$itemType]) && $itemTypes2[$itemType][$itemType2]) {
                    $sqlpartial .= "AND $tableItemName.subtype = ? ";
                    $bind[]      = $itemType2;
                } else {
                    $sqlpartial .= "AND $tableItemName.subtype IS NULL ";
                }
            }
        } else {
            $typeName   = preg_quote($itemType, '/');
            $itemTypes  = preg_grep("/.*?$typeName.*?/i", Flux::config('ItemTypes')->toArray());

            if (count($itemTypes)) {
                $itemTypes   = array_keys($itemTypes);
                $sqlpartial .= "AND (";
                $partial     = '';

                foreach ($itemTypes as $id) {
                    $partial .= "$tableItemName.type = ? OR ";
                    $bind[]   = $id;
                }

                $partial     = preg_replace('/\s*OR\s*$/', '', $partial);
                $sqlpartial .= "$partial) ";
            } else {
                $sqlpartial .= "AND $tableItemName.type IS NULL ";
            }
        }
    }

    $sth = $server->connection->getStatement("SELECT COUNT(DISTINCT($tableVendingName.owner)) AS total FROM $tableVendingName $sqlpartial");
    $sth->execute($bind);

    $paginator = $this->getPaginator($sth->fetch()->total);

    $sortable = ["nameid", "price", "amount", "name_japanese"];
    $paginator->setSortableColumns($sortable);

    $col = "$tableVendingName.*, $tableItemName.name_japanese, $tableItemName.slots, $tableItemName.type";

    $sql = $paginator->getSQL("SELECT $col FROM $tableVendingName $sqlpartial");
    $sth = $server->connection->getStatement($sql);
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
