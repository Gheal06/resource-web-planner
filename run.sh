# docker run -it --rm --name test -v "$PWD":/tmp/resource-web-planner -w $wd /tmp/resource-web-planner php:8.4-cli test.php
# cd /tmp/tehnologii-web/
# docker run -it --rm --name my-running-script -v "$PWD":/tmp/tehnologii-web -w /tmp/tehnologii-web php:8.4-cli php hw.php

docker stop resource-web-planner
docker rm resource-web-planner
docker rmi localhost/resource-web-planner

# cd $(pwd) &&
docker build -t resource-web-planner . &&
docker run -d -p 8081:80 --name resource-web-planner resource-web-planner
