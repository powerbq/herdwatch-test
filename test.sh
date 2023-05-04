#!/bin/sh

cd $(dirname $0)

docker-compose exec server php bin/console dbal:run-sql 'SET foreign_key_checks = 0; TRUNCATE TABLE `user`; TRUNCATE TABLE `group`;'

I=0
J=0
for _ in $(seq 5)
do
	I=$((I+1))
	docker-compose exec client php bin/console app:create-group "Group $I"

	for _ in $(seq 5)
	do
		J=$((J+1))
		docker-compose exec client php bin/console app:create-user "User $J" "user$J@example.com" $I
	done
done

docker-compose exec client php bin/console app:report

echo Press enter to continue
read

docker-compose exec client php bin/console app:edit-group    3 "Edited Group 3"
docker-compose exec client php bin/console app:edit-user     3 "Edited User 3"
docker-compose exec client php bin/console app:edit-user    13 "Edited User 13" "editeduser$J@example.com"
docker-compose exec client php bin/console app:edit-user    23 "Edited User 23" "editeduser$J@example.com" 1
docker-compose exec client php bin/console app:delete-group  5
docker-compose exec client php bin/console app:delete-user   5

docker-compose exec client php bin/console app:report

echo Press enter to continue
read

docker-compose exec client php bin/console app:report --group-id=2

echo Press enter to continue
read
