/**
 * Free Self-Hosted WhatsApp Web Gateway for PatSons Foods Goa
 * Built for Dev Can Fruit Products, Morjim, Pernem - Goa
 * 
 * Works with Laravel WhatsAppService on port 21465
 */

require('dotenv').config();
const express = require('express');
const cors = require('cors');
const wppconnect = require('@wppconnect-team/wppconnect');

const app = express();
app.use(cors());
app.use(express.json());

const PORT = process.env.PORT || 21465;
const SECRET_TOKEN = process.env.WHATSAPP_SECRET_TOKEN || 'THISISMYSECURETOKEN';
const SESSION_NAME = 'patsons-session';

let globalClient = null;
let latestQrCode = null;
let sessionState = 'DISCONNECTED';
let isInitializing = false;

// Token verification middleware
function requireAuth(req, res, next) {
    const authHeader = req.headers['authorization'];
    if (!authHeader) {
        return res.status(401).json({ status: 'error', message: 'Missing Authorization header' });
    }
    const token = authHeader.replace(/^Bearer\s+/i, '').trim();
    const validTokens = [
        SECRET_TOKEN,
        '$2b$10$Vvv_QADgQjgit5ICP1Joa.nrlxYgjpKDESMOtRDQCSaI.wEs0zHgm',
        'THISISMYSECURETOKEN'
    ].filter(Boolean);

    if (!validTokens.includes(token)) {
        return res.status(403).json({ status: 'error', message: 'Invalid secret token' });
    }
    next();
}

// Initialize WPPConnect Session
async function initSession() {
    if (globalClient || isInitializing) return;
    isInitializing = true;
    sessionState = 'INITIALIZING';

    // Auto-clean any lingering singleton lock files from previous unclean shutdowns
    const fs = require('fs');
    const path = require('path');
    const lockDir = path.resolve(__dirname, 'tokens/patsons-session');
    ['SingletonLock', 'SingletonCookie', 'SingletonSocket'].forEach(f => {
        try { fs.unlinkSync(path.join(lockDir, f)); } catch(e) {}
    });

    try {
        globalClient = await wppconnect.create({
            session: SESSION_NAME,
            autoClose: 0,
            deviceSyncTimeout: 0,
            catchQR: (base64Qr, asciiQR, attempts, urlCode) => {
                if (base64Qr) {
                    latestQrCode = base64Qr.startsWith('data:image') ? base64Qr : `data:image/png;base64,${base64Qr}`;
                    sessionState = 'QR_READY';
                    console.log(`[WhatsApp Gateway] New QR code generated. Attempt #${attempts}`);
                }
            },
            statusFind: (statusSession, session) => {
                sessionState = statusSession;
                console.log(`[WhatsApp Gateway] Status update: ${statusSession} (${session})`);
                if (['isLogged', 'inChat', 'chatsAvailable', 'qrReadSuccess', 'CONNECTED'].includes(statusSession)) {
                    latestQrCode = null;
                }
            },
            headless: 'new',
            devtools: false,
            useChrome: true,
            debug: false,
            logQR: false,
            browserArgs: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-accelerated-2d-canvas',
                '--no-first-run',
                '--no-zygote',
                '--disable-gpu'
            ]
        });

        sessionState = 'CONNECTED';
        latestQrCode = null;
        console.log('[WhatsApp Gateway] Successfully connected to WhatsApp Web!');
    } catch (err) {
        console.error('[WhatsApp Gateway] Initialization error:', err.message);
        sessionState = 'ERROR';
        globalClient = null;
    } finally {
        isInitializing = false;
    }
}

// 1. Health & status check
app.get('/health', (req, res) => {
    const isConnected = ['CONNECTED', 'isLogged', 'inChat', 'chatsAvailable', 'qrReadSuccess'].includes(sessionState);
    res.json({
        status: 'ok',
        gateway: 'PatSons Foods WhatsApp Gateway',
        port: PORT,
        session: SESSION_NAME,
        state: isConnected ? 'CONNECTED' : sessionState,
        connected: isConnected
    });
});

// 2. Start Session / Get QR Code
app.post(`/api/${SESSION_NAME}/start-session`, requireAuth, async (req, res) => {
    if (!globalClient && !isInitializing) {
        initSession();
    }

    // If client requested waiting for QR code, hold response for up to 8 seconds
    const shouldWait = req.body && (req.body.waitQrCode === true || req.body.waitQrCode === 'true');
    const isConn = () => ['CONNECTED', 'isLogged', 'inChat', 'chatsAvailable', 'qrReadSuccess'].includes(sessionState);

    if (shouldWait && !latestQrCode && !isConn()) {
        for (let i = 0; i < 16; i++) {
            await new Promise(resolve => setTimeout(resolve, 500));
            if (latestQrCode || isConn()) {
                break;
            }
        }
    }

    const connected = isConn();

    res.json({
        status: 'success',
        session: SESSION_NAME,
        connected: connected,
        state: connected ? 'CONNECTED' : sessionState,
        qrcode: connected ? null : latestQrCode
    });
});

// 2.5 Session Status Endpoint
app.all(`/api/${SESSION_NAME}/status-session`, requireAuth, (req, res) => {
    const isConn = ['CONNECTED', 'isLogged', 'inChat', 'chatsAvailable', 'qrReadSuccess'].includes(sessionState);
    res.json({
        status: isConn ? 'CONNECTED' : sessionState,
        connected: isConn,
        message: isConn ? 'WhatsApp gateway is active and connected' : `Gateway state is ${sessionState}`
    });
});

// 3. Send WhatsApp Message
app.post(`/api/${SESSION_NAME}/send-message`, requireAuth, async (req, res) => {
    const { phone, message } = req.body;

    if (!phone || !message) {
        return res.status(400).json({ status: 'error', message: 'phone and message are required fields' });
    }

    if (!globalClient) {
        return res.status(503).json({
            status: 'error',
            message: 'WhatsApp gateway is not connected. Please scan the QR code in PatSons Admin settings.'
        });
    }

    try {
        let destination = phone.trim();
        if (!destination.includes('@')) {
            let digits = destination.replace(/[^0-9]/g, '');
            if (digits.length === 10) digits = '91' + digits;
            destination = `${digits}@c.us`;
        }

        const result = await globalClient.sendText(destination, message);
        console.log(`[WhatsApp Gateway] Message dispatched to ${destination}`);

        res.json({
            status: 'success',
            message: 'Message dispatched successfully',
            result: result
        });
    } catch (err) {
        console.error('[WhatsApp Gateway] Send error:', err.message);
        res.status(500).json({ status: 'error', message: err.message });
    }
});

// Start Server
app.listen(PORT, () => {
    console.log(`====================================================`);
    console.log(` PatSons Foods Goa - WhatsApp Web Gateway`);
    console.log(` Dev Can Fruit Products, Morjim, Pernem - Goa`);
    console.log(` Listening on: http://127.0.0.1:${PORT}`);
    console.log(` Auth Token: ${SECRET_TOKEN}`);
    console.log(`====================================================`);
    
    // Auto-initialize on boot
    initSession();
});
