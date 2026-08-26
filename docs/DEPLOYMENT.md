# GitHub to cPanel deployment

This repository uses GitHub Actions, two protected environments, and atomic Laravel releases.

## Release flow

1. Develop changes on a feature branch and merge them into `develop`.
2. GitHub Actions validates Composer, builds assets, checks formatting, and runs the test suite.
3. A successful `develop` push deploys the exact commit automatically to `https://staging.geic.in`.
4. Review and test staging, then open a pull request from `develop` to `main`.
5. After the required checks pass and the pull request is merged, the `main` commit deploys automatically to `https://www.geic.in`.
6. The **Deploy** workflow can also be dispatched manually for the branch that belongs to the selected environment.

## GitHub configuration

Create `staging` and `production` environments. Restrict staging to `develop` and production to `main`.

Store these as repository Actions secrets:

- `CPANEL_HOST`
- `CPANEL_USER`
- `CPANEL_API_TOKEN`

Define these variables in both environments:

- `APP_URL`: environment URL without a trailing slash
- `CPANEL_REPOSITORY_ROOT`: absolute cPanel-managed repository path

Protect `main`: require a pull request and the `Test and build` status check, and block force pushes and branch deletion.

## cPanel configuration

Use a repository-scoped read-only SSH deploy key for GitHub. Configure two independent cPanel Git Version Control clones:

- `develop` at `/home2/geicic3c/repositories/geic-staging`
- `main` at `/home2/geicic3c/repositories/geic-production`

The deploy script creates this layout for each environment:

```text
<deploy-path>/
|-- current -> releases/<git-sha>
|-- releases/
`-- shared/
    |-- .env
    `-- storage/
```

Staging deploys under `/home2/geicic3c/public_html/staging`. Production deploys under `/home2/geicic3c/apps/geic-production`. Five releases are retained.

Before the first production cutover, run `scripts/install-production-bridge.sh` from the production repository. It creates a timestamped backup and connects `public_html` to the active release. Do not run it until a production release exists and passes its local Laravel setup.

## Environment requirements

- Staging uses its own `.env`, application key, SQLite database, and `APP_URL=https://staging.geic.in`.
- Production preserves its existing application key and database configuration with `APP_URL=https://www.geic.in`.
- Both environments use `APP_DEBUG=false` and PHP 8.3.
- Shared storage, databases, uploaded files, logs, and sessions are never committed or replaced.

## Deployment safety

The deployment is idempotent for an already-active commit. It prepares a release before switching the `current` symlink, validates public routes and CSS after activation, and restores the previous symlink if a health check fails. GitHub independently verifies the deployed SHA through `/release.txt` and checks the primary routes and stylesheet.
