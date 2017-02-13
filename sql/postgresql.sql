SET TIME ZONE 'UTC';

--
-- Table structure for table users
--

CREATE TABLE IF NOT EXISTS users (
	id SERIAL,
	username text NOT NULL,
	email text NOT NULL,
	password text NOT NULL,
	name text NOT NULL,
	created text NOT NULL,
	attempt text NOT NULL DEFAULT '0',
	PRIMARY KEY (id)
);

--
-- Table structure for table user_tokens
--

CREATE TABLE IF NOT EXISTS user_tokens (
	token text NOT NULL, -- 'The Unique Token Generated'
	uid integer NOT NULL, -- 'The User Id'
	requested text NOT NULL -- 'The Date when token was created'
);

--
-- Table structure for table user_devices
--

CREATE TABLE IF NOT EXISTS user_devices (
	id SERIAL,
	uid int NOT NULL, -- The user's ID
	token character(15) NOT NULL, -- A unique token for the user's device
	last_access text NOT NULL,
	PRIMARY KEY (id)
);
