CREATE TABLE IF NOT EXISTS resetTokens (
  token text NOT NULL, -- 'The Unique Token Generated'
  uid integer NOT NULL, -- 'The User Id'
  requested text NOT NULL -- 'The Date when token was created'
);