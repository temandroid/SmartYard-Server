create table frs_faces
(
    face_id   integer
        constraint frs_faces_pk
            primary key,
    face_uuid character varying
);

create unique index frs_faces_face_uuid_uindex
    on frs_faces (face_uuid);


create table frs_links_faces
(
    flat_id             integer not null,
    house_subscriber_id integer not null,
    face_id             integer not null
);

create unique index frs_links_faces_main_uindex
    on frs_links_faces (flat_id, house_subscriber_id, face_id);

create index frs_links_faces_face_id_index
    on frs_links_faces (face_id);
