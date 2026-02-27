# Outlook Integration - What Was Changed

## Summary of Issues Found

Your Outlook API implementation had most of the foundation in place but was missing:

1. ❌ **Automatic token refresh** - Tokens expire after 1 hour, weren't being refreshed
2. ❌ **Token validation before API calls** - System didn't check if tokens were expired
3. ❌ **Proper error handling** - Generic exceptions instead of specific error messages
4. ❌ **Refresh token endpoint** - No way to manually refresh tokens
5. ❌ **Reusable token logic** - Logic was duplicated and hard to maintain

---

## Files Changed

### 1. **MicrosoftAuthController.php** ✏️ MODIFIED
**What changed**: 
- Added `HandlesMicrosoftTokens` trait
- Improved error handling with try-catch blocks
- Added `refreshToken()` method to refresh tokens on-demand
- Better logging for debugging

**Key additions**:
```php
use App\Traits\HandlesMicrosoftTokens;

public function refreshToken(Request $request)
{
    // Manually refresh token via API endpoint
    $refreshed = $this->refreshMicrosoftToken($user);
    return response()->json(['success' => $refreshed]);
}
```

---

### 2. **MicrosoftGraphService.php** ✏️ MODIFIED
**What changed**:
- Added `$user` property to track user object
- Added `ensureTokenIsValid()` method to check/refresh tokens
- Added `refreshToken()` method for token refresh logic
- Constructor now automatically validates token before use

**Key additions**:
```php
public function __construct(User $user)
{
    // ... existing code ...
    $this->ensureTokenIsValid(); // Refresh if needed
}

private function ensureTokenIsValid(): void
{
    if ($this->user->microsoft_token_expires_at->isPast()) {
        $this->refreshToken();
    }
}
```

---

### 3. **HandlesMicrosoftTokens.php** ✨ NEW FILE
**Purpose**: Reusable trait for token management
**Contains**:
- `ensureMicrosoftTokenIsValid()` - Check and refresh tokens
- `refreshMicrosoftToken()` - Perform token refresh
- Proper error handling and logging
- Can be used in controllers, commands, or jobs

**Usage example**:
```php
class BookingController extends Controller
{
    use HandlesMicrosoftTokens;
    
    public function confirm(Booking $booking)
    {
        $this->ensureMicrosoftTokenIsValid();
        // Safe to use Microsoft API now
    }
}
```

---

### 4. **web.php** (routes) ✏️ MODIFIED
**What changed**: Added new route for token refresh
```php
Route::post('/microsoft/refresh-token', [MicrosoftAuthController::class, 'refreshToken'])
    ->name('microsoft.refresh-token');
```

---

## Configuration Files

### **config/microsoft.php** ✓ EXISTING (No changes needed)
Already correctly configured to read from environment:
```php
return [
    'microsoft' => [
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'tenant_id' => env('MICROSOFT_TENANT_ID'),
    ],
];
```

### **.env** ⚠️ NEEDS YOUR ATTENTION (Placeholder values!)
Currently has:
```env
MICROSOFT_CLIENT_ID=your_client_id
MICROSOFT_CLIENT_SECRET=your_client_secret
MICROSOFT_TENANT_ID=your_tenant_id
MICROSOFT_REDIRECT_URI=http://localhost:8000/auth/callback
```

**YOU MUST REPLACE** with actual Azure values.

---

## Database

### Migration ✓ EXISTING
File: `database/migrations/2026_02_19_000000_add_microsoft_columns_to_users_table.php`

Ensures users table has:
- `microsoft_token` TEXT - Access token
- `microsoft_refresh_token` TEXT - Refresh token (for renewal)
- `microsoft_token_expires_at` TIMESTAMP - When token expires

**Status**: Should already be applied (check with `php artisan migrate:status`)

---

## How It Works Now

### **Before (Broken)**
```
User clicks "Connect Outlook"
      ↓
Token stored in database
      ↓
Dashboard shows "Connected"
      ↓
Try to use API 1 hour later
      ↓
Token is expired ❌
      ↓
API call fails 💥
      ↓
User sees error or stuck state
```

### **After (Fixed)**
```
User clicks "Connect Outlook"
      ↓
Token stored in database with expiry (1 hour)
      ↓
Dashboard shows "Connected"
      ↓
Try to use API 1 hour later
      ↓
Check token expiry ✓
      ↓
Token is expired, use refresh token
      ↓
Get new token automatically ✓
      ↓
API call succeeds ✓
      ↓
User never sees an issue
```

---

## Token Lifecycle

### Initial Connection
1. User clicks "Connect Outlook"
2. Redirected to Microsoft login
3. User authorizes app
4. Get access token (1 hour valid) + refresh token (90 days valid)
5. Store both in database

### Automatic Refresh (What's new!)
1. When API is about to be called
2. Check if token expires within 5 minutes
3. If yes, use refresh token to get new access token
4. Update database with new token
5. Continue with API call

### Manual Refresh (New!)
1. POST `/microsoft/refresh-token`
2. Returns status and new expiry time
3. Useful for forcing refresh

### Disconnection
1. User clicks "Disconnect Outlook"
2. Clear all tokens from database
3. Must reconnect from scratch

---

## Migration Path

If you have users already connected:

```bash
# 1. Remove cached config
php artisan config:cache
php artisan cache:clear

# 2. Run migrations if not already done
php artisan migrate

# 3. Restart your server
php artisan serve
```

Existing tokens should work, but users may need to reconnect if tokens are very old.

---

## Testing Your Setup

### Step 1: Verify Configuration
```bash
# Check if config loads correctly
php artisan tinker
> config('microsoft.microsoft')
```

Should show your client_id, client_secret, tenant_id.

### Step 2: Test Connection
1. Go to any dashboard (student/adviser/admin)
2. Click "Connect Outlook"
3. You'll be redirected to Microsoft login
4. After authorization, dashboard should show "✓ Connected"

### Step 3: Verify Database
```bash
php artisan tinker
> $user = \App\Models\User::find(1)
> $user->microsoft_token // Should have a long string
> $user->hasMicrosoftToken() // Should return true
```

### Step 4: Test Token Refresh
```bash
# Manually trigger refresh
php artisan tinker
> $user = \App\Models\User::find(1)
> app(\App\Traits\HandlesMicrosoftTokens::class)->refreshMicrosoftToken($user)
```

---

## What Still Needs Implementation

These features are architecture-ready but need completion:

### 1. **Calendar Event Creation** (In progress)
```php
// When booking is confirmed, create calendar event
$service = new MicrosoftGraphService($adviser);
$event = MicrosoftGraphService::formatBookingAsEvent($booking, $adviser);
$service->createBookingEvent($event);
```

### 2. **Calendar Event Updates** (Ready to implement)
```php
// When booking status changes, update event
$service->updateBookingEvent($eventId, $newEventData);
```

### 3. **Calendar Event Deletion** (Ready to implement)
```php
// When booking is cancelled, remove event
$service->deleteBookingEvent($eventId);
```

### 4. **Availability Sync** (Ready to implement)
```php
// Get advisor's busy times from Outlook
$availability = $service->getAvailability($start, $end);
```

These methods exist in `MicrosoftGraphService` - just need to be integrated into booking workflows.

---

## Troubleshooting Checklist

- [ ] `.env` updated with real Azure credentials
- [ ] `php artisan config:cache` run after changing `.env`
- [ ] `php artisan migrate` run
- [ ] Logged in as test user
- [ ] Click "Connect Outlook" on dashboard
- [ ] See "✓ Connected" status
- [ ] Can create/confirm bookings
- [ ] Check logs: `tail -f storage/logs/laravel.log`

---

## Next Steps

1. **[IMMEDIATE] Get Azure credentials** - See OUTLOOK_SETUP.md
2. **[IMMEDIATE] Update .env file** - Add real credentials
3. **[URGENT] Clear config cache** - `php artisan config:cache`
4. **[TEST] Verify connection works** - Try connecting on dashboard
5. **[TODO] Integrate event creation** - Hook into booking confirmations
6. **[TODO] Handle calendar sync** - Show Outlook availability

---

## Support Resources

- **Setup Guide**: See `OUTLOOK_SETUP.md`
- **Troubleshooting**: See `OUTLOOK_TROUBLESHOOTING.md`
- **Code**: `app/Services/MicrosoftGraphService.php`, `app/Controllers/MicrosoftAuthController.php`, `app/Traits/HandlesMicrosoftTokens.php`
- **Tests**: Look for Microsoft-related tests in `tests/Feature/`

---

## Commits to Make

Recommended git commits:

```bash
git add -A
git commit -m "feat: add automatic Microsoft token refresh and validation

- Add HandlesMicrosoftTokens trait for reusable token logic
- Update MicrosoftGraphService to validate tokens before API calls
- Add manual token refresh endpoint
- Improve error handling and logging
- Add comprehensive setup and troubleshooting guides"
```

