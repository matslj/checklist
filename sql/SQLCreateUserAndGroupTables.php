<?php
// ===========================================================================================
//
// SQLCreateUserAndGroupTables.php
//
// SQL statements to create the tables for the User and group tables.
//
// WARNING: Do not forget to check input variables for SQL injections.
//
// Author: Mats Ljungquist
//

$imageLink = WS_IMAGES;

// Get the tablenames
$tNote           = DBT_Note;
$tNoteList       = DBT_NoteList;
$tUser 		 = DBT_User;
$tGroup 	 = DBT_Group;
$tGroupMember 	 = DBT_GroupMember;

// Get the SP/UDF/trigger names
$spAuthenticateUser            = DBSP_AuthenticateUser;
$spCreateUser                  = DBSP_CreateUser;
$spGetUserDetails              = DBSP_GetUserDetails;
$spSetUserDetails              = DBSP_SetUserDetails;
$spSetUserPassword             = DBSP_SetUserPassword;
$spSetUserEmail                = DBSP_SetUserEmail;
$spUpdateLastLogin             = DBSP_UpdateLastLogin;
$spSetUserNameAndEmail         = DBSP_SetUserNameAndEmail;
$spCreateUserAccountOrEmail    = DBSP_CreateUserAccountOrEmail;
$spDeleteUser                  = DBSP_DeleteUser;

$spCreateNote                  = DBSP_CreateNote;
$spDeleteNote                  = DBSP_DeleteNote;
$spCheckUncheckNote            = DBSP_CheckUncheckNote;

$spCreateNoteList              = DBSP_CreateNoteList;
$udfDeleteNoteList             = DBUDF_DeleteNoteList;
$udfNumberOfNotesInNoteList    = DBUDF_NumberOfNotesInNoteList;
$spUpdateNoteList              = DBSP_UpdateNoteList;

$fCheckUserIsAdmin              = DBUDF_CheckUserIsAdmin;

// Create the query
$query = <<<EOD
DROP TABLE IF EXISTS {$tNote};
DROP TABLE IF EXISTS {$tNoteList};
DROP TABLE IF EXISTS {$tGroupMember};
DROP TABLE IF EXISTS {$tUser};
DROP TABLE IF EXISTS {$tGroup};

--
-- Table for the User
--
CREATE TABLE {$tUser} (

  -- Primary key(s)
  idUser INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

  -- Attributes
  accountUser CHAR(20) NULL UNIQUE,
  nameUser CHAR(100),
  emailUser CHAR(100) NULL UNIQUE,
  lastLoginUser DATETIME NOT NULL,
  passwordUser CHAR(32) NOT NULL,
  deletedUser BOOL NOT NULL,
  activeUser BOOL NOT NULL
  
);

--
-- Table for the Group
--
CREATE TABLE {$tGroup} (

  -- Primary key(s)
  idGroup CHAR(3) NOT NULL PRIMARY KEY,

  -- Attributes
  nameGroup CHAR(40) NOT NULL
);


--
-- Table for the GroupMember
--
CREATE TABLE {$tGroupMember} (

  -- Primary key(s)
  --
  -- The PK is the combination of the two foreign keys, see below.
  --

  -- Foreign keys
  GroupMember_idUser INT NOT NULL,
  GroupMember_idGroup CHAR(3) NOT NULL,

  FOREIGN KEY (GroupMember_idUser) REFERENCES {$tUser}(idUser),
  FOREIGN KEY (GroupMember_idGroup) REFERENCES {$tGroup}(idGroup),

  PRIMARY KEY (GroupMember_idUser, GroupMember_idGroup)

  -- Attributes

);

--
-- Table for note list
--
CREATE TABLE {$tNoteList} (

  -- Primary key(s)
  idNoteList INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

  -- Attributes
  titleNoteList VARCHAR(256) NOT NULL,
  descriptionNoteList VARCHAR(256) NOT NULL,
  created DATETIME NOT NULL,
  isDefaultNoteList BOOLEAN NOT NULL
);  

--
-- Table for notes
--
CREATE TABLE {$tNote} (

  -- Primary key(s)
  idNote INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

  -- Attributes
  textNote VARCHAR(256) NOT NULL,
  tagNote VARCHAR(256) NOT NULL,
  dateNote DATETIME NOT NULL,
  checkedNote BOOL NOT NULL,
  Note_idNoteList INT NOT NULL,
  FOREIGN KEY (Note_idNoteList) REFERENCES {$tNoteList}(idNoteList)
);

--
-- SP to delete a note
--
DROP PROCEDURE IF EXISTS {$spDeleteNote};
CREATE PROCEDURE {$spDeleteNote}
(
    IN anIdNote INT
)
BEGIN    
    DELETE FROM {$tNote}
    WHERE
        idNote = anIdNote;
END;
    
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
-- 
-- SP to delete a NoteList
-- 
DROP FUNCTION IF EXISTS {$udfDeleteNoteList};
CREATE FUNCTION {$udfDeleteNoteList}
(
    aNoteListId INT UNSIGNED
)
RETURNS TINYINT UNSIGNED
DETERMINISTIC
wrap: BEGIN
    DECLARE i INT UNSIGNED;
    -- Check permissions
    SELECT {$udfNumberOfNotesInNoteList}(aNoteListId) INTO i;
    -- If the return value from the udf is greater than zero, then the NoteList is not empty and may not be deleted.
    IF i>0 THEN
        RETURN 1;
    END IF;
    DELETE FROM {$tNoteList} WHERE idNoteList = aNoteListId;
    -- Delete ok
    RETURN 0;
END wrap;

-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
-- 
-- Function which returns the number of Notes in a NoteList.
-- 
-- Return values:
-- 0 if NoteList is empty
-- n where n is the number of Notes in the NoteList
-- 
DROP FUNCTION IF EXISTS {$udfNumberOfNotesInNoteList};
CREATE FUNCTION {$udfNumberOfNotesInNoteList}
(
    aNoteListId INT UNSIGNED
)
RETURNS INT UNSIGNED
READS SQL DATA
BEGIN
    DECLARE i INT UNSIGNED;
    SELECT COUNT(idNote) INTO i FROM {$tNote}
        WHERE
        Note_idNoteList = aNoteListId;
    IF i IS NULL THEN
        RETURN 0;
    END IF;
        RETURN i;
END; 
    
--
-- SP to check/uncheck note
--
DROP PROCEDURE IF EXISTS {$spCheckUncheckNote};
CREATE PROCEDURE {$spCheckUncheckNote}
(
        IN anIdNote INT,
        IN aCheckedNote BOOL
)
BEGIN
        UPDATE {$tNote} SET
                checkedNote = aCheckedNote
        WHERE
                idNote = anIdNote
        LIMIT 1;
END;
        
--
-- SP to check/uncheck note
--
DROP PROCEDURE IF EXISTS {$spUpdateNoteList};
CREATE PROCEDURE {$spUpdateNoteList}
(
        IN anIdNoteList INT,
        IN aTitleNoteList VARCHAR(256),
        IN aDescriptionNoteList VARCHAR(256),
        IN aIsDefaultNoteList BOOLEAN
)
BEGIN
        UPDATE {$tNoteList} SET
                titleNoteList = aTitleNoteList,
                descriptionNoteList = aDescriptionNoteList,
                isDefaultNoteList = aIsDefaultNoteList
        WHERE
                idNoteList = anIdNoteList
        LIMIT 1;
END;
   
--
-- SP to create a new note
--
DROP PROCEDURE IF EXISTS {$spCreateNote};
CREATE PROCEDURE {$spCreateNote}
(
	IN aTextNote VARCHAR(256),
	IN aTagNote VARCHAR(256),
        IN aNoteListId INT
)
BEGIN
        INSERT INTO {$tNote}
                (textNote, tagNote, dateNote, checkedNote, Note_idNoteList)
                VALUES
                (aTextNote, aTagNote, NOW(), FALSE, aNoteListId);
END;
        
--
-- SP to create a new note
--
DROP PROCEDURE IF EXISTS {$spCreateNoteList};
CREATE PROCEDURE {$spCreateNoteList}
(
	IN aTitleNoteList VARCHAR(256),
        IN aDescriptionNoteList VARCHAR(256),
        OUT aListId INT
)
BEGIN
        INSERT INTO {$tNoteList}
                (titleNoteList, descriptionNoteList, created, isDefaultNoteList)
                VALUES
                (aTitleNoteList, aDescriptionNoteList, NOW(), false);
        
        SET aListId = LAST_INSERT_ID();
END;

--
-- SP to create a new user
--
DROP PROCEDURE IF EXISTS {$spCreateUser};
CREATE PROCEDURE {$spCreateUser}
(
	IN anAccountUser CHAR(20),
	IN aPassword CHAR(32)
)
BEGIN
        INSERT INTO {$tUser}
                (accountUser, passwordUser, lastLoginUser, deletedUser)
                VALUES
                (anAccountUser, md5(aPassword), NOW(), FALSE);
        INSERT INTO {$tGroupMember} (GroupMember_idUser, GroupMember_idGroup)
	VALUES (LAST_INSERT_ID(), 'usr');
        CALL {$spAuthenticateUser}(anAccountUser,aPassword);
END;
   
--
-- SP to create a new user based on either account name or email
--
DROP PROCEDURE IF EXISTS {$spCreateUserAccountOrEmail};
CREATE PROCEDURE {$spCreateUserAccountOrEmail}
(
	IN anAccountUser CHAR(20),
        IN aNameUser CHAR(100),
        IN anEmailUser CHAR(100),
	IN aPassword CHAR(32)
)
BEGIN
    DECLARE authAttribute CHAR(100);
    IF anEmailUser = '' THEN
        BEGIN
            SET authAttribute = anAccountUser;
        END;
    ELSE
        BEGIN
            SET authAttribute = anEmailUser;
        END;
    END IF;
    INSERT INTO {$tUser}
            (accountUser, emailUser, nameUser, passwordUser, lastLoginUser, deletedUser)
            VALUES
            (anAccountUser, anEmailUser, aNameUser, md5(aPassword), NOW(), FALSE);
    INSERT INTO {$tGroupMember} (GroupMember_idUser, GroupMember_idGroup)
    VALUES (LAST_INSERT_ID(), 'usr');
    CALL {$spAuthenticateUser}(authAttribute,aPassword);
END;

--
-- SP to authenticate a user
--
DROP PROCEDURE IF EXISTS {$spAuthenticateUser};
CREATE PROCEDURE {$spAuthenticateUser}
(
	IN anAccountUserOrEmail CHAR(100),
	IN aPassword CHAR(32)
)
BEGIN
	SELECT
	idUser AS id,
	accountUser AS account,
        nameUser AS name,
        emailUser AS email,
	GroupMember_idGroup AS groupid
FROM {$tUser} AS U
	INNER JOIN {$tGroupMember} AS GM
		ON U.idUser = GM.GroupMember_idUser
WHERE
        (
	accountUser	= anAccountUserOrEmail AND
	passwordUser 	= md5(aPassword)
        )
        OR
        (
	emailUser	= anAccountUserOrEmail AND
	passwordUser 	= md5(aPassword)
        )
;
END;
        
--
-- SP to get user details
--
DROP PROCEDURE IF EXISTS {$spGetUserDetails};
CREATE PROCEDURE {$spGetUserDetails}
(
	IN anIdUser INT
)
BEGIN
	SELECT
	idUser AS id,
	accountUser AS account,
        nameUser AS name,
        emailUser AS email,
	GroupMember_idGroup AS groupid,
        nameGroup AS groupname
FROM {$tUser} AS U
	INNER JOIN {$tGroupMember} AS GM
		ON U.idUser = GM.GroupMember_idUser
        INNER JOIN {$tGroup} AS G
                ON GM.GroupMember_idGroup = G.idGroup
WHERE
	idUser = anIdUser
;
END;
      
--
-- SP to set user details
--
DROP PROCEDURE IF EXISTS {$spSetUserPassword};
CREATE PROCEDURE {$spSetUserPassword}
(
        IN anIdUser INT,
        IN aPassword CHAR(32)
)
BEGIN
        UPDATE {$tUser} SET
                passwordUser = md5(aPassword)
        WHERE
                idUser = anIdUser
        LIMIT 1;
END;
 
--
-- SP to set user details
--
DROP PROCEDURE IF EXISTS {$spSetUserNameAndEmail};
CREATE PROCEDURE {$spSetUserNameAndEmail}
(
        IN anIdUser INT,
        IN anAccountUser CHAR(20),
        IN aNameUser CHAR(100),
        IN anEmailUser CHAR(100)
)
BEGIN
        UPDATE {$tUser} SET
                accountUser = anAccountUser,
                nameUser = aNameUser,
                emailUser = anEmailUser
        WHERE
                idUser = anIdUser
        LIMIT 1;
END;      
   
--
-- SP to set user details
--
DROP PROCEDURE IF EXISTS {$spSetUserEmail};
CREATE PROCEDURE {$spSetUserEmail}
(
        IN anIdUser INT,
        IN anEmailUser CHAR(100)
)
BEGIN
        UPDATE {$tUser} SET
                emailUser = anEmailUser
        WHERE
                idUser = anIdUser
        LIMIT 1;
END;

--
-- SP to set user details
--
DROP PROCEDURE IF EXISTS {$spUpdateLastLogin};
CREATE PROCEDURE {$spUpdateLastLogin}
(
        IN anIdUser INT
)
BEGIN
        UPDATE {$tUser} SET
                lastLoginUser = NOW()
        WHERE
                idUser = anIdUser
        LIMIT 1;
END;
      
--
-- SP to set user details
--
DROP PROCEDURE IF EXISTS {$spSetUserDetails};
CREATE PROCEDURE {$spSetUserDetails}
(
        IN anIdUser INT,
        IN aNameUser CHAR(100),
        IN anEmailUser CHAR(100),
        IN aPassword CHAR(32),
        IN anActiveUser BOOL
)
BEGIN
        UPDATE {$tUser} SET
                nameUser = aNameUser,
                emailUser = anEmailUser,
                passwordUser = md5(aPassword),
                activeUser = anActiveUser
        WHERE
                idUser = anIdUser
        LIMIT 1;
END;
        
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
--  Create UDF that checks if user is member of group adm.
--
DROP FUNCTION IF EXISTS {$fCheckUserIsAdmin};
CREATE FUNCTION {$fCheckUserIsAdmin}
(
	aUserId INT
)
RETURNS BOOLEAN
READS SQL DATA
BEGIN
	DECLARE isAdmin INT;
	
	SELECT idUser INTO isAdmin
	FROM {$tUser} AS U
		INNER JOIN {$tGroupMember} AS GM
			ON U.idUser = GM.GroupMember_idUser
		INNER JOIN {$tGroup} AS G
			ON G.idGroup = GM.GroupMember_idGroup
	WHERE
		idGroup = 'adm' AND
		idUser = aUserId;
		
	RETURN (isAdmin OR 0);		
END;
        
--
-- Add default user(s)
--
INSERT INTO {$tUser} (accountUser, emailUser, nameUser, lastLoginUser, passwordUser,  activeUser, deletedUser)
VALUES ('admin', 'admin@noreply.se', 'Mr Admin', NOW(), md5('hemligt'), FALSE, FALSE);
INSERT INTO {$tUser} (accountUser, emailUser, nameUser, lastLoginUser, passwordUser, activeUser, deletedUser)
VALUES ('mats', 'mats@noreply.se', 'Mats Ljungquist', NOW(), md5('stugan2015'), TRUE, FALSE);
INSERT INTO {$tUser} (accountUser, emailUser, nameUser, lastLoginUser, passwordUser, activeUser, deletedUser)
VALUES ('disa', 'disa@noreply.se', 'Disa Holmlander', NOW(), md5('stugan2015'), TRUE, FALSE);
    
--
-- Add default groups
--
INSERT INTO {$tGroup} (idGroup, nameGroup) VALUES ('adm', 'Administrators of the site');
INSERT INTO {$tGroup} (idGroup, nameGroup) VALUES ('usr', 'Regular users of the site');

--
-- Add default groupmembers
--
INSERT INTO {$tGroupMember} (GroupMember_idUser, GroupMember_idGroup)
	VALUES ((SELECT idUser FROM {$tUser} WHERE accountUser = 'admin'), 'adm');
INSERT INTO {$tGroupMember} (GroupMember_idUser, GroupMember_idGroup)
	VALUES ((SELECT idUser FROM {$tUser} WHERE accountUser = 'disa'), 'usr');
INSERT INTO {$tGroupMember} (GroupMember_idUser, GroupMember_idGroup)
	VALUES ((SELECT idUser FROM {$tUser} WHERE accountUser = 'mats'), 'usr');

EOD;

?>