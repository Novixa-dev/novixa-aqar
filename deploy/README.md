# Deployment — Novixa Aqar → Dexal (cPanel, no root)

Pushing to **`demo`** deploys. `main` is the source of truth; `demo` is
fast-forwarded from it and never committed to directly.

```
main ──(ff)──▶ demo ──push──▶ GitHub Actions ──ssh──▶ Dexal
```

## What runs

`.github/workflows/deploy-demo.yml`

| Job | Does | Gates deploy |
|---|---|---|
| `test` | `composer install` (with dev) → `php artisan test` | yes |
| `build` | production `composer install --no-dev`, vendor slimming, `npm ci && npm run build`, package `.tar.gz` | yes |
| `deploy` | upload artifact over SSH, run `deploy/release.sh`, verify five live URLs | — |

`workflow_dispatch` exposes a `skip_tests` toggle for an emergency
deploy. Use it knowingly; it is the only path that ships untested code.

## The release model on the server

```
~/app/releases/<timestamp>/    one immutable copy per release
~/app/shared/.env              survives every release
~/app/shared/storage/          survives every release (uploads, logs, sessions)
~/app/current  ->  releases/<timestamp>
~/public_html/<file>  ->  ../app/current/public/<file>
```

The web root points at `current`, never at a specific release, so the
cutover is one `rename(2)` — atomic. No request can observe a
half-written release.

Order inside `release.sh` is deliberate:

1. unpack, sanity-check the artifact (`artisan`, `vendor/`, `public/build`)
2. wire `shared/.env` + `shared/storage` symlinks
3. **migrate** — before the switch, so a failed migration leaves the live
   release untouched and the new directory is discarded
4. **warm caches** (`config` / `route` / `view` / `event`) — before the
   switch, so the release is hot the instant it goes live
5. **atomic switch** of `current`
6. re-sync `public_html` symlinks (picks up files added to / removed from
   `public/`; never touches `.htaccess` or `.well-known`)
7. **health check** ×5 with backoff → automatic rollback to the previous
   release if it never returns 200
8. prune to the last 3 releases

A `flock` guard means two deploys cannot race for `current`.

### What rollback does and does not cover

Code rolls back automatically. **Schema does not.** `migrate:rollback` can
destroy data, and automatically reversing a partially applied migration is
more dangerous than a human reading the failure. If a deploy rolls back,
the script says so explicitly and keeps the failed release directory for
inspection.

## One-time setup

### 1. Repository secrets

`Settings → Secrets and variables → Actions → New repository secret`

| Secret | Value |
|---|---|
| `DEXAL_HOST` | `aqar-demo.dexal.net` |
| `DEXAL_USER` | `akaqar` |
| `DEXAL_SSH_KEY` | contents of the private key file delivered separately |
| `DEXAL_KNOWN_HOSTS` | the host-key line below |

`DEXAL_KNOWN_HOSTS` (pins the server so CI cannot be MITM'd — the
workflow uses `StrictHostKeyChecking=yes`):

```
aqar-demo.dexal.net ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIC1r4yqzjurRXsfQ76m+9tkK1tO6ZpOC5srBxL58Oj74
```

Until all four exist the deploy job prints a notice and ends green — it
does not fail the run.

### 2. The deploy key

A dedicated ed25519 keypair was generated for CI and its **public** half
is already installed in `~/.ssh/authorized_keys` on the server:

```
ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIFOSQK/6QwBXHdRtd1lhTwtqhQ6qIjPk9vF5W4E+7aWG github-actions-deploy@novixa-aqar
```

The private half is delivered to you as a file. Paste it into
`DEXAL_SSH_KEY` (whole file, including the `BEGIN`/`END` lines), then
delete the local copy. It is separate from your interactive key, so you
can revoke CI access alone by removing that one line from
`~/.ssh/authorized_keys`.

If you would rather CI never used a key you did not create yourself,
generate your own and replace the line:

```bash
ssh-keygen -t ed25519 -f dexal_ci -N "" -C "github-actions-deploy@novixa-aqar"
# then, on the server, swap the github-actions-deploy line in
# ~/.ssh/authorized_keys for the new dexal_ci.pub
```

### 3. `shared/.env`

Already in place at `~/app/shared/.env`. The pipeline never writes it —
environment changes are a deliberate manual step, so a deploy can never
silently alter configuration or leak a secret into the repo.

## Operating it

```bash
# normal release
git checkout main && git pull
git checkout demo && git merge --ff-only main && git push origin demo

# manual re-release of what is already on demo
gh workflow run "Deploy demo to Dexal" --ref demo

# emergency deploy without tests
gh workflow run "Deploy demo to Dexal" --ref demo -f skip_tests=true
```

### Manual rollback

```bash
ssh akaqar@aqar-demo.dexal.net
ls -1 ~/app/releases            # pick the release you want
ln -s releases/<timestamp> ~/app/.rb && mv -T ~/app/.rb ~/app/current
```

Then re-sync the web root if `public/` differed between the two releases:

```bash
for f in ~/app/current/public/*; do
  [ "$(basename "$f")" = ".htaccess" ] || \
    ln -sfn "../app/current/public/$(basename "$f")" ~/public_html/
done
```

## Constraints this host imposes

| Constraint | Consequence |
|---|---|
| No Composer or Node on the server | the artifact must arrive fully built — CI does the building |
| No Redis | `CACHE_DRIVER=file`, `SESSION_DRIVER=cookie`, `QUEUE_CONNECTION=sync` |
| No persistent process manager | no Octane, Horizon or Reverb; broadcasting stays on the `log` driver |
| 5 GB account quota | vendor slimming is not optional; 3 retained releases ≈ 1 GB |
| cPanel owns `public_html` | it stays a real directory of symlinks; `.well-known` and the PHP-handler `.htaccess` are never touched |
