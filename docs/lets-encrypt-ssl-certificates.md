# Let's Encrypt SSL Certificates
The _PI Planner_ uses free Let's Encrypt SSL certificates in the production environment.
If you re-create/replicate the production server to often you might exceed the [renewal quota][1] (5 times per week).
To avoid exceeding the renewal quota [back up and restore][2] the SSL certificates data volume.

[1]: https://letsencrypt.org/docs/rate-limits/
[2]: ./backuping-and-restoring-data-volumes.md
