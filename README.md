# Checklist
by Mats Ljungquist

## What?
This manages a checklist; the user can add/delete/check the notes in the checklist.

A checklist, in this context, is a list of words/notes sorted into categories. The
checklist could be used as an inventory check when planning a trip. Example:

Clothes
- hat
- shoes
- socks

Clothes is a category/tag and hat, shoes, socks is notes on that category.

In the app, every note is checkable (to mark that it has been packed, for example).

## Installation
1. Copy the project to your apache www-directory.
2. Install the database tables and stored routines. To do this
you must alter the appropriate db-credentials in checklink/sql/config.php and then goto the url
<your site>/checklink/pages/install/PInstall.php. Click the link. Scroll down and check the
status report. If any errors has occurred its probably something wrong with your db
credentials or with a firewall.
3. Remove the directory <your site>/checklink/pages/install and all its contents if you're installing for production.
4. Open phpmyadmin (or similar) and enter your own user data in the user-table
5. goto <your site>/checklink/ you should now be prompted with a login-page. Login with your user (or one of the pre prepared ones).

