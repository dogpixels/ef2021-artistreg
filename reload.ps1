docker rm -f efo2021-artistreg-instance
docker build -t efo2021-artistreg .
docker run -dp 80:80 --name efo2021-artistreg-instance efo2021-artistreg