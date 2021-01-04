<?php
/* @var string $package */
/* @var string $namespace */
/* @var string $test_namespace */
/* @var string $sample_namespace */
/* @var string $version_constraint */
?>
{
    "name": "<?php echo $package ?>",
    "autoload": {
        "psr-4": {
            <?php echo $namespace ?>: "src/",
            <?php echo $test_namespace ?>: "tests/",
            <?php echo $sample_namespace ?>: "samples/"
        }
    },
    "require": {
        "php": ">=8.0",
        "osmphp/framework2": "<?php echo $version_constraint ?>"
    },
    "extra": {
        "osm": {
            "component_pools": {
                "src": {
                    "module_path": "*/Module.php",
                    "theme_path": "*/theme.php"
                },
                "samples": {
                    "module_path": "*/Module.php",
                    "testing": true
                }
            }
        }
    }
}