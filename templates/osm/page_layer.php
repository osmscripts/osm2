<?php
/* @var string $css_modifier CSS modifier added to <body> element to
 * distinguish this page type from the others */
?>
<?php echo '<?php' ?>


use Osm\Framework\Views\Views\Container;

return [
    '@include' => ['page'],
    '#page' => [
        'modifier' => '<?php echo $css_modifier ?>',
        'content' => Container::new([
            'id' => 'content',
            'views' => [
                // add page-specific views here
            ],
        ]),
    ],
];