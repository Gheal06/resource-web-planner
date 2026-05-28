# docker run -it --rm --name test -v "$PWD":/tmp/resource-web-planner -w $wd /tmp/resource-web-planner php:8.4-cli test.php
# cd /tmp/tehnologii-web/
# docker run -it --rm --name my-running-script -v "$PWD":/tmp/tehnologii-web -w /tmp/tehnologii-web php:8.4-cli php hw.php

docker stop rwp-container &&
docker rm rwp-container 
# docker image prune
# docker stop postgres_container &&
# docker rm postgres_container
# docker stop pgadmin_container &&
# docker rm pgadmin_container
# docker rmi localhost/resource-web-planner

# cd $(pwd) &&
# docker run --network rwp-net --name postgres_container -e POSTGRES_PASSWORD=DB_Admin!7890 -d postgres &&
# docker run --network rwp-net --name pgadmin_container -p 5050:80 -e 'PGADMIN_DEFAULT_EMAIL=admin@rwp.com' -e 'PGADMIN_DEFAULT_PASSWORD=RWP_Admin!5050' -d dpage/pgadmin4 &&
docker build -t rwp . &&
docker run --network rwp-net --dns 1.1.1.1 --dns 8.8.8.8 -d --name rwp-container -p 8081:80 rwp
