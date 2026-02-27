# Outlook API Integration - Implementation Checklist & Troubleshooting

## Quick Diagnosis

Run this command to check your setup:

```bash
php artisan tinker
```

Then run:
```php
$user = \App\Models\User::where('microsoft_token', '!=', null)->first();
$user ? dd($user->hasMicrosoftToken()) : dd('No users with tokens');
```

---

## Implementation Checklist

### ✓ Code Changes (Already Done)

- [x] MicrosoftAuthController enhanced with token refresh
- [x] MicrosoftGraphService updated with automatic token validation
- [x] HandlesMicrosoftTokens trait created for reusable token logic
- [x] Token refresh route added (`/microsoft/refresh-token`)
- [x] Configuration file (config/microsoft.php) exists

### ⚠️ What You Need to Do

#### 1. **Azure Portal Setup** (CRITICAL - This is likely why it's not working)
   - [ ] Register app at https://portal.azure.com
   - [ ] Get Client ID, Client Secret, Tenant ID
   - [ ] Configure API permissions (Calendars.ReadWrite, User.Read, offline_access)
   - [ ] Grand admin consent to permissions
   - [ ] Add redirect URI: `http://localhost:8000/auth/callback`

#### 2. **Environment Variables** (CRITICAL)
Update `.env` file:
```env
MICROSOFT_CLIENT_ID=<your_client_id>
MICROSOFT_CLIENT_SECRET=<your_client_secret>
MICROSOFT_TENANT_ID=<your_tenant_id>
```

#### 3. **Database** 
Check migration has run:
```bash
php artisan migrate:status
```
If not run:
```bash
php artisan migrate
```

#### 4. **Clear Config Cache**
```bash
php artisan config:cache
php artisan cache:clear
```

---

## How the Integration Works

### 1. **User Clicks "Connect Outlook"**
   - Route: `/microsoft/connect`
   - Redirects to Microsoft login with scopes

### 2. **Microsoft OAuth Callback**
   - Route: `/auth/callback`
   - Exchanges authorization code for access token
   - Saves tokens to user record:
     - `microsoft_token` - Access token (1 hour validity)
     - `microsoft_refresh_token` - Refresh token (90 days validity)
     - `microsoft_token_expires_at` - Expiration timestamp

### 3. **Using the Connection**
   - **Before any API call**: Tokens are automatically validated/refreshed
   - **For expired tokens**: System uses refresh token to get new access token
   - **If refresh fails**: Tokens are cleared and user must reconnect

### 4. **Calendar Events**
   - Create event: Booking confirmed → Event added to calendar
   - Update event: Booking status changes → Event updated
   - Delete event: Booking cancelled → Event removed

---

## Troubleshooting Steps

### Problem: "Not Connected" still shows after connecting

**Cause 1: Database issue**
```bash
# Check if columns exist
php artisan tinker
> \App\Models\User::find(1)->fresh()->only(['microsoft_token', 'microsoft_token_expires_at'])
```

**Fix**: Run migration
```bash
php artisan migrate --path=database/migrations/2026_02_19_000000_add_microsoft_columns_to_users_table.php
```

---

### Problem: "Authorization failed" error when connecting

**Cause 1: Redirect URI mismatch**
- Check in Azure Portal → App registration → Authentication
- Must match exactly with `.env` `MICROSOFT_REDIRECT_URI`

**Cause 2: Missing API permissions**
- Go to Azure Portal → API permissions
- Must have: Calendars.ReadWrite, User.Read, offline_access
- Must grant admin consent ✓

**Cause 3: Invalid credentials in .env**
- Verify values are EXACTLY from Azure Portal
- No extra spaces or quotes

**Fix**:
```bash
# Clear cached config
php artisan config:cache
php artisan cache:clear
```

---

### Problem: Token refresh fails, stuck "Not Connected"

**Check logs**:
```bash
# View recent errors
tail -f storage/logs/laravel.log | grep -i microsoft
```

**Common causes**:
1. Client secret expired in Azure
2. Refresh token expired (90 days max)
3. Application deleted from Azure
4. Permissions revoked

**Fix**: 
- User must disconnect and reconnect
- Or manually clear tokens:
```bash
php artisan tinker
> $user = \App\Models\User::find(1);
> $user->microsoft_token = null;
> $user->microsoft_refresh_token = null;
> $user->microsoft_token_expires_at = null;
> $user->save();
```

---

### Problem: "Api permission validation failed" in Azure

**Fix**:
1. Go to Azure Portal → API permissions
2. Remove and re-add:
   - Calendars.ReadWrite (Delegated)
   - User.Read (Delegated)
   - offline_access (Delegated)
3. Click "Grant admin consent for [org]" ✓

---

## Testing the Integration

### Manual Test 1: Check Connection Status
```bash
php artisan tinker
```
```php
$user = Auth::user(); // or \App\Models\User::find(1)
$user->hasMicrosoftToken() // Should return true if connected
```

### Manual Test 2: Try Refreshing Token
```bash
php artisan tinker
```
```php
$user = \App\Models\User::where('microsoft_token', '!=', null)->first();
app(MicrosoftAuthController::class)->refreshToken(new Request())
```

### Manual Test 3: Create Calendar Event (if connected)
```bash
php artisan tinker
```
```php
$user = \App\Models\User::where('microsoft_token', '!=', null)->first();
$service = new \App\Services\MicrosoftGraphService($user);
$event = \App\Services\MicrosoftGraphService::formatBookingAsEvent(
    \App\Models\Booking::first(),
    \App\Models\User::find(2)
);
$service->createBookingEvent($event);
```

---

## Environment Variables Reference

### Development (.env)
```env
MICROSOFT_CLIENT_ID=12345678-1234-1234-1234-123456789012
MICROSOFT_CLIENT_SECRET=abc123...xyz
MICROSOFT_TENANT_ID=87654321-4321-4321-4321-210987654321
MICROSOFT_REDIRECT_URI=http://localhost:8000/auth/callback
```

### Production
```env
MICROSOFT_CLIENT_ID=<production_client_id>
MICROSOFT_CLIENT_SECRET=<production_client_secret>
MICROSOFT_TENANT_ID=<production_tenant_id>
MICROSOFT_REDIRECT_URI=https://yourdomain.com/auth/callback
```

**Important**: Update redirect URI in Azure Portal when deploying!

---

## Log Locations

```bash
# Laravel application logs
tail -f storage/logs/laravel.log

# Filter for Microsoft errors
grep -i microsoft storage/logs/laravel.log

# View recent 50 lines
tail -50 storage/logs/laravel.log
```

---

## API Endpoints Reference

| Endpoint | Method | Purpose | Auth |
|----------|--------|---------|------|
| `/microsoft/connect` | GET | Start OAuth flow | Required |
| `/auth/callback` | GET | OAuth callback | Not needed |
| `/microsoft/disconnect` | GET | Disconnect Outlook | Required |
| `/microsoft/refresh-token` | POST | Manually refresh token | Required |

---

## Security Notes

1. **Never commit `.env`** to version control
2. **Tokens are long-lived** (90 days for refresh token)
3. **Always use HTTPS** in production
4. **Client secret should be rotated** periodically in Azure
5. **Monitor logs** for failed token operations

---

## Key Files Modified/Created

```
app/
  Controllers/
    MicrosoftAuthController.php       ← Enhanced with token refresh
  Services/
    MicrosoftGraphService.php         ← Added auto token validation
  Traits/
    HandlesMicrosoftTokens.php        ← NEW: Reusable token logic
  Models/
    User.php                          ← Has hasMicrosoftToken() method

config/
  microsoft.php                       ← Loads env variables

routes/
  web.php                             ← Added refresh-token route

database/
  migrations/
    2026_02_19_*_add_microsoft...     ← Adds token columns

OUTLOOK_SETUP.md                      ← NEW: Setup instructions
```

---

## Next Steps After Setup

1. **Test the connection** on all dashboard types (student, adviser, admin)
2. **Create a booking** and verify it appears in Outlook calendar
3. **Update booking status** and verify calendar event updates
4. **Disconnect** and reconnect to verify flow works

---

## Contact & References

- [Microsoft Graph API Docs](https://learn.microsoft.com/en-us/graph/)
- [Azure Active Directory Docs](https://learn.microsoft.com/en-us/azure/active-directory/)
- [OAuth 2.0 Authorization Code Flow](https://learn.microsoft.com/en-us/azure/active-directory/develop/v2-oauth2-auth-code-flow)
