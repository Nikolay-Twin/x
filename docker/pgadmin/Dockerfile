FROM dpage/pgadmin4

ARG PGADMIN_DEFAULT_FOLDER=admin_admin.ad

RUN mkdir -p "/var/lib/pgadmin/storage/$PGADMIN_DEFAULT_FOLDER"
COPY servers.json "/var/lib/pgadmin/storage/$PGADMIN_DEFAULT_FOLDER"
