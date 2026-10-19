# 🌴 PatSons Foods Goa — Free WhatsApp Gateway

**100% Free, Zero-Monthly-Cost WhatsApp Automation Gateway** for PatSons Foods (Dev Can Fruit Products, Morjim, Pernem - Goa).

Hosts directly on the same server/machine alongside your Laravel website on port **21465**.

---

## ⚡ Quick 3-Step Setup

### Step 1: Install Dependencies
Open a terminal in this directory:
```bash
cd patsons-wa-gateway
npm install
```

### Step 2: Start the Gateway
```bash
npm start
```

*(For permanent background running with PM2):*
```bash
npm install -g pm2
pm2 start ecosystem.config.js
pm2 save
```

### Step 3: Link Your WhatsApp Phone
1. Go to your PatSons Admin Portal $\rightarrow$ **Store Settings & WhatsApp**.
2. Click **"⚡ Link Device / View QR"**.
3. Open WhatsApp on your phone $\rightarrow$ **Settings** $\rightarrow$ **Linked Devices** $\rightarrow$ **Link a Device**.
4. Scan the QR code displayed on your screen!

✅ **Done!** From now on:
- OTP verification codes are dispatched automatically to customers when they order.
- Order confirmation breakdowns are sent immediately to customers & admin.
- Order dispatch & approval alerts are automatically fired when you update order status in the admin panel!
