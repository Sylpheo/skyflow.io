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

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS flow (
  id SERIAL NOT NULL PRIMARY KEY,
  name text NOT NULL,
  class text NOT NULL,
  documentation text NOT NULL,
  id_user int NOT NULL REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS mapping (
  id SERIAL NOT NULL PRIMARY KEY,
  id_event int NOT NULL REFERENCES event (id) ON DELETE CASCADE ON UPDATE CASCADE,
  id_flow int NOT NULL REFERENCES flow (id) ON DELETE CASCADE ON UPDATE CASCADE,
  id_user int NOT NULL REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE (id_event, id_flow, id_user)
);

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS wave_request (
  id SERIAL NOT NULL PRIMARY KEY,
  request text NOT NULL,
  id_user int NOT NULL REFERENCES users (id)
);