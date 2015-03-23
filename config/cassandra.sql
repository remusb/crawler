CREATE KEYSPACE apps
WITH REPLICATION = { 'class' : 'SimpleStrategy', 'replication_factor' : 1 };

CREATE TABLE configuration (
  env varchar,
  group varchar,
  config map<varchar, text>,
  array list<text>,
  PRIMARY KEY (env, group));

INSERT INTO configuration (env, group, array, config) VALUES 
('dev', 'redis', [], { 'host' : '127.0.0.1',
	'port' : '6379',
	'url_queue' : 'url_queue'});

INSERT INTO configuration (env, group, array, config) VALUES 
('dev', 'general', [], {});

INSERT INTO configuration (env, group, array, config) VALUES 
('dev', 'crawling', [], { 'frequency': '1440' });

INSERT INTO configuration (env, group, array, config) VALUES 
('dev', 'seed', ['http://www.pccommerce.ro/'], {});

CREATE TABLE domain (
  domain varchar,
  homepage varchar,
  PRIMARY KEY (domain));

CREATE TABLE url (
  url varchar,
  frequency int,
  PRIMARY KEY (url));