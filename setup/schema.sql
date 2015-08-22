
begin;

-- TODO: "start_date" would allow a person to enter a race late, vs retroactively (based on race start date).
CREATE TABLE hw_participant_table (
    participant_id serial NOT NULL PRIMARY KEY,
    uid integer NOT NULL REFERENCES cs_authentication_table(uid),
	display_name text NOT NULL,
	start_date date NOT NULL DEFAULT NOW()::date
);
CREATE UNIQUE INDEX hw_participant_table_participant_id_uid_uid_uidx ON hw_participant_table (participant_id, uid);


CREATE TABLE hw_csv_table (
    csv_id serial NOT NULL PRIMARY KEY,
	participant_id integer NOT NULL REFERENCES hw_participant_table(participant_id),
    data_source text NOT NULL,
    date_column integer NOT NULL default 0,
    steps_column integer NOT NULL default 1,
    distance_column integer NOT NULL default 2,
    date_format varchar(10) NOT NULL default 'm/d/Y',
    distance_unit varchar(2) NOT NULL default 'mi',
    data_starts_at_row integer NOT NULL default 0,
    created timestamptz NOT NULL DEFAULT NOW()
);

CREATE TABLE hw_data_table (
    data_id serial NOT NULL PRIMARY KEY,
	participant_id integer NOT NULL REFERENCES hw_participant_table(participant_id),
    entry_date date NOT NULL DEFAULT NOW()::date,
    steps integer NOT NULL default 0,
    mileage decimal(5,2) NOT NULL DEFAULT 0.0
);
create UNIQUE INDEX hw_data_table_participant_id_entry_date_uidx ON hw_data_table (participant_id, entry_date);

CREATE TABLE hw_map_table (
    map_id serial NOT NULL PRIMARY KEY,
    map_name varchar(100) NOT NULL,
    map_description text NOT NULL
);

-- NOTE:: one possibility for a milestone was "climbing everest" (which doesn't work using distance)
CREATE TABLE hw_map_milestone_table (
    map_milestone_id serial NOT NULL PRIMARY KEY,
    map_id integer NOT NULL REFERENCES hw_map_table(map_id),
    milestone_name varchar(100) NOT NULL,
    milestone_description text,
    milestone_distance decimal(10,2) NOT NULL
);

CREATE TABLE hw_race_table (
    race_id serial NOT NULL PRIMARY KEY,
	race_name text NOT NULL UNIQUE,
    map_id integer REFERENCES hw_map_table(map_id),
    race_start_date date NOT NULL DEFAULT NOW()::date,
	race_end_date date,
	creator_id integer NOT NULL REFERENCES hw_participant_table(participant_id),
	is_ended boolean NOT NULL DEFAULT FALSE,
	is_deleted boolean NOT NULL DEFAULT FALSE
);

CREATE TABLE hw_race_participant_table (
	race_participant_id serial NOT NULL PRIMARY KEY,
	race_id integer REFERENCES hw_race_table(race_id),
	participant_id integer REFERENCES hw_participant_table(participant_id)
);
CREATE UNIQUE INDEX hw_race_participant_table_race_id_participant_id_uidx ON hw_race_participant_table (race_id, participant_id);
