# Backups, restore and updates (Epic 13)

## What is backed up, and where

| What                                                                  | By                                               | Where                                                                                           | Kept                                         |
| --------------------------------------------------------------------- | ------------------------------------------------ | ----------------------------------------------------------------------------------------------- | -------------------------------------------- |
| Database (MySQL)                                                      | STRATO, automatic                                | STRATO panel                                                                                    | per STRATO (check the panel; record it here) |
| Database + uploads + themes + plugins + `wp-config.php` + `.htaccess` | `bin/backup.sh`, started by `bin/backup-pull.sh` | `~/chargenet-backups` on the server (7 newest) **and** a second machine you control (30 newest) | 7 on the server, 30 offsite                  |
| The theme code                                                        | GitHub (`CNThijs/ev-truck-site_wordpress`)       | GitHub                                                                                          | history                                      |
| Content as code                                                       | GitHub (`content/`)                              | GitHub                                                                                          | history                                      |

The server copy alone is not a backup (same machine, same account). The offsite copy is made by **pulling from another machine**, so STRATO holds no credentials for the destination.

## Set up (once)

1. On STRATO over SSH: copy `bin/backup.sh` to a folder **above** the web root (not inside it), `chmod 700` it. Create the passphrase file: `openssl rand -base64 32 > ~/.chargenet-backup.key && chmod 600 ~/.chargenet-backup.key`. **Copy the passphrase into your password manager now.** Without it no backup can be opened.
2. On the machine that keeps the offsite copy (your Mac): copy `bin/backup-pull.sh`, then test: `SSH_HOST=<user@host> WP_ROOT=<path of the site on STRATO> SCRIPT_DIR=<folder with backup.sh> ./backup-pull.sh`. Use SSH keys, not passwords.
3. The offsite machine is **your Mac**. Schedule it with launchd so it runs when the Mac is on (a sleeping Mac skips the run; launchd then runs it at the next wake-up). Save as `~/Library/LaunchAgents/energy.chargenet.backup.plist`, fill in the three values, then `launchctl load ~/Library/LaunchAgents/energy.chargenet.backup.plist`:

   ```xml
   <?xml version="1.0" encoding="UTF-8"?>
   <!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
   <plist version="1.0"><dict>
     <key>Label</key><string>energy.chargenet.backup</string>
     <key>ProgramArguments</key><array><string>/Users/thijs/bin/backup-pull.sh</string></array>
     <key>EnvironmentVariables</key><dict>
       <key>SSH_HOST</key><string>USER@HOST</string>
       <key>WP_ROOT</key><string>/PATH/ON/STRATO</string>
       <key>SCRIPT_DIR</key><string>/PATH/TO/FOLDER/WITH/backup.sh</string>
     </dict>
     <key>StartCalendarInterval</key><dict><key>Hour</key><integer>3</integer><key>Minute</key><integer>30</integer></dict>
     <key>StandardErrorPath</key><string>/Users/thijs/chargenet-backup.log</string>
     <key>StandardOutPath</key><string>/Users/thijs/chargenet-backup.log</string>
   </dict></plist>
   ```

   STRATO needs no cron job. Also run it by hand before every update. Put the offsite folder (`~/chargenet-backups-offsite`) in your normal Mac backup (Time Machine) so the copy itself survives a lost laptop. The files are encrypted, but keep the passphrase separate from the Mac (password manager).

4. Check weekly that the newest file in the offsite folder is from the last day or two. Failures print an error and exit with a non-zero status; wire the cron output to a mailbox you read (`security@chargenet.energy`).

## Restore test (do it once before launch, then every quarter)

Goal: prove that a backup can be opened, is complete, and can rebuild the site. Use a copy, never the live site.

1. `KEY_FILE=<key> bin/backup-verify.sh <backup>.tar.gz.enc /tmp/restore-test` — checks the checksum, decrypts, checks that `database.sql` has the WordPress tables and that `wp-config.php`, uploads and the theme are present.
2. Rebuild locally: `ddev start`, `ddev import-db --file=/tmp/restore-test/database.sql`, copy `/tmp/restore-test/files/wp-content/uploads` into `web/wp-content/uploads`, then `ddev wp search-replace https://chargenet.energy http://chargenet.ddev.site --all-tables`, `ddev wp cache flush`.
3. Open `/en/` and `/nl/`, log in, open a page and the media library, send the contact form. Everything should look like the live site.
4. Record the test below (date, backup file, who, result, time it took).

| Date | Backup file | Done by | Result | Minutes |
| ---- | ----------- | ------- | ------ | ------- |
|      |             |         |        |         |

## Restore on STRATO after a failure

1. Decide what broke (files, database or both). If unsure, restore both from the same backup.
2. Put the site in maintenance: create a file `.maintenance` in the WordPress folder containing `<?php $upgrading = time();`.
3. `bin/backup-verify.sh` the file, unpack it elsewhere. Restore files by copying `wp-content/uploads`, `themes`, `plugins`, `wp-config.php`, `.htaccess` over the site; restore the database with `wp db import database.sql`.
4. `wp cache flush`, clear the page cache (Settings → Cache Enabler, or delete `wp-content/cache`), remove `.maintenance`, run `npm run check:security` and `wp eval-file check-hardening.php production`.
5. If the cause was a compromise: change the salts in `wp-config.php` (logs everyone out), reset all administrator passwords, review users and plugins, and write down what happened for `security@chargenet.energy`.

## Update procedure with rollback

Owner: `thijs@chargenet.energy`. Weekly, plus immediately for a security release.

1. **Before:** run the backup (`backup-pull.sh`) and check the new file exists. Read the plugin's changelog for "breaking" or database changes.
2. **Staging is not available**, so update locally first when it is a major version: `ddev composer update wpackagist-plugin/<slug>`, run `npm run lint`, `npm run test:forms`, `npm run check:seo`, `npm run check:a11y`. Minor and security releases may go straight to the live site after the backup.
3. **Update** one thing at a time (core, then each plugin), open `/en/`, `/nl/`, the contact form and the admin after each.
4. **After:** clear the page cache, run `npm run check:security -- https://chargenet.energy --production`. Note the date and versions in `docs/plugins.md` or your changelog.
5. **Rollback:** a plugin: reinstall the previous version (`wp plugin install <slug> --version=<old> --force`, versions are listed on wordpress.org under "Advanced view"). The theme: upload the previous `chargenet-<version>.zip` from `dist/` or GitHub. Core: `wp core download --version=<old> --force --skip-content`. If the database changed or the site is broken beyond that: restore the backup from step 1 (section above).
