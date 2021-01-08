# Development

## How-to
### Add a new synced property to an entity
1. Add the new property to the entity:

        docker-compose exec api bin/console make:entity

1. Let a serializer know how to normalize/denormalize the property:

        open api/src/VersionOne/Resources/config/serializer.yml

1. Generate a VersionOne attribute metadata class for the property:

        docker-compose exec api bin/console make:version-one:asset-metadata

1. Make a migration:

        docker-compose exec api bin/console make:migration

1. Apply the migration:
    1. In case of adding a field that can't contain the NULL value in the database, reset the environment entirely
    to apply the migration to a database without any data and run imports automatically:

             docker-compose down -v
             docker-compose up -d

    1. In case of adding a field that can contain the NULL value in the database and re-import needed assets:

            docker-compose exec api bin/console doctrine:migrations:migrate
            docker-compose exec api bin/console version-one:import-assets Theme -i -f
