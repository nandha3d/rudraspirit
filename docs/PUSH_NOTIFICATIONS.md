# Mobile push notifications (FCM HTTP v1)

The mobile apps receive push (order updates, etc.) via Firebase Cloud Messaging.
The server code was migrated from the dead legacy "server key" API to the
current **HTTP v1** API. It authenticates with a **service-account key** and
mints a short-lived OAuth2 token per request batch.

Until you provide credentials, push is **skipped safely** — order placement and
all other flows work normally; only the push itself is a no-op (and logged).

## One-time setup

1. **Firebase console** → your project → **Project settings → Service accounts**
   → **Generate new private key**. This downloads a JSON file.
2. Upload it to the server **outside the web root**, e.g.:
   ```
   storage/app/firebase/service-account.json
   ```
   (That path is gitignored — never commit the key.)
3. In the server `.env`:
   ```
   FIREBASE_PROJECT_ID="your-firebase-project-id"
   FIREBASE_CREDENTIALS="/full/path/to/storage/app/firebase/service-account.json"
   ```
   `FIREBASE_PROJECT_ID` is the `project_id` value inside the JSON.
4. In the admin panel, make sure **Google Firebase** is enabled
   (`get_setting('google_firebase') == 1`) — the order flows only push when it's on.

That's it. No code change, no redeploy needed beyond setting the env vars.

## App side

Each Flutter app must be built with the **matching Firebase config**
(`google-services.json` for Android, `GoogleService-Info.plist` for iOS) from the
**same** Firebase project, and register its device token with the backend (the
V2 API already stores `users.device_token`).

## How it works (for maintainers)

- `config/firebase.php` — project id, credentials path, timeout.
- `App\Services\Firebase\FcmV1Client`:
  - `isConfigured()` — true only when project id + a readable key file are set.
  - builds an RS256 JWT from the service account (signed with `openssl_sign`,
    no external dependency), exchanges it at `oauth2.googleapis.com/token` for an
    access token, and **caches the token** until ~5 min before it expires.
  - `send()` posts the v1 `{"message":{token,notification,data,android,apns}}`
    payload with SSL verification on. Returns false + logs on any failure;
    never throws into the caller.
- `NotificationUtility::sendFirebaseNotification()` calls the client and still
  writes the in-app `FirebaseNotification` record (unchanged), so the app's
  notification list works even if a push fails.

## Troubleshooting

- Nothing arrives, log says "Firebase not configured" → env vars unset or the
  key file path is wrong/unreadable.
- Log "FCM token request failed" → the service-account JSON is invalid or the
  clock is skewed (JWT `iat/exp`); check server time.
- Log "FCM v1 send failed (HTTP 404)" for a token → that device token is stale
  (app reinstalled) — expected; the app re-registers a new token on next launch.
