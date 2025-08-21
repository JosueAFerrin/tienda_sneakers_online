// environments/environment.prod.js
module.exports = {
  environment: {
    production: true,
    apiUrl: process.env.API_URL || 'http://localhost:3000'
  }
};
