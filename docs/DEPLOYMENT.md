# GitHub to cPanel deployment

This repository uses two protected GitHub environments and atomic Laravel releases.

## Release flow

1. Develop changes on a feature branch.
2. Merge reviewed changes into `develop`.
3. GitHub Actions runs formatting, migrations, tests, and the frontend build.
4. A successful `develop` build deploys automatically to staging.
5. After staging approval, merge `develop` into `main`.
6. In GitHub Actions, run the **Deploy** workflow from `main`, choose `production`, and confirm the run.

Production never deploys from a push. It only deploys through the manual workflow.

## GitHub environment configuration

Both `staging` and `production` require these secrets:

- `CPANEL_HOST`
- `CPANEL_USER`
- `CPANEL_API_TOKEN`

Both environments require these variables:

- `APP_URL`: environment base URL without a trailing slash
- `CPANEL_REPOSITORY_ROOT`: absolute path to the cPanel-managed Git repository

The `staging` environment accepts only the `develop` branch. The `production` environment accepts only `main`.

## cPanel directory layout

Each environment has an independent cPanel-managed repository and deployment root:

```text
<deploy-path>/
├── current -> releases/<git-sha>
├── incoming/
├── releases/
└── shared/
    ├── .env
    └── storage/
```

The staging document root points to `/home2/geicic3c/public_html/staging/current/public`. Production releases are prepared under `/home2/geicic3c/apps/geic-production`; the main-domain cutover remains a separate, explicitly approved step.

The remote `.env`, database, uploaded files, logs, and sessions are never committed or replaced by a deployment. Five releases are retained for diagnosis and rollback.

## First deployment checklist

- cPanel has a dedicated, revocable API token stored only in the GitHub environments.
- cPanel Git Version Control contains independent `develop` and `main` clones.
- `shared/.env` exists separately for staging and production.
- Staging uses a separate database and `APP_ENV=staging`, `APP_DEBUG=false`.
- Production uses `APP_ENV=production`, `APP_DEBUG=false`.
- The two document roots point to their own `current/public` paths.
- HTTPS is enabled for both environment URLs.
- The `/up` health endpoint returns HTTP 200.
