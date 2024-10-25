# Nextcloud Two-Factor Authentication Admin Support Provider

[![Build status](https://github.com/ChristophWurst/twofactor_admin/actions/workflows/test.yml/badge.svg)](https://github.com/ChristophWurst/twofactor_admin/actions/workflows/test.yml)
[![Read the Docs](https://img.shields.io/readthedocs/nextcloud-twofactor-admin.svg)](https://nextcloud-twofactor-admin.readthedocs.io/en/latest/)

This two-factor auth (2FA) provider for Nextcloud allows admins to generate a one-time
code for users to log into a 2FA protected account. This is helpful in situations where
users have lost access to their other 2FA methods or mandatory 2FA without any previously
enabled 2FA provider.

For more details, see the [admin documentation] and [user documentation].

## Maintainers

* [Altahrim](https://github.com/Altahrim)
* [ChristophWurst](https://github.com/ChristophWurst)
* [Nextcloud Two-Factor Authentication Working Group](https://github.com/nextcloud/wg-two-factor-authentication#members)

[admin documentation]: https://nextcloud-twofactor-admin.readthedocs.io/en/latest/Admin%20Documentation/
[user documentation]: https://nextcloud-twofactor-admin.readthedocs.io/en/latest/User%20Documentation/

## How to release

1) Go to https://github.com/nextcloud/twofactor_admin/actions/workflows/release.yml
2) Click *Run workflow*
   1) Leave *Branch: main*
   2) Click *Run workflow*
3) Go to https://github.com/nextcloud/twofactor_admin/actions
4) Click on the pending *Release* workflow and approve it (only maintainers can do this)
