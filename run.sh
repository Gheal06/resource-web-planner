# docker run -it --rm --name test -v "$PWD":/tmp/resource-web-planner -w $wd /tmp/resource-web-planner php:8.4-cli test.php
# cd /tmp/tehnologii-web/
# docker run -it --rm --name my-running-script -v "$PWD":/tmp/tehnologii-web -w /tmp/tehnologii-web php:8.4-cli php hw.php

docker stop resource-web-planner &&
docker rm resource-web-planner
docker stop postgres_container &&
docker rm postgres_container
docker stop pgadmin_container &&
docker rm pgadmin_container
# docker rmi localhost/resource-web-planner

# cd $(pwd) &&
docker run --network rwp-net --name postgres_container -e POSTGRES_PASSWORD=DB_Admin!7890 -d postgres &&
docker run --network rwp-net --name pgadmin_container -p 5050:80 -e 'PGADMIN_DEFAULT_EMAIL=admin@rwp.com' -e 'PGADMIN_DEFAULT_PASSWORD=RWP_Admin!5050' -d dpage/pgadmin4 &&
docker build -t resource-web-planner . &&
docker run --network rwp-net -d -p 8081:80 --name resource-web-planner resource-web-planner
