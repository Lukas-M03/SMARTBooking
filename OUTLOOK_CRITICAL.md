# 🔴 CRITICAL: Why Your Outlook Connection Isn't Working

## The Root Cause

Your `.env` file has **placeholder values** instead of real Azure credentials:

```env
MICROSOFT_CLIENT_ID=your_client_id              ❌ WRONG - placeholder
MICROSOFT_CLIENT_SECRET=your_client_secret      ❌ WRONG - placeholder
MICROSOFT_TENANT_ID=your_tenant_id              ❌ WRONG - placeholder
```

**Without these real values from Azure, the entire OAuth flow fails.**

---

## 🔧 How to Fix (3 Steps)

### Step 1: Get Azure Credentials
1. Go to https://portal.azure.com
2. Sign in with your account
3. Go to **Azure Active Directory** → **App registrations**
4. Click **New registration**
5. Name it "SMARTBooking"
6. Click **Register**
7. Click on your app
8. Copy **Application (client) ID** → This is `MICROSOFT_CLIENT_ID`
9. Click **Certificates & secrets** on the left
10. Click **New client secret**
11. Copy the value → This is `MICROSOFT_CLIENT_SECRET`
12. Go back to app overview
13. Copy **Directory (tenant) ID** → This is `MICROSOFT_TENANT_ID`

### Step 2: Configure Permissions
1. In your Azure app, click **API permissions**
2. Click **Add a permission**
3. Select **Microsoft Graph**
4. Select **Delegated permissions**
5. Search and add:
   - `Calendars.ReadWrite` ✓
   - `User.Read` ✓
   - `offline_access` ✓
6. Click **Grant admin consent for [Your Org]** ✓

### Step 3: Update .env
Edit `.env` file and replace with your real values:

```env
MICROSOFT_CLIENT_ID=12345678-1234-1234-1234-123456789012
MICROSOFT_CLIENT_SECRET=abc123XYZ789...
MICROSOFT_TENANT_ID=87654321-4321-4321-4321-210987654321
MICROSOFT_REDIRECT_URI=http://localhost:8000/auth/callback
```

Then run:
```bash
php artisan config:cache
php artisan cache:clear
```

---

## ✅ Verification

Test it works:

1. Go to your dashboard
2. See the **Microsoft Outlook Calendar** section
3. Click **Connect Outlook**
4. You should be sent to Microsoft login (NOT an error)
5. After login, you'll see **✓ Connected**

---

## What I Fixed in Your Code

### Issue 1: No Token Refresh ❌ → ✅
**Problem**: Tokens expire after 1 hour, system didn't refresh them
**Solution**: Added automatic refresh before each API call

### Issue 2: No Error Handling ❌ → ✅
**Problem**: Generic exception messages, hard to debug
**Solution**: Added specific error logging and handling

### Issue 3: No Refresh Endpoint ❌ → ✅
**Problem**: Couldn't manually force token refresh
**Solution**: Added `/microsoft/refresh-token` endpoint

### Issue 4: Duplicate Logic ❌ → ✅
**Problem**: Token refresh logic needed in multiple places
**Solution**: Created `HandlesMicrosoftTokens` trait for reuse

---

## Files Modified/Created

```
✏️  app/Http/Controllers/MicrosoftAuthController.php
    - Added token refresh method
    - Better error handling
    - Uses HandlesMicrosoftTokens trait

✏️  app/Services/MicrosoftGraphService.php
    - Auto-validates tokens before API calls
    - Token refresh on initialization
    - Better error handling

✨ app/Traits/HandlesMicrosoftTokens.php
    - NEW: Reusable token management
    - Can be used in any controller/service

✏️  routes/web.php
    - Added /microsoft/refresh-token route

✨ OUTLOOK_SETUP.md
    - NEW: Complete setup guide

✨ OUTLOOK_TROUBLESHOOTING.md
    - NEW: Debugging guide

✨ OUTLOOK_CHANGES.md
    - NEW: What changed explained
```

---

## Common Mistakes

❌ **DON'T**: 
- Use the same redirect URI for dev and production
- Forget to grant admin consent in Azure
- Use incorrect credential values
- Leave credentials in version control

✅ **DO**:
- Use `http://localhost:8000/auth/callback` for local development
- Grant admin consent for permissions
- Copy exact values from Azure Portal
- Never commit `.env` to git

---

## Testing Checklist

```
[ ] Azure app registered
[ ] Client ID, Secret, Tenant ID copied
[ ] API permissions granted with admin consent
[ ] .env updated with real credentials
[ ] php artisan config:cache run
[ ] Application restarted
[ ] "Connect Outlook" button works
[ ] Redirected to Microsoft login page
[ ] Can authorize and get redirected back
[ ] Dashboard shows "✓ Connected"
[ ] Can see token refreshing in logs
```

---

## Still Having Issues?

Check the detailed guides:
- **OUTLOOK_SETUP.md** - Step-by-step Azure setup
- **OUTLOOK_TROUBLESHOOTING.md** - Detailed troubleshooting steps

View logs for errors:
```bash
tail -f storage/logs/laravel.log | grep -i microsoft
```

---

## Quick Reference

| Item | Value |
|------|-------|
| Portal URL | https://portal.azure.com |
| Scopes | Calendars.ReadWrite, User.Read, offline_access |
| Redirect URI (Dev) | http://localhost:8000/auth/callback |
| Redirect URI (Prod) | https://yourdomain.com/auth/callback |
| Token Lifetime | 1 hour (auto refreshed) |
| Refresh Token Lifetime | 90 days |

