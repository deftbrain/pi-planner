# Set up the PI Planner

## Common
1. Install [Docker Engine][3] 17.9+ (for supporting [extension fields][1]);
1. Install [Docker Compose][4] 1.28+ (for supporting [profiles][2]);

## Production
1. Register a domain name;
1. [Generate a VersionOne API key][5];
1. [Register a single-page application][6] with the Microsoft Identity platform and add the following redirect URIs
 (replace placeholders with the relevant values):
    * ${ADMIN_URL}/#/login
    * ${CLIENT_URL}/login
1. Copy the `env-vars.dist.sh` file outside of the project folder and set values relevant to the environment;
1. Load the environment variables to the current shell session:

        source /path/to/env-vars.sh

1. Build Docker images:

        docker-compose -f docker-compose.yml -f docker-compose.build.yml -f docker-compose.${APP_ENV}.yml build

1. Push Docker images if you gonna use the images on a different machine:

        docker-compose -f docker-compose.yml -f docker-compose.prod.yml push

1. Compile the final config to a different directory:

        docker-compose -f docker-compose.yml -f docker-compose.prod.yml config > /tmp/docker-compose.yml

1. Upload the final config to the different machine if needed:

        scp /tmp/docker-compose.yml SERVER_IP:/var/www/pi-planner/
        
1. [Restore][7] data volume backups if needed;

1. Start up the application:

        dcdocker-compose up -d


[1]: https://docs.docker.com/compose/compose-file/compose-file-v3/#extension-fields
[2]: https://docs.docker.com/compose/profiles/
[3]: https://docs.docker.com/engine/install/
[4]: https://docs.docker.com/compose/install/
[5]: https://community.versionone.com/Digital.ai_Agility_Integrations/Developer_Library/Getting_Started/API_Authentication/Access_Token_Authentication
[6]: https://docs.microsoft.com/en-us/azure/active-directory/develop/quickstart-register-app
[7]: ./backuping-and-restoring-data-volumes.md
