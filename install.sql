CREATE TABLE IF NOT EXISTS users (
  id SERIAL NOT NULL PRIMARY KEY,
  username text NOT NULL,
  password text NOT NULL,
  salt text NOT NULL,
  role text NOT NULL,
  skyflowtoken text NOT NULL,
  clientid text DEFAULT NULL,
  clientsecret text DEFAULT NULL,
  wave_client_id text DEFAULT NULL,
  wave_client_secret text DEFAULT NULL,
  wave_access_token text DEFAULT NULL,
  wave_refresh_token text DEFAULT NULL,
  wave_instance_url text DEFAULT NULL,
  wave_is_sandbox boolean DEFAULT NULL,
  salesforce_client_id text DEFAULT NULL,
  salesforce_client_secret text DEFAULT NULL,
  salesforce_access_token text DEFAULT NULL,
  salesforce_refresh_token text DEFAULT NULL,
  salesforce_instance_url text DEFAULT NULL,
  salesforce_is_sandbox boolean DEFAULT NULL
);

INSERT INTO users (
  id,
  username,
  password,
  salt,
  role,
  skyflowtoken,
  clientid,
  clientsecret,
  wave_client_id,
  wave_client_secret,
  wave_access_token,
  wave_refresh_token,
  wave_instance_url,
  wave_is_sandbox,
  salesforce_client_id,
  salesforce_client_secret,
  salesforce_access_token,
  salesforce_refresh_token,
  salesforce_instance_url,
  salesforce_is_sandbox,
  exact_target_client_id,
  exact_target_client_secret
) VALUES (
  1,
  'skyflow',
  'a1mvTX1PGa/tPegPfCiRHdc3PFLTb3GpTQi3QqBHVp3rBjt/rYQXuJCvU6K96WfPY9OGN+ClmveryL19r5Xg7Q==',
  '1c75f1db82ca11cff02043b',
  'ROLE_ADMIN',
  'trfKG56uBhu4WnBNLxc07tGaAkfx+PMLmAzhP6BO6ZpAz3D1Oy+MIBGTpkuP2+vLItCLeVepHp1XfrUGMCP8xQ==',
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL
);

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

CREATE TABLE IF NOT EXISTS query (
  id SERIAL NOT NULL PRIMARY KEY,
  query text NOT NULL,
  id_user int NOT NULL REFERENCES users (id)
);

CREATE TABLE IF NOT EXISTS wave_request (
  id SERIAL NOT NULL PRIMARY KEY,
  request text NOT NULL,
  id_user int NOT NULL REFERENCES users (id)
);