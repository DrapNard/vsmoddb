<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Welcome to VSModDB</h1>
            <p>The ultimate database for Vintage Story mods</p>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>Latest Mods</h2>
                </div>
                <div class="card-body">
                    <?php if (empty($latestMods)): ?>
                        <p>No mods found</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($latestMods as $mod): ?>
                                <li class="list-group-item">
                                    <a href="/mod/<?= $mod['assetid'] ?>">
                                        <?= htmlspecialchars($mod['name']) ?>
                                    </a>
                                    <small class="text-muted">by <?= htmlspecialchars($mod['username']) ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>Trending Mods</h2>
                </div>
                <div class="card-body">
                    <?php if (empty($trendingMods)): ?>
                        <p>No mods found</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($trendingMods as $mod): ?>
                                <li class="list-group-item">
                                    <a href="/mod/<?= $mod['assetid'] ?>">
                                        <?= htmlspecialchars($mod['name']) ?>
                                    </a>
                                    <small class="text-muted"><?= number_format($mod['downloads']) ?> downloads</small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>