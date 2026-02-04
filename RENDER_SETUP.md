Deploy ServiQ to Render

This guide will help you deploy ServiQ to Render using SQLite.

Prerequisites

A GitHub account with this repository pushed
A Render account

Deployment Steps

1. Create a New Web Service on Render

Go to https://dashboard.render.com
Click New + button and select Web Service
Connect your GitHub account if you haven't already
Select the repository: jerome

2. Configure the Web Service

Use the following settings:

Name: serviq
Region: Choose the closest region to you
Branch: main
Runtime: Docker
Instance Type: Free

3. Environment Variables

Render will automatically read most settings from render.yaml, but you need to set:

You must generate an APP_KEY. Click Generate or use this format:
base64:RANDOM_32_CHARACTER_STRING_HERE

The app will also auto-generate one if not set, but it's better to set it manually.

Add these environment variables in Render dashboard:
APP_KEY: Click Generate button in Render
APP_URL: Your Render app URL

All other variables are already configured in render.yaml:
DB_CONNECTION=sqlite
SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
APP_ENV=production
APP_DEBUG=false

4. Deploy

Click Create Web Service
Render will automatically:
Build the Docker image
Install PHP dependencies
Install Node.js dependencies
Build frontend assets
Create SQLite database
Run migrations
Seed database with test users
Start the application

5. Monitor Deployment

Watch the build logs in Render dashboard
The build process takes 5-10 minutes on the free tier
Once deployed, your app will be available at the URL provided by Render

Default Users

After deployment, you can login with these test accounts:

Admin Account:
Email: admin@example.com
Password: password

Regular User Account:
Email: user@example.com
Password: password

Change these passwords immediately in production.

Database Persistence

The SQLite database is stored in /app/database/database.sqlite
On Render's free tier, the database will reset when the service restarts after 15 minutes of inactivity
For production with data persistence, consider upgrading to a paid plan or using an external database

Troubleshooting

Build Fails

If the build fails, check the logs for:
PHP version compatibility should be 8.2+
Node.js version should be 18+
Composer dependencies

App Key Error

If you see No application encryption key has been specified:
Generate a key: php artisan key:generate --show
Add it to Render environment variables as APP_KEY
Redeploy

Database Errors

If migrations fail:
Check logs for specific error messages
Ensure database directory has write permissions
Try manual deployment with php artisan migrate:fresh --force

Assets Not Loading

If CSS/JS doesn't load:
Check APP_URL matches your Render URL
Verify npm run build completed successfully in logs
Check public/build directory exists

File Structure

Key deployment files:
render.yaml - Render configuration
Dockerfile - Docker container setup
start.sh - Application startup script
build.sh - Build process script
.env.render - Example production environment variables

Auto-Deployment

Once set up, Render will automatically deploy when you push to the main branch on GitHub.

Need Help?

Render Docs: https://render.com/docs
Laravel Docs: https://laravel.com/docs
Check the Render logs for error messages

Upgrading from SQLite

If you later want to use PostgreSQL:
Create a PostgreSQL database in Render
Update environment variables:
DB_CONNECTION=pgsql
DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
Redeploy

Your app should now be live on Render!
