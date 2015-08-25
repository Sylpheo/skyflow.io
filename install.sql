-- --
-- -- This is for development only.
-- -- This is for MySQL, not tested for PostgreSQL.
-- -- The rest of the file is PostgreSQL syntax.
-- -- --------------------
--
-- CREATE DATABASE IF NOT EXISTS skyflow;
--
-- GRANT ALL ON skyflow.* to 'skyflow'@'localhost' IDENTIFIED BY 'skyflow';
--
-- --
-- -- Database: exacttarget
-- --
--
-- USE 'skyflow';
--
-- -- --------------------

CREATE TABLE IF NOT EXISTS users (
  id SERIAL NOT NULL PRIMARY KEY,
  username text NOT NULL,
  password text NOT NULL,
  salt text NOT NULL,
  role text NOT NULL,
  clientid text DEFAULT NULL,
  clientsecret text DEFAULT NULL,
  waveid text DEFAULT NULL,
  wavesecret text DEFAULT NULL,
  wavelogin text DEFAULT NULL,
  wavepassword text DEFAULT NULL,
  skyflowtoken text NOT NULL,
  access_token_salesforce text DEFAULT NULL,
  refresh_token_salesforce text DEFAULT NULL,
  instance_url_salesforce text DEFAULT NULL,
  salesforce_id text DEFAULT NULL,
  salesforce_secret text DEFAULT NULL
);

INSERT INTO users (id, username, password, salt, role, clientid, clientsecret, waveid, wavesecret, wavelogin, wavepassword, skyflowtoken, access_token_salesforce, refresh_token_salesforce, instance_url_salesforce, salesforce_id, salesforce_secret) VALUES
(1, 'skyflow', 'a1mvTX1PGa/tPegPfCiRHdc3PFLTb3GpTQi3QqBHVp3rBjt/rYQXuJCvU6K96WfPY9OGN+ClmveryL19r5Xg7Q==', '1c75f1db82ca11cff02043b', 'ROLE_ADMIN', NULL, NULL, NULL, NULL, NULL, NULL, '75118235125828845286db3fb91bc1a9', NULL, NULL, NULL, NULL, NULL);

ALTER SEQUENCE users_id_seq START WITH 2;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS event (
  id SERIAL NOT NULL PRIMARY KEY,
  name text NOT NULL,
  description text NOT NULL,
  id_user int NOT NULL REFERENCES users (id)
);

INSERT INTO event (id, name, description, id_user) VALUES
(12, 'Modificiation', 'test', 1),
(13, 'nom', 'nom', 1),
(14, 'remerciements', 'remerciements', 1);

ALTER SEQUENCE event_id_seq START WITH 15;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS flow (
  id SERIAL NOT NULL PRIMARY KEY,
  name text NOT NULL,
  class text NOT NULL,
  documentation text NOT NULL,
  id_user int NOT NULL REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO flow (id, name, class, documentation, id_user) VALUES
(1, 'FLoooow', 'test', '<p>Modificaion</p>', 1),
(2, 'Flow_nom', 'test2', '<p><strong><span style="background-color:#FF0000">Documentation du Flow_Test2 ! </span></strong></p>', 1),
(3, 'Mail_remerciement', 'mail_remerciements', '<p>Flow d&#39;envoi d&#39;un mail de remerciement pour la participation + wave</p>', 1),
(4, 'testModification', 'test', '<p>Modification de la documentation !!!</p>', 1);

ALTER SEQUENCE flow_id_seq START WITH 5;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS mapping (
  id SERIAL NOT NULL PRIMARY KEY,
  id_event int NOT NULL REFERENCES event (id) ON DELETE CASCADE ON UPDATE CASCADE,
  id_flow int NOT NULL REFERENCES flow (id) ON DELETE CASCADE ON UPDATE CASCADE,
  id_user int NOT NULL REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE (id_event, id_flow, id_user)
);

INSERT INTO mapping (id, id_event, id_flow, id_user) VALUES
(6, 12, 1, 1),
(3, 13, 2, 1),
(4, 14, 3, 1);

ALTER SEQUENCE mapping_id_seq START WITH 7;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS wave_request (
  id SERIAL NOT NULL PRIMARY KEY,
  request text NOT NULL,
  id_user int NOT NULL REFERENCES users (id)
);

INSERT INTO wave_request (id, request, id_user) VALUES
(1, 'q = load "0FbB00000005KPEKA2/0FcB00000005W4tKAE";q = filter q by ''Email'' in ["e.lodie62@hotmail.fr"];q = foreach q generate ''FirstName'' as ''FirstName'',''LastName'' as ''LastName'';', 1);

ALTER SEQUENCE wave_request_id_seq START WITH 2;