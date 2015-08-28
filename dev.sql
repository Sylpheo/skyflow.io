--
-- This is for development only.
--

CREATE ROLE _www;
CREATE USER skyflow PASSWORD 'skyflow' ROLE _www;
CREATE DATABASE skyflow OWNER skyflow;
GRANT ALL PRIVILEGES ON DATABASE skyflow TO skyflow;

-- ROLLBACK --

-- DROP DATABASE skyflow;
-- DROP USER skyflow;
-- DROP ROLE _www;