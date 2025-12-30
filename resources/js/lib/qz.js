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

    qz.security.setCertificatePromise(() => (
        axios.get('/qz/certificate', { responseType: 'text' }).then((response) => response.data)
    ));

    qz.security.setSignaturePromise((toSign) => (
        axios.post('/qz/sign', { data: toSign }).then((response) => response.data.signature)
    ));

    securityConfigured = true;
};

const ensureConnection = async () => {
    configureSecurity();

    if (qz.websocket.isActive()) {
        return;
    }

    if (!connectPromise) {
        connectPromise = qz.websocket.connect();
    }

    try {
        await connectPromise;
    } catch (error) {
        connectPromise = null;
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
