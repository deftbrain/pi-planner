# Backuping and restoring data volumes
Before creating/restoring backups you have to create the `backups` directory
in the same folder where the `docker-compose.yml` is placed.

    mkdir backups

## Backuping
To back up a volume, run one of the following commands:

    docker-compose run --rm backup-db-data
    docker-compose run --rm backup-certs

These commands create `db-data-{DATE_TIME}.tar.gz` and  `certs-{DATE_TIME}.tar.gz` files respectively
in the `backups` directory.

## Restoring
To restore a volume you need to copy its backup to the `backups` directory (as an option you can simply copy the whole `backups` directory to not create it manually)
on the new server and run one of the following commands from the folder where the `docker-compose.yml` is placed:

    # Use the autocomplete to choose the needed backup file 
    docker-compose run --rm restore-db-data backups/db-data-
    docker-compose run --rm restore-certs backups/certs-
