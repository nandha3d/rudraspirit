<?php

/*
|--------------------------------------------------------------------------
| Firebase Cloud Messaging (HTTP v1)
|--------------------------------------------------------------------------
|
| Push notifications for the mobile apps use the FCM HTTP v1 API, which
| authenticates with an OAuth2 token minted from a service-account key
| (the legacy "server key" API was shut down by Google in 2024).
|
| To enable push:
|   1. Firebase console → Project settings → Service accounts →
|      "Generate new private key" → download the JSON.
|   2. Put the file OUTSIDE the web root (e.g. storage/app/firebase/
|      service-account.json) and point FIREBASE_CREDENTIALS at it.
|   3. Set FIREBASE_PROJECT_ID to your project id.
|
| Until both are set, push is skipped safely (order flows are unaffected).
|
*/

return [

    // Firebase project id, e.g. "animazon-shop". Found in the service-account
    // JSON as "project_id" and in the Firebase console.
    'project_id' => env('FIREBASE_PROJECT_ID', ''),

    // Absolute path to the service-account JSON key file. Keep it out of the
    // web root and out of git.
    'credentials' => env('FIREBASE_CREDENTIALS', ''),

    // HTTP timeout (seconds) for token + send requests.
    'timeout' => (int) env('FIREBASE_TIMEOUT', 10),

];
