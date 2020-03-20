<?= "<?php " ?>
return [
    'components' => [

        <?php foreach ($services as $alias => $config): ?>
            '<?= $alias?>' => [
                <?php foreach ($config as $name => $value): ?>
                    '<?= $name?>' => '<?= $value?>'
                <?php endforeach; ?>
            ],
        <?php endforeach; ?>

    ]
];
