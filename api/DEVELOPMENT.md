# Development

## How-to
### Add a new synced property to an entity
1. Add the new property to the entity:

        docker-compose exec api bin/console make:entity

1. Make a migration:

        docker-compose exec api bin/console make:migration

1. Apply the migration:

        docker-compose exec api bin/console doctrine:migrations:migrate

1. Let a serializer know how to normalize/denormalize the property:

        open api/src/VersionOne/Resources/config/serializer.yml

1. Generate a VersionOne attribute metadata class for the property:

        docker-compose exec api bin/console make:version-one:asset-metadata
