<?php

namespace OsmScripts\Osm\Commands;

use Osm\Framework\Db\MySql;
use OsmScripts\Core\Script;
use OsmScripts\Osm\Command;

/** @noinspection PhpUnused */

/**
 * `backup` shell command class.
 *
 * @property string $filename
 */
class Backup extends Command
{
    #region Properties
    public function __get($property) {
        switch ($property) {
            case 'filename':
                return $this->filename = "{$this->app->temp_path}/db.sql";
        }

        return parent::__get($property);
    }
    #endregion

    protected function configure() {
        $this->setDescription("Backs up the database");
    }

    protected function handle() {
        if ($this->app->db instanceof MySql) {
            /** @noinspection PhpParamsInspection */
            $this->handleMySql($this->app->db);
            return;
        }

        throw new \Exception(get_class($this->app->db) . "not supported");
    }

    protected function handleMySql(MySql $db) {
        osm_make_dir_for($this->filename);

        $this->shell->run("mysqldump -h \"{$db->host}\" " .
            "-u \"{$db->username}\" \"-p{$db->password}\" \"{$db->database}\" " .
            "> {$this->filename}"
        );
    }
}