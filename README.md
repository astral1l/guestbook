# Guestbook

A small guestbook on plain PHP. Visitors leave messages, everyone sees them on one page, and a single admin can log in and delete messages.

## Requirements

- PHP 8.1 or newer
- PDO SQLite extension (`pdo_sqlite`)
- Apache/nginx with the document root pointed at `public/`, or the PHP built-in server for local work

The SQLite extension is not always enabled by default. Check it with `php -m | grep -i sqlite`, and if it is missing uncomment `extension=pdo_sqlite` in `php.ini`. On Debian/Ubuntu the package is `php-sqlite3`, on Arch it is `php-sqlite`.

## Setup

1. Copy the config file:

   ```
   cp config.example.php config.php
   ```

2. Create a password hash for the admin and put it into `config.php`:

   ```
   php -r "echo password_hash('your-password', PASSWORD_DEFAULT), PHP_EOL;"
   ```

   The example config already has a hash for `admin` / `admin123`, so the app works after the setup steps below. Change it before putting the site anywhere real.

3. Create the database and the table:

   ```
   php bin/init-db.php
   ```

   This creates `storage/guestbook.sqlite` and runs `database/schema.sql`. It is safe to run more than once.

4. Start the app:

   ```
   php -S localhost:8000 -t public public/index.php
   ```

   The guestbook is at http://localhost:8000, the admin page at http://localhost:8000/admin.

## Web server

The document root has to be `public/`. Config, source code and the database are outside it, so they cannot be requested from the browser.

For Apache, `public/.htaccess` already contains the rewrite rule. It needs `mod_rewrite` and `AllowOverride All` (or the same rule in the virtual host). For nginx, point `root` at `public/` and send requests for missing files to `/index.php`.

## Docker

```
docker compose up
```

Then open http://localhost:8080. On the first start the container copies `config.example.php` to `config.php` if needed and creates the database. The SQLite file lives in the `storage` volume, so messages survive a restart.

## Tests

```
php tests/run.php
```

A small runner with no dependencies. It checks the validation rules, pagination numbers and HTML escaping.

## Structure

```
public/          front controller, assets, Apache rewrite
src/             bootstrap, router, database, messages, auth, csrf, helpers
views/           PHP templates
bin/init-db.php  creates the database from database/schema.sql
storage/         SQLite file (gitignored)
tests/run.php    test runner
config.example.php
docker-compose.yml
```

## Security

- All output goes through `e()`, which is `htmlspecialchars()` with `ENT_QUOTES` and UTF-8. The message text keeps line breaks but `<script>` is shown as text.
- Every query is prepared with PDO; user input is never concatenated into SQL.
- The admin password is stored as a hash and checked with `password_verify()`. The login from the config is compared with `hash_equals()`.
- The delete action checks the session itself, not only the admin page. A request without a logged-in admin is redirected to `/admin`.
- The message form, login form and every delete button include a CSRF token tied to the session.
- The message form has a hidden honeypot field and a 30 second cooldown per session.
- The session cookie is `httponly` and `samesite=Lax`, and the session id is regenerated on login.
- `config.php` and the SQLite file are outside `public/` and listed in `.gitignore`.
- Email is stored, but it is never selected on the public pages or printed anywhere.
- `debug` is on in the example config. Set it to `false` on a real server to hide error output.

## Why these technologies

The app is small enough that a framework would add more setup than code, so I wrote a router of about 40 lines instead of taking Slim. Templates are plain PHP instead of Twig, because there is one list page and one admin form and neither needs a template language. For the database I picked PDO with SQLite instead of Doctrine DBAL, Eloquent or a MySQL server: there is a handful of queries and SQLite needs no separate service for a demo. I skipped Composer completely, since the only third-party things would be PHPUnit and maybe dotenv, and the project does not need either. I wrote the CSRF check, validation, pagination and a tiny test runner myself; they are small enough that a package would hide more than it saves at this size. The cost of no Composer is manual `require` calls and no autoloading, and I would add it once there are more classes, real migrations, logging or PHPUnit. SQLite is fine for one small site, though it is weak on concurrent writes. With a million messages and 100 admins I would move to PostgreSQL or MySQL, add indexes, switch from `OFFSET` to cursor pagination, put admins in a separate table with roles, cache the first page and probably take a framework for auth and background jobs.

## Not included

No moderation queue, editing or multiple admins; the task did not ask for them. The cooldown is per session instead of per IP, which is easy to bypass but enough for a demo. For real traffic I would add an IP-based limit or a captcha.
