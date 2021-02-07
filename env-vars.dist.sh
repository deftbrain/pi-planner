# BEFORE CHANGING ORDER OF VARIABLES, MAKE SURE DEPENDANT VARIABLE ARE PLACED BELOW THEIR DEPENDENCIES

### COMMON VARIABLES
# Common host name needed for sharing cookies between services accessible via sub-domains
export COMMON_HOST=piplanning.local


### MICROSOFT OAUTH RELATED VARIABLES
# Format: 1234a1ab-a12a-123a-1a2b-a12bc34d5678
export MICROSOFT_OAUTH_CLIENT_ID=
# Format: 344a1ab-3432-123a-1f2b-a45bc34d54sr
export MICROSOFT_OAUTH_TENANT_ID=
# Format: https://login.microsoftonline.com/company.onmicrosoft.com
export MICROSOFT_OAUTH_TENANT_ENDPOINT=
export MICROSOFT_OAUTH_PUBLIC_KEYS_URL=https://login.microsoftonline.com/common/discovery/keys


### VERSIONONE API RELATED VARIABLES
# Format: 3.123ABcdE456CBFOw2/5RM34bcKw=
export VERSION_ONE_ACCESS_TOKEN=
# Format: https://www9.v1host.com/InstanceName
export VERSION_ONE_SERVER_BASE_URI=


### LETSENCRYPT RELATED VARIABLES
# Let you know if certificates renewal is broken https://letsencrypt.org/docs/expiration-emails/
export LETSENCRYPT_EMAIL=


### DATABASE RELATED VARIABLES
export DB_DRIVER=postgres
export DB_USER=api-platform
export DB_PASSWORD=!ChangeMe!
export DB_NAME=api
export DB_HOST=db
export DB_VERSION=12


### ADMIN RELATED VARIABLES
export ADMIN_IMAGE=pi-planner/admin
# development: api_platform_admin_development
# production: api_platform_admin_nginx
export ADMIN_IMAGE_TARGET=api_platform_admin_development
export ADMIN_HOST=admin.${COMMON_HOST}
export ADMIN_URL=https://${ADMIN_HOST}


### CLIENT RELATED VARIABLES
export CLIENT_IMAGE=pi-planner/client
# development: api_platform_client_development
# production: api_platform_client_nginx
export CLIENT_IMAGE_TARGET=api_platform_client_development
export CLIENT_HOST=${COMMON_HOST}
export CLIENT_URL=https://${CLIENT_HOST}


### API RELATED VARIABLES
export PHP_IMAGE=pi-planner/php
# development: api_platform_php_development
# production: api_platform_php
export PHP_IMAGE_TARGET=api_platform_php_development
export API_SCHEME=https
# To use the cookie authentication method the API must be served from the common host sub-domain
export API_HOST=api.${COMMON_HOST}
# development: dev
# production: prod
export APP_ENV=dev
# development: 1
# production: 0
export APP_DEBUG=1
# https://symfony.com/doc/current/reference/configuration/framework.html#secret
export APP_SECRET=!ChangeMe!
export DATABASE_URL="${DB_DRIVER}://${DB_USER}:${DB_PASSWORD}@${DB_HOST}/${DB_NAME}?serverVersion=${DB_VERSION}"
export MESSENGER_TRANSPORT_DSN=doctrine://default


### MERCURE RELATED VARIABLES
# To use the cookie authentication method the Mercure hub must be served from the common sub-domain
export MERCURE_HOST=mercure.${COMMON_HOST}
export MERCURE_CORS_ALLOWED_ORIGINS="${CLIENT_URL} ${ADMIN_URL}"
export MERCURE_SUBSCRIBE_URL=https://${MERCURE_HOST}/.well-known/mercure
# The 'mercure' host is an internal hostname tha must be equal to the related service name
# used in docker-compose.yml to prevent adding extra options for accessing the related container
export MERCURE_PUBLISH_URL=http://mercure/.well-known/mercure
# A secret for signing JWTs, should be at least be 32 characters long, but the longer the better.
# https://stackoverflow.com/a/62095056/4664724
export MERCURE_JWT_SECRET=
