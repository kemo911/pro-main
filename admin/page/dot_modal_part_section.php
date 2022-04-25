<div class="col-md-12" id="dotModalPartSection" style="margin: 10px 0;">
    <div class="dot-group dot-group-A" style="display: none;">
        <table class="table table-bordered">
            <tr>
                <th>Pièces</th>
                <th>Description</th>
                <th># Hrs</th>
                <th>Prix</th>
            </tr>
            <?php foreach ([ 0 => 'MLR TOIT G.', 1 => 'MLR TOIT D.' ] as $itemId => $groupItem): ?>
                <?php include __DIR__ . '/group_item.php'; ?>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="dot-group dot-group-B" style="display: none;">
        <table class="table table-bordered">
            <tr>
                <th>Pièces</th>
                <th>Description</th>
                <th># Hrs</th>
                <th>Prix</th>
            </tr>
            <?php foreach ([ 2 => 'MLR Sitière', 3 => 'Lêche vitre', 4 => 'Appliqué' ] as $itemId => $groupItem): ?>
                <?php include __DIR__ . '/group_item.php'; ?>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="dot-group dot-group-C" style="display: none;">
        <table class="table table-bordered">
            <tr>
                <th>Pièces</th>
                <th>Description</th>
                <th># Hrs</th>
                <th>Prix</th>
            </tr>
            <?php foreach ([ 5 => 'MLR VITRE' ] as $itemId => $groupItem): ?>
                <?php include __DIR__ . '/group_item.php'; ?>
            <?php endforeach; ?>
        </table>
    </div>
</div>