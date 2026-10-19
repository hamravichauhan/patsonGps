module.exports = {
  apps: [
    {
      name: 'patsons-wa-gateway',
      script: 'server.js',
      instances: 1,
      autorestart: true,
      watch: false,
      max_memory_restart: '800M',
      env: {
        NODE_ENV: 'production',
        PORT: 21465,
        WHATSAPP_SECRET_TOKEN: 'THISISMYSECURETOKEN'
      }
    }
  ]
};
