<?php
// ===========================================================================================
//
// config.php
//
// Config-file for database and SQL related issues. All SQL-statements are usually stored in this
// directory (TP_SQLPATH). This files contains global definitions for table names and so.
//
// Author: Mats Ljungquist
//

// -------------------------------------------------------------------------------------------
//
// Settings for the database connection
//
define('DB_HOST', 	'192.168.33.99');           // The database host
define('DB_USER', 	'root');		// The username of the database
define('DB_PASSWORD', 	'');		// The users password
define('DB_DATABASE', 	'sanxion');		// The name of the database to use

//
// The following supports having many databases in one database by using table/view prefix.
//
define('DB_PREFIX', 'chk_');    // Prefix to use infront of tablename and views

// -------------------------------------------------------------------------------------------
//
// Define the names for the database (tables, views, procedures, functions, triggers)
//
define('DBT_User',          DB_PREFIX . 'User');
define('DBT_Group', 		DB_PREFIX . 'Group');
define('DBT_GroupMember',	DB_PREFIX . 'GroupMember');
define('DBT_Note',      	DB_PREFIX . 'Note');
define('DBT_NoteList',      DB_PREFIX . 'NoteList');

define('DBUDF_FCheckUserIsOwnerOrAdminOfSida',     DB_PREFIX . 'FCheckUserIsOwnerOrAdminOfSida');
define('DBUDF_CheckUserIsAdmin',	           DB_PREFIX . 'FCheckUserIsAdmin');

// Stored routines concerning user
define('DBSP_AuthenticateUser',             DB_PREFIX . 'PAuthenticateUser');
define('DBSP_CreateUser',                   DB_PREFIX . 'PCreateUser');
define('DBSP_GetUserDetails',               DB_PREFIX . 'PGetUserDetails');
define('DBSP_SetUserDetails',               DB_PREFIX . 'PSetUserDetails');
define('DBSP_SetUserPassword',              DB_PREFIX . 'PSetUserPassword');
define('DBSP_SetUserEmail',                 DB_PREFIX . 'PSetUserEmail');
define('DBSP_UpdateLastLogin',              DB_PREFIX . 'PUpdateLastLogin');
define('DBUDF_FCheckUserIsOwnerOrAdmin',    DB_PREFIX . 'FCheckUserIsOwnerOrAdmin');
define('DBSP_SetUserNameAndEmail',          DB_PREFIX . 'PSetUserNameAndEmail');
define('DBSP_CreateUserAccountOrEmail',     DB_PREFIX . 'PCreateUserAccountOrEmail');
define('DBSP_DeleteUser',                   DB_PREFIX . 'PDeleteUser');

// Stored routines concerning notes
define('DBSP_CreateNote',                   DB_PREFIX . 'PCreateNote');
define('DBSP_DeleteNote',                   DB_PREFIX . 'PDeleteNote');
define('DBSP_CheckUncheckNote',             DB_PREFIX . 'PCheckUncheckNote');

define('DBSP_CreateNoteList',               DB_PREFIX . 'PCreateNoteList');
define('DBUDF_DeleteNoteList',              DB_PREFIX . 'FDeleteNoteList');
define('DBUDF_NumberOfNotesInNoteList',     DB_PREFIX . 'FNumberOfNotesInNoteList');
define('DBSP_UpdateNoteList',               DB_PREFIX . 'PUpdateNoteList');

?>