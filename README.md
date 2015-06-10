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
2. Change the WS_SITELINK in <install dir>/config.php to the appropriate url for your installation. See config.php for more info.
3. Install the database tables and stored routines. To do this
you must alter the appropriate db-credentials in <install dir>/sql/config.php and then goto the url
<host>/<install dir>/pages/install/PInstall.php. Click the link. Scroll down and check the
status report. If any errors has occurred its probably something wrong with your db
credentials or with a firewall.
4. Remove the directory <install dir>/pages/install and all its contents if you're installing for production.
5. Open phpmyadmin (or similar) and create a user (for details see below).
6. goto <your site>/<install dir>/ you should now be prompted with a login-page. Login with your user.

### Create a user

```
--
-- Add default user(s)
--
INSERT INTO {$tUser} (accountUser, emailUser, nameUser, lastLoginUser, passwordUser, activeUser)
VALUES ('doe', 'doe@noreply.se', 'Jane Doe', NOW(), md5('doe'), TRUE);
    
--
-- Add default groups
--
INSERT INTO {$tGroup} (idGroup, nameGroup) VALUES ('usr', 'Regular users of the site');

--
-- Add default groupmembers
--
INSERT INTO {$tGroupMember} (GroupMember_idUser, GroupMember_idGroup)
	VALUES ((SELECT idUser FROM {$tUser} WHERE accountUser = 'doe'), 'usr');

```

