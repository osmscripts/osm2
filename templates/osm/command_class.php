<?php
/* @var string $command */
/* @var string $namespace */
/* @var string $class */
?>
<?php echo '<?php' ?>


namespace <?php echo $namespace ?>;

use Osm\Core\App;
use Osm\Framework\Console\Command;

/**
 * `<?php echo $command ?>` shell command class.
 *
 * @property
 */
class <?php echo $class ?> extends Command
{
    public function default($property) {
        global $osm_app; /* @var App $osm_app */

        switch ($property) {
        }
        return parent::default($property);
    }

    public function run() {
    }
}