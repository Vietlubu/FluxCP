<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Buying Shops Items List</h2>
<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form class="search-form" method="get">
    <?php echo $this->moduleActionFormInputs($params->get('module')) ?>
    <p>
        <label for="item_id">Item ID:</label>
        <input type="text" name="item_id" id="item_id" value="<?php echo htmlspecialchars($params->get('item_id')) ?>" />
        ...
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($params->get('name')) ?>" />
        ...
        <label for="type">Type:</label>
        <select name="type">
            <option value="-1"<?php if (($type=$params->get('type')) === '-1') echo ' selected="selected"' ?>>
                Any
            </option>
            <?php foreach (Flux::config('ItemTypes')->toArray() as $typeId => $typeName): ?>
                <option value="<?php echo $typeId ?>"<?php if (($type=$params->get('type')) === strval($typeId)) echo ' selected="selected"' ?>>
                    <?php echo htmlspecialchars($typeName) ?>
                </option>
                <?php $itemTypes2 = Flux::config('ItemTypes2')->toArray() ?>
                <?php if (array_key_exists($typeId, $itemTypes2)): ?>
                    <?php foreach ($itemTypes2[$typeId] as $typeId2 => $typeName2): ?>
                        <option value="<?php echo $typeId ?>-<?php echo $typeId2 ?>"<?php if (($type=$params->get('type')) === ($typeId . '-' . $typeId2)) echo ' selected="selected"' ?>>
                            <?php echo htmlspecialchars($typeName . ' - ' . $typeName2) ?>
                        </option>
                    <?php endforeach ?>
                <?php endif ?>
            <?php endforeach ?>
        </select>
    </p>
    <p>
        <input type="submit" value="Search" />
        <input type="button" value="Reset" onclick="reload()" />
    </p>
</form>

<?php if ($items): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
    <thead>
        <tr>
            <th><?php echo $paginator->sortableColumn('nameid', 'Item ID') ?></th>
            <th><?php echo $paginator->sortableColumn('name_japanese', 'Item Name') ?></th>
            <th><?php echo $paginator->sortableColumn('amount', 'Amount') ?></th>
            <th><?php echo $paginator->sortableColumn('price', 'Price') ?></th>
            <th>Shop name</th>
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
            <td><a href="<?php echo $this->url('market', 'view', ["character" => $item->owner]); ?>"><?php echo "$item->shop"; ?></a></td>
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
<?php echo $paginator->getHTML() ?>
<?php else: ?>
    <p>No items found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>