<!--
  - SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
# Admin Documentation

## The occ Interface

The Nextcloud [`occ` interface](https://docs.nextcloud.com/server/14/admin_manual/configuration_server/occ_command.html) allows admins to execute various tasks
from the command line. 

### Generate a one-time code

Once this app is enabled, it provides a `twofactorauth:admin:generate-code` command you can execute with

```bash
./occ twofactorauth:admin:generate-code leonida
```

where "leonida" is the user ID for which a one-time login could shall be generated.

The command will tell you the newly generated code as well as its expiry date. You may
now transfer this code to the user via a trusted and secure channel.
