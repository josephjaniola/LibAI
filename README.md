# LibAI — Library Management System

Place this folder inside your XAMPP `htdocs` directory (example path: `C:\xampp\htdocs\LIBAI`).

## 1. Local setup

1. Copy `.env.example` to a real `.env` file and fill in your credentials.
2. Import `sql/libai_schema.sql` into MySQL (phpMyAdmin or CLI).
3. Update your DB credentials in `.env`.
4. Start Apache & MySQL in XAMPP and open `http://localhost/LIBAI`.

## 2. Secure authentication setup

### Google OAuth 2.0

1. Go to Google Cloud Console.
2. Create an OAuth 2.0 Client ID.
3. Add the authorized redirect URI:
   - `http://localhost/LIBAI/?url=auth/googleCallback`
4. Copy the client ID and secret to `.env`:
   - `GOOGLE_CLIENT_ID=...`
   - `GOOGLE_CLIENT_SECRET=...`
5. The login screen button now redirects to Google’s official consent screen.

### SMS OTP via Twilio

1. Create a Twilio account and a phone number.
2. Add the credentials in your `.env` file:
   - `TWILIO_SID=...`
   - `TWILIO_AUTH_TOKEN=...`
   - `TWILIO_FROM=+15551234567`
3. The app stores OTPs as hashes rather than plain text and rejects expired or abused codes.

## 3. Environment variables

Use the `.env.example` template included in the project root. The app automatically loads values from `.env`.

## 4. Notes

- OTP codes are hashed with `APP_OTP_SECRET` and expire after the value in `OTP_CODE_TTL_SECONDS`.
- Login attempts are rate-limited with resend cooldowns and a max attempt count.
- Google login only accepts a verified Google email.
- Phone login validates the number on the server before sending a real SMS.

## 5. Additional tools

- To enable PDF exports, install Dompdf:

```bash
composer require dompdf/dompdf
```

- To generate bcrypt password hashes for local users:

```bash
php tools/generate_password_hash.php your_password_here
```

- To simulate RFID scans during development:

```bash
python tools/rfid_reader_sample.py
```
