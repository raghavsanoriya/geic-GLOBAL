export async function downloadChartPng(svg, name, environment = globalThis) {
    const { document, XMLSerializer, Blob, URL, Image } = environment;
    const serialized = new XMLSerializer().serializeToString(svg);
    const source = URL.createObjectURL(new Blob([serialized], { type: 'image/svg+xml' }));
    try {
        const bitmap = await new Promise((resolve, reject) => {
            const image = new Image();
            image.onload = () => resolve(image);
            image.onerror = () => reject(new Error('SVG rendering unavailable'));
            image.src = source;
        });
        const canvas = document.createElement('canvas');
        canvas.width = bitmap.width * 2;
        canvas.height = bitmap.height * 2;
        const context = canvas.getContext('2d');
        if (!context) throw new Error('Canvas unavailable');
        context.scale(2, 2);
        context.drawImage(bitmap, 0, 0);
        const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
        if (!blob) throw new Error('PNG unavailable');
        const url = URL.createObjectURL(blob);
        try {
            const link = document.createElement('a');
            link.href = url;
            link.download = 'geic-report-' + name + '.png';
            document.body.appendChild(link);
            link.click();
            link.remove();
        } finally {
            URL.revokeObjectURL(url);
        }
    } finally {
        URL.revokeObjectURL(source);
    }
}

if (typeof document !== 'undefined') {
    const status = document.querySelector('[data-chart-download-status]');
    document.querySelectorAll('[data-report-png]').forEach(button => {
        button.addEventListener('click', async () => {
            const name = button.dataset.reportPng;
            const chart = document.querySelector('[data-report-chart="' + name + '"]');
            if (!chart || button.disabled) return;
            button.disabled = true;
            try {
                await downloadChartPng(chart, name, {
                    document, XMLSerializer, Blob, URL, Image,
                });
                status.textContent = 'PNG chart downloaded.';
            } catch {
                status.textContent = 'PNG export is unavailable in this browser. Use the SVG or CSV download instead.';
            } finally {
                button.disabled = false;
            }
        });
    });
}
