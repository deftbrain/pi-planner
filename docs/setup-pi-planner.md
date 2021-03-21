# Set up the PI Planner

## Common
1. Install [Docker Engine][3] 17.9+ (for supporting [extension fields][1]);
1. Install [Docker Compose][4] 1.28+ (for supporting [profiles][2]);
1. [Generate a VersionOne API key][5];
1. [Register a single-page application][6] with the Microsoft Identity platform and add the following redirect URIs
 (replace placeholders with the relevant values):
    * ${ADMIN_URL}/#/login
    * ${CLIENT_URL}/login

## Production
1. Register a domain name;
1. Copy the `env-vars.dist.sh` file outside of the project folder and set values relevant to the environment;
1. Load the environment variables to the current shell session:

        source /path/to/env-vars.sh

1. Copy issue-tracker-specific configs to the `config` directory to enable it:

        # Yes, it sucks, but currently I have no time for moving issue-tracker-specific code into bundles 
        cp api/src/Integration/VersionOne/Resources/config/services.yaml api/config/services/version_one.yaml
        cp api/src/Integration/VersionOne/Resources/config/httplug.yaml api/config/packages/httplug_version_one.yaml
        cp api/src/Integration/VersionOne/Resources/config/messenger.yaml api/config/packages/messenger_version_one.yaml
 
1. Build Docker images:

        docker-compose -f docker-compose.yml -f docker-compose.build.yml -f docker-compose.prod.yml build

1. If you gonna run the web application on the same machine, extend a common configuration
 with a production-specific one to simplify next docker-compose commands:
    
        ln -s docker-compose.prod.yml docker-compose.override.yml

1. If you gonna run the web application on a different machine:
    1. Push the images to your container registry:

            docker-compose push

    1. Copy needed files to the target machine:

            scp docker-compose.yml /path/to/env-vars.sh SERVER_IP:/var/www/pi-planner/
            
    1. Extend a common configuration with a production-specific one:

            scp docker-compose.prod.yml SERVER_IP:/var/www/pi-planner/docker-compose.override.yml

    1. Log in to the target machine and export the environment variables into the current shell session:

            cd /var/www/pi-planner
            source env-vars.sh

    1. Do the next commands on the target machine as well;

1. [Restore][7] data volume backups if needed;

1. Start up the application:

        docker-compose up -d

1. Import the initial data if you didn't use a backup:

        docker-compose exec api bin/console version-one:import-assets -v

1. Add importing VersionOne assets to `crontab` on a host machine:

        # Import all assets every 15 minutes
        */15 * * * * flock -n /tmp/v1-import.lock -c 'cd PATH_TO_PROJECT && docker-compose exec api bin/console version-one:import-assets -v'
        # Import workitems only every minute
        * * * * * flock -n /tmp/v1-import.lock -c 'cd PATH_TO_PROJECT && docker-compose exec api bin/console version-one:import-assets PrimaryWorkitem -i'

[1]: https://docs.docker.com/compose/compose-file/compose-file-v3/#extension-fields
[2]: https://docs.docker.com/compose/profiles/
[3]: https://docs.docker.com/engine/install/
[4]: https://docs.docker.com/compose/install/
[5]: https://community.versionone.com/Digital.ai_Agility_Integrations/Developer_Library/Getting_Started/API_Authentication/Access_Token_Authentication
[6]: https://docs.microsoft.com/en-us/azure/active-directory/develop/quickstart-register-app
[7]: ./backuping-and-restoring-data-volumes.md
