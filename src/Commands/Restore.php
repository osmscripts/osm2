<?php

namespace OsmScripts\Osm\Commands;


use Osm\Framework\Db\MySql;
use OsmScripts\Osm\Command;

/** @noinspection PhpUnused */

/**
 * `restore` shell command class.
 *
 * @property string $filename
 */
class Restore extends Command
{
    #region Properties
    public function __get($property) {
        switch ($property) {
            case 'env': return $this->env = null;
            case 'filename':
                return $this->filename = "{$this->app->temp_path}/db.sql";
        }

        return parent::__get($property);
    }
    #endregion

    protected function configure() {
        $this->setDescription("Restores the database");
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
        $this->output->writeln("Dropping all tables ...");
        $this->app->db->schema->dropAllTables();

        $this->shell->run("mysql -h \"{$db->host}\" " .
            "-u \"{$db->username}\" \"-p{$db->password}\" \"{$db->database}\" " .
            "< {$this->filename}"
        );
    }
}