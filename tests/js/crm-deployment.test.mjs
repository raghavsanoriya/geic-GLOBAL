import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import os from 'node:os';
import path from 'node:path';
import { randomUUID } from 'node:crypto';
import { spawnSync } from 'node:child_process';

const script = await fs.readFile(new URL('../../scripts/deploy-release.sh', import.meta.url), 'utf8');

async function deployment(failure = '', driver = 'sqlite', retentionFixtures = false) {
    const root = await fs.mkdtemp(path.join(os.tmpdir(), 'geic-deployment-test-'));
    const owner = randomUUID();
    const account = path.join(root, 'account');
    const repository = path.join(root, 'repository');
    const bin = path.join(root, 'bin');
    const production = path.join(account, 'apps/geic-production');
    const shared = path.join(production, 'shared');
    const previous = path.join(production, 'releases/previous');
    const sha = 'a'.repeat(40);
    const original = `APP_KEY=preserved-fixture-key\nDB_CONNECTION=${driver}\n`;
    await fs.writeFile(path.join(root, 'owned-test-fixture'), owner);
    try {
        for (const directory of [bin, path.join(repository, 'scripts'), path.join(repository, 'database'), path.join(repository, 'public'), path.join(shared, 'database'), path.join(shared, 'storage/framework'), previous]) {
            await fs.mkdir(directory, { recursive: true });
        }
        await fs.writeFile(path.join(shared, '.env'), original);
        await fs.writeFile(path.join(shared, 'database/database.sqlite'), 'original-fixture-records');
        await fs.writeFile(path.join(shared, 'crm-mysql.json'), '{}');
        await fs.writeFile(path.join(previous, 'artisan'), 'previous-release');
        await fs.symlink(previous, path.join(production, 'current'));
        await fs.writeFile(path.join(repository, 'artisan'), 'fixture-artisan');
        await fs.writeFile(path.join(repository, 'composer.json'), '{}');
        await fs.writeFile(path.join(repository, 'composer.lock'), '{}');
        await fs.writeFile(path.join(bin, 'composer'), 'fixture-composer');
        if (retentionFixtures) {
            await fs.mkdir(path.join(shared, 'vendor-cache'), { recursive: true });
            for (let index = 0; index < 6; index += 1) {
                const directory = path.join(production, 'releases', `retained-${index}`);
                const timestamp = new Date(Date.UTC(2020, 0, 1, 0, 0, index));
                await fs.mkdir(directory);
                await fs.utimes(directory, timestamp, timestamp);
                if (index < 4) {
                    const archive = path.join(shared, 'vendor-cache', `retained-${index}.tar`);
                    await fs.writeFile(archive, 'owned-cache-fixture');
                    await fs.utimes(archive, timestamp, timestamp);
                }
            }
        }
        const executables = {
            git: '#!/usr/bin/env bash\ncase "$*" in *--show-toplevel*) echo "$GEIC_TEST_REPOSITORY";; *--abbrev-ref*) echo main;; *) printf a%.0s {1..40}; echo;; esac\n',
            sleep: '#!/usr/bin/env bash\nprintf "drain\\n" >> "$GEIC_TEST_EVENTS"\n',
            curl: '#!/usr/bin/env bash\nprintf "health\\n" >> "$GEIC_TEST_EVENTS"\nprintf "<%s>" "$@" >> "$GEIC_TEST_HEALTH_ARGUMENTS"\nprintf "\\n" >> "$GEIC_TEST_HEALTH_ARGUMENTS"\n[[ "$GEIC_TEST_FAILURE" != health ]]\n',
            php: `#!/usr/bin/env bash
set -euo pipefail
if [[ "$1" == -r ]]; then
    case "$2" in
        *getDriverName*) echo "$GEIC_TEST_DRIVER";;
        *'config("crm.enabled")'*) echo 1;;
        *'config("app.url")'*) echo https://geic.test;;
        *random_bytes*) echo fixture-maintenance-token;;
    esac
    exit 0
fi
if [[ "$1" == */composer ]]; then mkdir -p vendor; touch vendor/autoload.php; exit 0; fi
shift
command="$1"
printf '%s\\n' "$command" >> "$GEIC_TEST_EVENTS"
case "$command" in
    down) touch "$GEIC_TEST_SHARED/storage/framework/down";;
    up) rm -f "$GEIC_TEST_SHARED/storage/framework/down";;
    crm:backup-database)
        [[ "$GEIC_TEST_FAILURE" != backup ]] || exit 21
        [[ "$GEIC_TEST_FAILURE" != final-backup || "$*" == *-preflight* ]] || exit 21
        for argument in "$@"; do
            if [[ "$argument" == --label=* ]]; then
                destination="$GEIC_TEST_SHARED/storage/crm-backups/\u0024{argument#--label=}"
                mkdir -p "$destination"
                cp "$GEIC_TEST_SHARED/.env" "$destination/.env"
            fi
        done;;
    crm:prepare-mysql)
        if [[ "$*" == *--check-copy* ]]; then
            printf 'check-copy\\n' >> "$GEIC_TEST_EVENTS"
            [[ "$GEIC_TEST_FAILURE" != check ]] || exit 25
        elif [[ "$*" == *--activate* ]]; then
            printf 'APP_KEY=preserved-fixture-key\\nDB_CONNECTION=mysql\\n' > "$GEIC_TEST_SHARED/.env"
            [[ "$GEIC_TEST_FAILURE" != activate ]] || exit 22
        else
            mkdir -p "$GEIC_TEST_SHARED/storage/crm-mysql"
            [[ "$GEIC_TEST_FAILURE" != prepare ]] || exit 23
        fi;;
    migrate) [[ "$GEIC_TEST_FAILURE" != migrate ]] || exit 24;;
esac
`,
        };
        for (const [name, contents] of Object.entries(executables)) {
            await fs.writeFile(path.join(bin, name), contents, { mode: 0o755 });
        }
        await fs.writeFile(path.join(repository, 'scripts/install-production-bridge.sh'), '#!/usr/bin/env bash\nexit 0\n', { mode: 0o755 });
        const fixtureScript = script.replaceAll('/home2/geicic3c', account)
            .replace('/opt/cpanel/ea-php83/root/usr/bin/php', path.join(bin, 'php'))
            .replace('/opt/cpanel/composer/bin/composer', path.join(bin, 'composer'));
        const location = path.join(repository, 'scripts/deploy-release.sh');
        await fs.writeFile(location, fixtureScript);
        const result = spawnSync('bash', [location], { cwd: repository, encoding: 'utf8', timeout: 20000, env: {
            ...process.env, PATH: `${bin}:${process.env.PATH}`, GEIC_TEST_REPOSITORY: repository,
            GEIC_TEST_SHARED: shared, GEIC_TEST_EVENTS: path.join(root, 'events'), GEIC_TEST_FAILURE: failure, GEIC_TEST_DRIVER: driver,
            GEIC_TEST_HEALTH_ARGUMENTS: path.join(root, 'health-arguments'),
        } });
        assert.equal(result.error, undefined);
        const events = (await fs.readFile(path.join(root, 'events'), 'utf8')).trim().split('\n');
        const environment = await fs.readFile(path.join(shared, '.env'), 'utf8');
        const active = await fs.realpath(path.join(production, 'current'));
        assert.equal(await fs.readFile(path.join(shared, 'database/database.sqlite'), 'utf8'), 'original-fixture-records');
        assert.equal(await fs.stat(path.join(shared, 'storage/framework/down')).then(() => true, () => false), false, result.stderr);
        if (failure) {
            assert.notEqual(result.status, 0, result.stdout);
            assert.equal(environment, original);
            assert.equal(active, previous);
            if (driver === 'sqlite' && !['prepare', 'backup', 'check'].includes(failure)) assert.equal(events.at(-1), 'up');
            else assert.equal(events.includes('down'), false);
            if (driver === 'mysql' && failure === 'health') {
                assert.equal(result.status, 70, result.stderr);
                assert.equal(events.includes('up'), false);
            }
        } else {
            assert.equal(result.status, 0, result.stderr);
            assert.ok(result.stdout.includes(`Activated release ${sha}`), result.stdout);
            assert.equal(active, path.join(production, 'releases', sha));
            assert.equal(await fs.readFile(path.join(active, 'public/release.txt'), 'utf8'), sha);
            const healthRequests = (await fs.readFile(path.join(root, 'health-arguments'), 'utf8')).trim().split('\n');
            assert.equal(healthRequests.length, driver === 'sqlite' ? 8 : 7);
            for (const request of healthRequests) {
                assert.equal(request.includes('<>'), false, request);
                assert.equal(request.includes('<--cookie>'), driver === 'sqlite', request);
            }
            if (driver === 'sqlite') {
                assert.match(environment, /DB_CONNECTION=mysql/);
                assert.ok(events.indexOf('check-copy') < events.indexOf('down'));
                assert.ok(events.indexOf('drain') < events.lastIndexOf('crm:backup-database'));
                assert.ok(events.lastIndexOf('crm:prepare-mysql') < events.indexOf('migrate'));
                assert.ok(events.lastIndexOf('health') < events.indexOf('up'));
            } else {
                assert.equal(environment, original);
                assert.equal(events.includes('down'), false);
                assert.equal(events.includes('crm:prepare-mysql'), false);
            }
            if (retentionFixtures) {
                assert.deepEqual((await fs.readdir(path.join(production, 'releases'))).sort(), [sha, 'previous', 'retained-3', 'retained-4', 'retained-5'].sort());
                const archives = (await fs.readdir(path.join(shared, 'vendor-cache'))).sort();
                assert.equal(archives.length, 3);
                assert.ok(archives.includes('retained-2.tar'));
                assert.ok(archives.includes('retained-3.tar'));
            }
        }
    } finally {
        assert.equal(await fs.readFile(path.join(root, 'owned-test-fixture'), 'utf8'), owner);
        assert.equal(path.dirname(await fs.realpath(root)), await fs.realpath(os.tmpdir()));
        assert.match(path.basename(root), /^geic-deployment-test-/);
        await fs.rm(root, { recursive: true });
    }
}

test('cPanel activates the verified copy only after private health checks and preserves original SQLite data', { skip: process.platform === 'win32' }, () => deployment());
test('ordinary MySQL releases do not repeat the SQLite cutover', { skip: process.platform === 'win32' }, () => deployment('', 'mysql'));
test('successful MySQL releases retain the latest five releases and three dependency caches', { skip: process.platform === 'win32' }, () => deployment('', 'mysql', true));
test('a failed MySQL health check restores the previous release without a maintenance window', { skip: process.platform === 'win32' }, () => deployment('health', 'mysql'));
for (const phase of ['prepare', 'backup', 'check', 'final-backup', 'activate', 'migrate', 'health']) {
    test(`cPanel failure during ${phase} restores configuration, release and site availability`, { skip: process.platform === 'win32' }, () => deployment(phase));
}
