
begin;

CREATE TABLE hw_race_participant_table (
	race_participant_id serial NOT NULL PRIMARY KEY,
	race_id integer REFERENCES hw_race_table(race_id),
	participant_id integer REFERENCES hw_participant_table(participant_id)
);


-- create a temporary table that has only unique participants
CREATE TABLE _uniq (
    participant_id integer unique,
    uid integer NOT NULL REFERENCES cs_authentication_table(uid)
);
INSERT INTO _uniq (participant_id, uid) SELECT DISTINCT ON (uid) participant_id, uid FROM hw_participant_table;




-- fix the race participant table so it uses the proper participant id
INSERT INTO hw_race_participant_table (race_id, participant_id) SELECT race_id, (select participant_id FROM _uniq WHERE uid=p.uid) as participant_id FROM hw_participant_table as p;
DELETE FROM hw_participant_table WHERE participant_id NOT IN (select participant_id FROM _uniq);
DROP TABLE _uniq;
CREATE UNIQUE INDEX hw_race_participant_table_race_id_participant_id_uidx ON hw_race_participant_table (race_id, participant_id);


-- fix the participant table.
ALTER TABLE hw_participant_table DROP COLUMN race_id;
ALTER TABLE hw_participant_table ADD COLUMN display_name text;
UPDATE hw_participant_table as p set display_name=a.username FROM cs_authentication_table as a WHERE a.uid=p.uid;


-- fix the CSV table...
ALTER TABLE hw_csv_table ADD COLUMN participant_id integer REFERENCES hw_participant_table(participant_id);
UPDATE hw_csv_table as x SET participant_id=(select participant_id from hw_participant_table WHERE uid=x.uid);
ALTER TABLE hw_csv_table DROP COLUMN uid;
ALTER TABLE hw_csv_table ALTER COLUMN participant_id SET NOT NULL;


-- fix the data table...
ALTER TABLE hw_data_table ADD COLUMN participant_id integer REFERENCES hw_participant_table(participant_id);
UPDATE hw_data_table as x SET participant_id=(select participant_id FROM hw_participant_table WHERE uid=x.uid);
SELECT a.* FROM hw_data_table AS a INNER JOIN hw_participant_table AS x USING (participant_id) WHERE a.uid <> x.uid;
ALTER TABLE hw_data_table DROP COLUMN uid;
ALTER TABLE hw_data_table ALTER COLUMN participant_id SET NOT NULL;


-- fix the race table
ALTER TABLE hw_race_table ADD COLUMN creator_participant_id integer REFERENCES hw_participant_table(participant_id);
UPDATE hw_race_table AS x SET creator_participant_id=(select participant_id FROM hw_participant_table WHERE uid=x.creator_uid);
SELECT r.* FROM hw_race_table AS r INNER JOIN hw_participant_table AS p ON (r.creator_participant_id=p.participant_id) WHERE p.uid <> r.creator_uid;

ALTER TABLE hw_race_table DROP COLUMN creator_uid;
ALTER TABLE hw_race_table ALTER COLUMN creator_participant_id SET NOT NULL;

-- abort;