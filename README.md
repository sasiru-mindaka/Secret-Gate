<div align="center">

# Secret Gate

### A zero-knowledge, ephemeral messaging engine that never sees your secrets.

**Pure Native PHP. No frameworks. No bloat. No trust required.**

[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-PDO-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Redis](https://img.shields.io/badge/Redis-Rate%20Limiting-DC382D?style=for-the-badge&logo=redis&logoColor=white)](https://redis.io/)
[![License: AGPL v3](https://img.shields.io/badge/License-AGPL%20v3-blue?style=for-the-badge)](https://www.gnu.org/licenses/agpl-3.0.html)

[Live Demo](https://secretgate.site) · [Report a Bug](https://github.com/sasiru-mindaka/Secret-Gate/issues) · [Request a Feature](https://github.com/sasiru-mindaka/Secret-Gate/issues)

</div>

---

## Overview

**Secret Gate** is a self-hostable, ephemeral messaging engine for people who want an anonymous inbox without handing a server the keys to read it. You generate a link, share it, and anyone can drop an encrypted message into it — but the encryption and decryption happen entirely in the visitor's browser using the **Web Crypto API**. The server only ever stores ciphertext it is mathematically incapable of decrypting.

There's no signup, no email, no phone number, and no tracking. Just a keypair, a password, and a link. When a message or link expires, it's gone — enforced at the database and cache layer, not by a "trust us" policy.

It was built deliberately **without** a framework. No Composer dependency tree, no `node_modules`, no build step — just a handful of clean, native PHP files you can read top to bottom in an afternoon, deployable on almost any LAMP-style stack.

---

## ✨ Key Features

- 🔐 **True Zero-Knowledge Encryption** — Hybrid RSA-OAEP (2048-bit) + AES-256-GCM performed entirely client-side via `window.crypto.subtle`. The server stores only ciphertext and a public key; it never sees plaintext, passwords, or private keys.
- 🗝️ **Password-Wrapped Private Key, Zero Server Exposure** — Each inbox's RSA private key is wrapped with a PBKDF2-derived (300,000 iterations) AES-256-GCM key and held only inside the browser's IndexedDB vault, itself sealed behind a non-extractable, per-device AES-GCM key — never transmitted, never logged.
- 🪶 **Zero Framework Bloat** — No Laravel, no Symfony, no Composer sprawl. Just clean, native, auditable PHP across roughly a dozen files.
- ⏳ **Ephemeral by Design** — Per-link message TTLs and inactivity-based account expiry, enforced via MySQL `expires_at` columns — not a soft "please delete this" toggle.
- 🛡️ **Hardened Security Baseline** — Argon2id password hashing, strict same-origin + CSRF token validation on every state-changing request, PDO prepared statements everywhere, per-request CSP nonces, and a whitelist-only file execution model via `.htaccess`.
- 🕵️ **Privacy by Default** — No accounts, no email, no PII collected or stored, ever. What we don't collect, we can't leak.
- 🚦 **Abuse-Resistant** — Redis-backed sliding-window rate limiting, IP blacklisting, repeat-offender "burning", honeypot fields, and Cloudflare Turnstile on public-facing forms. Redis is a hard dependency: if it's unreachable, Secret Gate fails closed with a maintenance page rather than silently degrading.
- 📦 **Deploy Anywhere** — Runs on any standard Apache + PHP-FPM + MySQL + Redis stack. No containers required (though it'll happily run in one).

---

## 🏗️ Architecture & Encryption Flow

Secret Gate uses **hybrid encryption**: an RSA-OAEP keypair identifies the inbox, while every individual message gets its own single-use AES-256-GCM key — the best of both worlds for a multi-sender, single-recipient model.

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         1. INBOX CREATION (Owner)                           │
│                                                                             │
│   Browser                                                                   │
│   ├─ Generate RSA-OAEP 2048-bit keypair              (window.crypto.subtle) │
│   ├─ Derive AES-256-GCM wrapping key                 (PBKDF2, 300k iters)   │
│   ├─ Encrypt(private_key, wrapping_key)         ──►  store in IndexedDB     │
│   │                                                  (never leaves device)  │
│   └─ POST public_key (JWK) + Argon2id(password) ──► Server                  │
│                                                                             │
│   Server (api.php)                                                          │
│   └─ INSERT INTO sb_links (public_id, public_key, password_hash)            │
│      (server never sees the private key or the plaintext password)          │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│                       2. SENDING A MESSAGE (Sender)                         │
│                                                                             │
│   Browser                             Server                                │
│   ├─ GET public_key (JWK) for link          ──►  fetch from sb_links        │
│   ├─ Generate one-time AES-256-GCM key                                      │
│   ├─ Encrypt(message, AES key)                                              │
│   ├─ Encrypt(AES key, RSA-OAEP public_key)                                  │
│   └─ POST { encrypted_message, wrapped_key } ──►  INSERT sb_messages        │
│                                                     (ciphertext only)       │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│                      3. READING MESSAGES (Owner)                            │
│                                                                             │
│   Browser                                Server                             │
│   ├─ Unlock local vault with password                                       │
│   │  └─ Derive wrapping key (PBKDF2)   ──►  decrypt private key locally     │
│   ├─ GET encrypted messages            ──►  SELECT WHERE not expired        │
│   └─ Decrypt(AES key, private_key)     ──►  Decrypt(message, AES key)       │
│      (all decryption happens on-device ──►  server output stays opaque)     │
└─────────────────────────────────────────────────────────────────────────────┘
```

**Why hybrid encryption?** RSA-OAEP lets *any* anonymous sender encrypt a message using only the public key — no shared secret, no prior handshake. AES-256-GCM keeps per-message encryption fast and keeps ciphertext size sane, since RSA alone isn't built for encrypting arbitrary-length payloads.

---

## 🚀 Prerequisites & Quick Start

### Prerequisites

- PHP 8.0+ with `pdo_mysql`, `redis`, and `openssl` extensions enabled
- MySQL 8.0+ (or MariaDB equivalent)
- **Redis server (required)** — powers rate limiting, IP blacklisting, and abuse "burning." Secret Gate fails closed (serves a 503 maintenance page) if Redis is unreachable, by design — there is no silent fallback
- Apache with `mod_rewrite` and `mod_headers`
- A [Cloudflare Turnstile](https://www.cloudflare.com/products/turnstile/) site/secret key pair (used on the feedback form)
- A Telegram bot token + chat ID (used to relay feedback submissions — required for the feedback form to function)

### 1. Clone the repository

```bash
git clone https://github.com/sasiru-mindaka/Secret-Gate.git
cd Secret-Gate
```

### 2. Configure your environment

Create an `.env` file (outside the web root is strongly recommended) with the following keys:

```env
APP_ENV=production

# Database
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USER=your_db_user
DB_PASS=your_db_password
DB_NAME=schema

# Redis (required — Secret Gate will not serve traffic without it)
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASS=

# Cloudflare Turnstile
CF_TURNSTILE_SECRET_KEY=your_turnstile_secret

# Telegram feedback relay
TELEGRAM_BOT_TOKEN=your_bot_token
TELEGRAM_CHAT_ID=your_chat_id
```

> By default, `config.php` loads `.env` from **one directory above your web root** (`__DIR__ . '/../.env'`) — e.g. if your site lives at `/var/www/html`, place `.env` at `/var/www/.env`. This keeps credentials outside the publicly served directory even if the web server itself is ever misconfigured. Only change the path in `loadEnv()` if your server layout is different from this.

### 3. Import the database schema

```bash
mysql -u your_db_user -p schema < schema.sql
```

This creates the two core tables:

| Table | Purpose |
|---|---|
| `sb_links` | Stores the inbox's public key, Argon2id password hash, and TTL settings — never the private key |
| `sb_messages` | Stores ciphertext only, with an optional `expires_at` for self-destructing messages |

### 4. Point your web server at the project root

Apache is expected — `.htaccess` handles routing, denies direct access to every `.php` file by default, and whitelists only the entry-point scripts (`index.php`, `api.php`, `feedback.php`, etc.). Make sure `AllowOverride All` is set for the vhost.

### 5. Visit your domain and generate your first link 🎉

---

## 🔍 Data Minimization & Security Matrix

| Layer | Protection | Implementation |
|---|---|---|
| **Message Content** | Never readable by the server | Client-side AES-256-GCM, per-message single-use key |
| **Private Keys** | Never transmitted | RSA-OAEP private key wrapped locally with a PBKDF2-derived AES-256-GCM key, stored only in browser IndexedDB behind a non-extractable device key |
| **Passwords** | Never stored in plaintext | Argon2id hashing (`PASSWORD_ARGON2ID`) |
| **Database Access** | No injection surface | 100% PDO prepared statements, zero raw query concatenation |
| **State-Changing Requests** | CSRF-protected | Per-session CSRF tokens with expiry, validated via `hash_equals()` |
| **Cross-Origin Requests** | Rejected by default | Strict Origin/Referer host matching, same-origin-only CORS headers, XHR-only enforcement on internal APIs |
| **Bot / Abuse Traffic** | Rate-limited & blocked, no message-linked IP logging | Redis-backed sliding-window rate limiting, IP blacklisting, and permanent "burning" for repeat offenders; the app fails closed (503) if Redis is unreachable. IPs are used only for this abuse-prevention logic — never stored alongside messages or link data |
| **Data Expiry** | Enforced, not optional | Per-message TTL and inactivity-based link auto-deletion, swept on every request |
| **File/Config Exposure** | Locked down | `.htaccess` denies direct execution of every PHP file except a strict whitelist, blocks dotfiles, and blocks backup/config/log extensions (`.env`, `.sql`, `.log`, `.bak`, and more) |
| **Response Headers** | Hardened by default | Per-request CSP with nonces, HSTS, `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, a locked-down `Permissions-Policy`, and no-cache headers on every response |
| **Personal Data** | Not collected | No accounts, no email, no phone number, no analytics or tracking scripts |
| **Sessions** | Hardened cookies | `HttpOnly`, `Secure`, `SameSite=Strict`, strict mode, `read_and_close` session handling |

---

## 📄 License

Secret Gate is released under the **[GNU Affero General Public License v3.0 or later](https://www.gnu.org/licenses/agpl-3.0.html)**.

In short: you're free to use, modify, and self-host this project — but if you run a modified version as a network service, you must make your modified source available to your users too. See [`LICENSE`](LICENSE) for the full text.

---

<div align="center">

**Built by [Sasiru Mindaka](https://github.com/sasiru-mindaka)**

If Secret Gate is useful to you, consider dropping a ⭐ — it helps the project reach more people who care about privacy.

</div>