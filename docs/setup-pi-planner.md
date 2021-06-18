# Set up the PI Planner

## Common
1. Install [Docker Engine][3] 17.9+ (for supporting [extension fields][1]);
1. Install [Docker Compose][4] 1.28+ (for supporting [profiles][2]);
1. Generate an API key for an issue-tracker you are going to be integrated with: 
    * [Jira][8]
    * [VersionOne][5]
1. [Register a single-page application][6] with the Microsoft Identity platform and add the following redirect URIs
 (replace placeholders with the relevant values):
    * ${ADMIN_URL}/#/login
    * ${CLIENT_URL}/login

## Production
1. Register a domain name;
1. Copy the `env-vars.dist.sh` file outside of the project folder and set values relevant to the environment;
1. Load the environment variables to the current shell session:

        source /path/to/env-vars.sh

1. Copy issue-tracker-specific configs to the `config` directory to enable integration.
 Yes, it sucks, but currently I have no time for moving issue-tracker-specific code into bundles :)

        api/bin/enable-integration.sh Jira|VersionOne

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
            source /path/to/env-vars.sh

    1. Do the next commands on the target machine as well;

1. [Restore][7] data volume backups if needed;

1. Start up the application:

        docker-compose up -d

1. Import the initial data if you didn't use a backup:
    * For integration with Jira:

            docker-compose exec api bin/console jira:import:projects PROJECT_KEY1 PROJECT_KEY2
            docker-compose exec api bin/console jira:import:issue-field-values Story $JIRA_CUSTOM_FIELD_PI ProgramIncrement PROJECT_KEY1 PROJECT_KEY2
            docker-compose exec api bin/console jira:import:issue-field-values Story components Component PROJECT_KEY1 PROJECT_KEY2
            docker-compose exec api bin/console jira:import:issue-field-values Story $JIRA_CUSTOM_FIELD_TEAM Team PROJECT_KEY1 PROJECT_KEY2
            docker-compose exec api bin/console jira:import:sprints
            docker-compose exec api bin/console jira:import:epics
            docker-compose exec api bin/console jira:import:stories Story 'Issue type name 2' 'Issue type name 3'

    * For integration with VersionOne:

            docker-compose exec api bin/console version-one:import-assets -v

1. Schedule synchronization with appropriate issue-tracker:
    * For integration with Jira:
        1. Add the following jobs to the `crontab` on a host machine:

                0 0 * * * flock -n /tmp/pi-planner-import-epics.lock -c "cd /path/to/docker-compose.yaml/dir && source /path/to/env-vars.sh && /usr/local/bin/docker-compose exec -T api bin/console jira:import:epics &>> /var/log/pi-planner-import-epics.log"
                0 1 * * * flock -n /tmp/pi-planner-import-stories.lock -c "cd /path/to/docker-compose.yaml/dir && source /path/to/env-vars.sh && /usr/local/bin/docker-compose exec -T api bin/console jira:import:stories Story 'Issue type name 2' 'Issue type name 3' &>> /var/log/pi-planner-import-stories.log"

        1. Register a [webhook][9] for getting updates related to epics and workitems from Jira in a real time
         (replace all placeholders, except the `${issue.key}`, with the relevant values):
            * Name: PI Planner
            * URL: https://${API_HOST}/webhooks/jira/${issue.key}
            * Scope: project in (PROJECT_KEY1, PROJECT_KEY2) AND issuetype in (Story, 'Issue type name 2', 'Issue type name 3')
            * Events: Issue Created, Issue Updated, Issue Deleted
            * ExcludeBody: Yes

    * For integration with VersionOne add the following jobs to the `crontab` on a host machine:

            # Import all assets every 15 minutes
            */15 * * * * flock -n /tmp/pi-planner-import.lock -c 'cd /path/to/docker-compose.yaml/dir && source /path/to/env-vars.sh && /usr/local/bin/docker-compose exec api bin/console version-one:import-assets -v'
            # Import workitems every 2 minutes
            */2 * * * * flock -n /tmp/pi-planner-import.lock -c 'cd /path/to/docker-compose.yaml/dir && source /path/to/env-vars.sh && /usr/local/bin/docker-compose exec api bin/console version-one:import-assets PrimaryWorkitem -i'


[1]: https://docs.docker.com/compose/compose-file/compose-file-v3/#extension-fields
[2]: https://docs.docker.com/compose/profiles/
[3]: https://docs.docker.com/engine/install/
[4]: https://docs.docker.com/compose/install/
[5]: https://community.versionone.com/Digital.ai_Agility_Integrations/Developer_Library/Getting_Started/API_Authentication/Access_Token_Authentication
[6]: https://docs.microsoft.com/en-us/azure/active-directory/develop/quickstart-register-app
[7]: ./backuping-and-restoring-data-volumes.md
[8]: https://developer.atlassian.com/cloud/jira/platform/basic-auth-for-rest-apis/#get-an-api-token
[9]: https://developer.atlassian.com/cloud/jira/platform/webhooks/
