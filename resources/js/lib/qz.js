import axios from 'axios';
import qz from 'qz-tray';

let securityConfigured = false;
let connectPromise = null;

const defaultPrinterName = import.meta.env.VITE_QZ_PRINTER_NAME || null;
const defaultPaperWidthMm = Number(import.meta.env.VITE_QZ_PAPER_WIDTH_MM || 80);

const configureSecurity = () => {
    if (securityConfigured) {
        return;
    }

    qz.security.setCertificatePromise((resolve, reject) => {
        console.log('[QZ] Requesting certificate...');
        axios.get('/qz/certificate', { responseType: 'text' })
            .then((response) => {
                console.log('[QZ] Certificate received');
                resolve(response.data);
            })
            .catch((err) => {
                console.error('[QZ] Certificate request failed:', err);
                reject(err);
            });
    });

    qz.security.setSignaturePromise((toSign) => {
        console.log('[QZ LOG v4] Requesting signature...', { toSign });
        return axios.post('/qz/sign', { data: toSign })
            .then((response) => {
                console.log('[QZ LOG v4] Signature received');
                return response.data.signature;
            })
            .catch((err) => {
                console.error('[QZ LOG v4] Signature request failed:', err);
                throw err;
            });
    });

    securityConfigured = true;
    console.log('[QZ LOG v4] Security configured');
};

const ensureConnection = async () => {
    configureSecurity();
    console.log('[QZ LOG v4] Checking connection status...');

    if (qz.websocket.isActive()) {
        console.log('[QZ] Websocket already active');
        return;
    }

    if (connectPromise) {
        console.log('[QZ] Waiting for existing connection attempt...');
        await connectPromise;
        return;
    }

    console.log('[QZ] connecting to websocket...');
    connectPromise = qz.websocket.connect({
        retries: 0, // Disable retries to fail fast for debugging
        keepAlive: 60
    });

    const timeoutPromise = new Promise((_, reject) =>
        setTimeout(() => reject(new Error('Connection timed out after 5s')), 5000)
    );

    try {
        await Promise.race([connectPromise, timeoutPromise]);
        console.log('[QZ] Websocket connected successfully');
    } catch (error) {
        console.error('[QZ] Websocket connection failed or timed out:', error);
        connectPromise = null;
        if (qz.websocket.isActive()) {
            console.log('[QZ] Disconnecting due to error...');
            try { await qz.websocket.disconnect(); } catch (e) {}
        }
        throw error;
    }
};

export const printReceiptHtml = async (html, options = {}) => {
    const printerName = options.printerName || defaultPrinterName;
    const widthMm = Number(options.widthMm || defaultPaperWidthMm);

    console.log('[QZ] Starting print job...', { printerName, widthMm });

    try {
        await ensureConnection();
        console.log('[QZ] Connection ensured');
    } catch (err) {
        console.error('[QZ] Connection failed:', err);
        throw new Error(`QZ Tray connection failed: ${err.message || err}`);
    }

    let printer;
    try {
        if (printerName) {
            console.log(`[QZ] Searching for printer: "${printerName}"`);
            printer = await qz.printers.find(printerName);
        } else {
            console.log('[QZ] Searching for default printer');
            printer = await qz.printers.find();
        }
        console.log(`[QZ] Found printer: "${printer}"`);
    } catch (err) {
        console.error('[QZ] Printer not found:', err);
        throw new Error(`Printer not found: ${err.message || err}`);
    }

    const configOptions = {
        units: 'mm',
        margins: { top: 0, right: 0, bottom: 0, left: 0 },
        rasterize: true,
        scaleContent: false,
    };

    if (Number.isFinite(widthMm) && widthMm > 0) {
        configOptions.size = { width: widthMm };
    }

    try {
        const config = qz.configs.create(printer, configOptions);
        const data = [{ type: 'html', format: 'plain', data: html }];

        console.log('[QZ] Sending data to printer...');
        await qz.print(config, data);
        console.log('[QZ] Print submitted successfully');
    } catch (err) {
        console.error('[QZ] Print execution failed:', err);
        throw new Error(`Print failed: ${err.message || err}`);
    }
};
