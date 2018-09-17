# Using the occ interface

The Nextcloud [`occ` interface](https://docs.nextcloud.com/server/14/admin_manual/configuration_server/occ_command.html) allows admins to execute various tasks
from the command line. Once this app is enabled, it provides a `twofactorauth:admin:generate-code` command you can execute with

```bash
./occ twofactorauth:admin:generate-code leonida
```

where "leonida" is the user ID for which a one-time login could shall be generated.