<div align="center">

# 🔒 Secret Gate

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

**Secret Gate** is a self-hostable, ephemeral messaging engine for people who want an anonymous inbox without handing a server the keys to read it. You generate a link, share it, and anyone can drop an encrypted message into it — but the encryption/decryption happens entirely in the visitor's browser using the **Web Crypto API**. The server only ever stores ciphertext it cannot decrypt.

There's no signup, no email, no phone number, and no tracking. Just a keypair, a password, and a link. When a message or link expires, it's gone — enforced at the database and cache layer, not by a "trust us" policy.

It was built deliberately **without** a framework. No Composer dependency tree, no `node_modules`, no build step. ~10 PHP files you can read top to bottom in an afternoon, deployable on almost any LAMP-style stack.

---

## ✨ Key Features

- 🔐 **True Zero-Knowledge Encryption** — Hybrid RSA-OAEP (2048-bit) + AES-256-GCM performed client-side via `window.crypto.subtle`. The server stores only ciphertext and a public key; it is mathematically incapable of reading message contents.
- 🗝️ **Server-Side Private Key, Zero Exposure** — Each link's private key is wrapped with a password-derived AES-256-GCM key (PBKDF2, 300,000 iterations) and held only in the browser's IndexedDB vault — never transmitted or logged.
- 🪶 **Zero Framework Bloat** — No Laravel, no Symfony, no Composer sprawl. Just clean, native, auditable PHP.
- ⏳ **Ephemeral by Design** — Per-link message TTLs and inactivity-based account expiry, enforced via MySQL `expires_at` columns and Redis TTL keys — not a soft "please delete this" toggle.
- 🛡️ **Hardened Security Baseline** — Argon2id password hashing, strict same-origin + CSRF token validation on every state-changing request, PDO prepared statements everywhere, and a whitelist-only file execution model via `.htaccess`.
- 🕵️ **Privacy by Default** — No accounts, no email, no PII collected or stored, ever. What we don't collect, we can't leak.
- 🚦 **Abuse-Resistant** — Redis-backed sliding-window rate limiting, IP blacklisting/burning, honeypot fields, and Cloudflare Turnstile on public-facing forms.
- 📦 **Deploy Anywhere** — Runs on any standard Apache + PHP-FPM + MySQL stack. No containers required (though it'll happily run in one).

---

## 🏗️ Architecture & Encryption Flow

Secret Gate uses **hybrid encryption**: an RSA-OAEP keypair identifies the inbox, while every individual message gets its own single-use AES-256-GCM key — the best of both worlds for a multi-sender, single-recipient model.

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         1. INBOX CREATION (Owner)                           │
│                                                                             │
│   Browser                                                                   │
│   ├─ Generate RSA-OAEP 2048-bit keypair              (window.crypto.subtle) │
│   ├─ Derive AES-256-GCM wrapping key                 (PBKDF2, password)     │
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
│   ├─ GET public_key(JWK) for link           ──►  fetch from sb_links        │
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
- Redis server *(optional but recommended)* — powers rate limiting and IP blacklisting/burning. If Redis isn't installed or reachable, Secret Gate automatically falls back to a file-based store under `storage/` with the same behavior, so the app still runs without it
- Apache with `mod_rewrite` and `mod_headers`
- A [Cloudflare Turnstile](https://www.cloudflare.com/products/turnstile/) site/secret key pair (used on the feedback form)
- A Telegram bot token + chat ID (used to relay feedback submissions — optional but required for the feedback form to function)

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

# Redis (optional — leave as-is if you're not running Redis;
# the app falls back to file-based rate limiting automatically)
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

Apache is expected — `.htaccess` handles routing, PHP file whitelisting, and blocks direct access to everything except the entry-point scripts (`index.php`, `api.php`, `feedback.php`, etc.). Make sure `AllowOverride All` is set for the vhost.

### 5. Visit your domain and generate your first link 🎉

---

## 🔍 Data Minimization & Security Matrix

| Layer | Protection | Implementation |
|---|---|---|
| **Message Content** | Never readable by the server | Client-side AES-256-GCM, per-message single-use key |
| **Private Keys** | Never transmitted | RSA-OAEP private key wrapped locally with a password-derived AES-256-GCM key, stored only in browser IndexedDB |
| **Passwords** | Never stored in plaintext | Argon2id hashing (`PASSWORD_ARGON2ID`) |
| **Database Access** | No injection surface | 100% PDO prepared statements, zero raw query concatenation |
| **State-Changing Requests** | CSRF-protected | Per-session CSRF tokens with expiry, validated via `hash_equals()` |
| **Cross-Origin Requests** | Rejected by default | Strict Origin/Referer host matching, same-origin-only CORS headers |
| **Bot / Abuse Traffic** | Rate-limited & blocked, no message-linked IP logging | Redis-backed sliding-window rate limiting (auto-falls back to file storage if Redis is unavailable) — request counters expire on their own within the rate window (~30–60s); temporary IP blacklist entries auto-expire (default 24h); repeat offenders can be permanently "burned" and are kept blocked until manually cleared. IPs are used only for this abuse-prevention logic — never stored alongside messages or link data. |
| **Data Expiry** | Enforced, not optional | Per-message TTL and inactivity-based link auto-deletion (MySQL + Redis) |
| **File/Config Exposure** | Locked down | `.htaccess` denies direct PHP execution outside a strict whitelist, blocks `.env`, `.sql`, `.log`, and other sensitive extensions |
| **Personal Data** | Not collected | No accounts, no email, no phone number, no analytics/tracking scripts |
| **Sessions** | Hardened cookies | `HttpOnly`, `Secure`, `SameSite=Lax`, strict mode, `read_and_close` session handling |

---

## 📄 License

Secret Gate is released under the **[GNU Affero General Public License v3.0 or later](https://www.gnu.org/licenses/agpl-3.0.html)**.

In short: you're free to use, modify, and self-host this project — but if you run a modified version as a network service, you must make your modified source available to your users too. See [`LICENSE`](LICENSE) for the full text.

---

<div align="center">

**Built by [Sasiru Mindaka](https://github.com/sasiru-mindaka)**

If Secret Gate is useful to you, consider dropping a ⭐ — it helps the project reach more people who care about privacy.

</div>