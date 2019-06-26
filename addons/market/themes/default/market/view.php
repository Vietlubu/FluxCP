<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Viewing Shop</h2>
<table class="horizontal-table">
    <tbody>
        <tr>
            <?php if($items[0]->type == 1): ?>
                <th colspan="2">Buying Shop Information</th>
            <?php else: ?>
                <th colspan="2">Vending Shop Information</th>
            <?php endif; ?>
        </tr>
        <tr>
            <th align="right">Shop Name</th>
            <td><?php echo $items[0]->shop; ?></td>
        </tr>
        <tr>
            <th align="right">Owner</th>
            <td><?php echo $items[0]->owner; ?></td>
        </tr>
        <tr>
            <th align="right">Location</th>
            <td><?php echo "{$items[0]->map} {$items[0]->x}/{$items[0]->y}"; ?></td>
        </tr>
    </tbody>
</table>

<br>

<table class="horizontal-table">
    <thead>
        <tr align="left">
            <?php if($items[0]->type == 1): ?>
                <th colspan="5">Buying Shop Items</th>
            <?php else: ?>
                <th colspan="5">Vending Shop Items</th>
            <?php endif; ?>
        </tr>
        <tr>
            <th>Item ID</th>
            <th>Item Name</th>
            <th>Amount</th>
            <th>Price</th>
            <th>Cards</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo $this->linkToItem($item->nameid, $item->nameid) ?></td>
                <td>
                    <?php if (($item->type == 4 || $item->type == 5) && $item->refine > 0): ?>
                        +<?php echo $item->refine; ?>
                    <?php endif; ?>
                    <?php echo $this->linkToItem($item->nameid, (($item->slots)) ? "$item->name_japanese [$item->slots]" : $item->name_japanese) ?>
                </td>
                <td><?php echo number_format($item->amount) ?></td>
                <td align="right"><?php echo number_format($item->price) ?> z</td>
                <td>
                    <ul>
                        <?php for($i=0; $i <= 3; $i++): $cardIndex = "card$i" ?>
                            <?php if($item->$cardIndex && ($item->type == 4 || $item->type == 5) && $item->$cardIndex != 254 && $item->$cardIndex != 255 && $item->$cardIndex != -256): ?>
                                <li>
                                    <?php if (!empty($cards[$item->$cardIndex])): ?>
                                        <?php echo $this->linkToItem($item->$cardIndex, $cards[$item->$cardIndex]) ?>
                                    <?php else: ?>
                                        <?php echo $this->linkToItem($item->$cardIndex, $item->$cardIndex) ?>
                                    <?php endif ?>
                                </li>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <?php if (!$item->card0 && !$item->card1 && !$item->card2 && !$item->card3): ?>
                            <span class="not-applicable">None</span>
                        <?php endif; ?>
                    </ul>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
