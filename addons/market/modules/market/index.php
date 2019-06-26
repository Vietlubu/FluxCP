<?php
    if (!defined('FLUX_ROOT')) exit;

    //$this->loginRequired();

    $title = 'Vending Shops Items List';

    $tableVendingName = "{$server->charMapDatabase}.vending_stat";

    // Statement parameters, joins and conditions.
    $bind        = [];
    $sqlpartial = "";

    $sth = $server->connection->getStatement("SELECT COUNT(DISTINCT($tableVendingName.owner)) AS total FROM $tableVendingName $sqlpartial");
    $sth->execute($bind);

    $paginator = $this->getPaginator($sth->fetch()->total);

    $sortable = ["owner", "shop"];
    $paginator->setSortableColumns($sortable);

    $col = "$tableVendingName.*";

    $sql = $paginator->getSQL("SELECT $col FROM $tableVendingName $sqlpartial GROUP BY $tableVendingName.owner");
    $sth = $server->connection->getStatement($sql);

    $sth->execute($bind);
    $shops = $sth->fetchAll();
