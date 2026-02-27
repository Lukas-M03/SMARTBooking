# Microsoft Outlook Calendar Integration Setup Guide

## Overview
This guide explains how to set up the Azure OAuth integration for Microsoft Outlook calendar synchronization.

## Prerequisites
- Azure account with admin access
- Laravel application running
- Microsoft Graph API access

## Step 1: Register Your Application in Azure

1. Go to [Azure Portal](https://portal.azure.com)
2. Navigate to **Azure Active Directory** → **App registrations**
3. Click **New registration**
4. Fill in the following:
   - **Name**: SMARTBooking
   - **Supported account types**: Accounts in this organizational directory only
   - **Redirect URI**: Web - `http://localhost:8000/auth/callback` (for development)
     - For production: Use your actual domain (e.g., `https://yourdomain.com/auth/callback`)
5. Click **Register**

## Step 2: Configure Application Credentials

1. In your app registration, go to **Certificates & secrets**
2. Under **Client secrets**, click **New client secret**
3. Add a description (e.g., "Laravel App")
4. Set expiration (recommended: 24 months)
5. Click **Add** and copy the secret value immediately
6. Back in the app, go to **Overview** and copy:
   - **Application (client) ID**
   - **Directory (tenant) ID**

## Step 3: Configure API Permissions

1. In your app registration, go to **API permissions**
2. Click **Add a permission**
3. Select **Microsoft Graph**
4. Select **Delegated permissions**
5. Search for and select:
   - `Calendars.ReadWrite` (read/write access to calendars)
   - `User.Read` (read user profile)
   - `offline_access` (refresh token support)
6. Click **Add permissions**
7. Click **Grant admin consent for [Your Organization]** ✓

## Step 4: Update Environment Variables

Update your `.env` file with the values from Azure:

```env
MICROSOFT_CLIENT_ID=your_application_client_id
MICROSOFT_CLIENT_SECRET=your_client_secret
MICROSOFT_TENANT_ID=your_directory_tenant_id
MICROSOFT_REDIRECT_URI=http://localhost:8000/auth/callback (or your production URL)
```

## Step 5: Database Migration

Ensure the database migration has been run:

```bash
php artisan migrate
```

This creates the following columns in the `users` table:
- `microsoft_token` - Access token for API calls
- `microsoft_refresh_token` - Token to refresh the access token
- `microsoft_token_expires_at` - When the access token expires

## Step 6: Test the Integration

1. Navigate to your dashboard (student, adviser, or admin)
2. Look for the **Microsoft Outlook Calendar** section
3. Click **Connect Outlook**
4. You'll be redirected to Microsoft login
5. Authorize the application
6. You should see "✓ Connected" in the dashboard

## Features

### Automatic Token Refresh
- Tokens expire after 1 hour of inactivity
- The system automatically refreshes tokens when needed
- If refresh fails, tokens are cleared and user must reconnect

### Calendar Operations
- Create calendar events for bookings
- Update events when booking status changes
- Delete events when bookings are cancelled
- Automatically sync with user's Outlook calendar

### Error Handling
- Token expiry is checked before each API call
- Failed API calls are logged for debugging
- Clear error messages in UI for connection issues

## Troubleshooting

### "Not Connected" status not changing
- Clear browser cache
- Check `.env` variables are set correctly
- Check that migration has run: `php artisan migrate:status`

### "Authorization failed" error
- Verify the Redirect URI matches exactly in Azure and `.env`
- Ensure API permissions are granted with admin consent
- Check that the application is not expired in Azure

### Token refresh issues
- Check Laravel logs: `storage/logs/laravel.log`
- Verify `microsoft.php` config file is correctly set up
- Ensure `microsoft_refresh_token` is stored in database

### API calls failing
- Check that the user has valid token: `Auth::user()->hasMicrosoftToken()`
- Verify user has granted permissions
- Check Laravel logs for specific error messages

## Production Deployment

Before deploying to production:

1. Change `MICROSOFT_REDIRECT_URI` to your production domain
2. Update the redirect URI in Azure app registration
3. Ensure HTTPS is used for all URLs
4. Verify environment variables are set on your cloud provider
5. Set longer-lived refresh tokens in Azure if needed
6. Monitor logs for token refresh failures

## Database Schema

```sql
ALTER TABLE users ADD COLUMN microsoft_token LONGTEXT;
ALTER TABLE users ADD COLUMN microsoft_refresh_token LONGTEXT;
ALTER TABLE users ADD COLUMN microsoft_token_expires_at TIMESTAMP;
```

## API Endpoints

### Connect to Outlook
- **Route**: `/microsoft/connect`
- **Method**: GET
- **Auth**: Required
- **Redirects**: Microsoft login page

### Handle OAuth Callback
- **Route**: `/auth/callback`
- **Method**: GET
- **Auth**: Not required
- **Redirects**: Dashboard with success/error message

### Disconnect Outlook
- **Route**: `/microsoft/disconnect`
- **Method**: GET
- **Auth**: Required
- **Redirects**: Back to referrer with confirmation

### Refresh Token (Manual)
- **Route**: `/microsoft/refresh`
- **Method**: POST
- **Auth**: Required
- **Returns**: JSON response with status

## Security Considerations

1. **Secrets**: Never commit `.env` to version control
2. **Token Storage**: Tokens are encrypted at rest in database (use Laravel encryption)
3. **HTTPS**: Always use HTTPS in production
4. **Token Expiry**: Tokens expire and are automatically refreshed
5. **Permissions**: Users can disconnect at any time
6. **Scope**: Only request necessary permissions

## References

- [Microsoft Graph API Documentation](https://learn.microsoft.com/en-us/graph/overview)
- [Azure App Registration Guide](https://learn.microsoft.com/en-us/azure/active-directory/develop/quickstart-register-app)
- [OAuth 2.0 Authorization Code Flow](https://learn.microsoft.com/en-us/azure/active-directory/develop/v2-oauth2-auth-code-flow)
