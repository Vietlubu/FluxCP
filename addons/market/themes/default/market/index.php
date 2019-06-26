<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Showing currently open shops.</h2>
<?php if ($shops): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('owner', 'Character Name') ?></th>
		<th><?php echo $paginator->sortableColumn('shop', 'Shop Name') ?></th>
		<th>Location</th>
	</tr>
	<?php foreach ($shops as $shop): ?>
	<tr>
		<td>
			<?php echo $shop->owner; ?>
		</td>
        <td>
            <?php
                if ($shop->type == 0) {
                    echo "<spam style='font-weight: bold;' title='Selling Shop'>[S] </spam>";
                } else {
                    echo "<spam style='font-weight: bold;' title='Buying Shop'>[B] </spam>";
                }
            ?>
            <a href="<?php echo $this->url('market', 'view', ["character" => $shop->owner]); ?>"><?php echo "$shop->shop"; ?></a>
        </td>
        <td>
            <pre><?php echo "$shop->map $shop->x/$shop->y"; ?></pre>
        </td>

	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>No items found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
