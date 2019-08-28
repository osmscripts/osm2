Code generation and maintenance commands for Osm framework-based project.

## Installation ##

Normally you don't have to install this package manually, as this package comes with every package created with `osmscripts create:package` command. 

However you can do it if needed by using the following commands (add `-g` switch to add the package to global Composer installation):

Run the following commands in the directory of the Osm framework-based project:

	cd {project_dir}
	composer config repositories.osmscripts_core vcs git@github.com:osmscripts/core.git
	composer config repositories.osmscripts_osm vcs git@github.com:osmscripts/osm.git
	composer require osmscripts/osm

Also, install [`osmscripts/osmrunner`](https://github.com/osmscripts/osm-runner) globally:

	composer -g config repositories.osmscripts_core vcs git@github.com:osmscripts/core.git
	composer -g config repositories.osmscripts_osm_runner vcs git@github.com:osmscripts/osm-runner.git
	composer -g require osmscripts/osm-runner

## License And Credits ##

Copyright (C) 2019 - present UAB "Softnova".

All files of this package are licensed under [GPL-3.0](/LICENSE).
