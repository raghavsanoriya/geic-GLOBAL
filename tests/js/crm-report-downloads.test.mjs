import test from 'node:test';
import assert from 'node:assert/strict';
import { downloadChartPng } from '../../public/assets/transglobe/js/crm-report-downloads.js';

function fixture(fail = false) {
    const revoked = [];
    const clicked = [];
    const canvas = { width: 0, height: 0, getContext: () => ({ scale() {}, drawImage() {} }), toBlob: callback => callback(fail ? null : new Blob(['png'])) };
    const environment = {
        Blob,
        XMLSerializer: class { serializeToString() { return '<svg/>'; } },
        Image: class {
            width = 920; height = 150;
            set src(value) { queueMicrotask(() => this.onload()); }
        },
        URL: { createObjectURL: () => 'blob:' + (revoked.length ? 'output' : 'source'), revokeObjectURL: value => revoked.push(value) },
        document: {
            body: { appendChild() {} },
            createElement: tag => tag === 'canvas' ? canvas : { click() { clicked.push(this.download); }, remove() {} },
        },
    };
    return { environment, revoked, clicked, canvas };
}

test('PNG rendering downloads a doubled-resolution chart and revokes temporary blob URLs', async () => {
    const data = fixture();
    await downloadChartPng({}, 'years', data.environment);
    assert.deepEqual(data.clicked, ['geic-report-years.png']);
    assert.equal(data.canvas.width, 1840);
    assert.equal(data.canvas.height, 300);
    assert.equal(data.revoked.length, 2);
});

test('failed PNG encoding revokes the source and does not start a download', async () => {
    const data = fixture(true);
    await assert.rejects(downloadChartPng({}, 'years', data.environment), /PNG unavailable/);
    assert.equal(data.revoked.length, 1);
    assert.equal(data.clicked.length, 0);
});
