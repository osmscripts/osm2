<?php
/* @var string $package */
/* @var string $namespace */
/* @var string $test_namespace */
/* @var string $sample_namespace */
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
        "php": ">=7.1"
    },
    "extra": {
        "branch-alias": {
            "dev-master": "1.x-dev"
        },
        "osm": {
            "component_pools": {
                "src": {
                    "module_path": "*/*/Module.php",
                    "theme_path": "*/*/theme.php"
                },
                "samples": {
                    "module_path": "*/Module.php",
                    "testing": true
                }
            }
        }
    }
}