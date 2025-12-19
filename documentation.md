Projectdocumentatie: Full-Stack Webapplicatie
1. Data Flow Design
Dit diagram beschrijft de stroom van data en verzoeken tussen de verschillende componenten van het project.

Gebruikersinteractie:
Een gebruiker navigeert met een webbrowser via HTTP naar de webapplicatie, die wordt geserveerd door de PHP/Apache Docker container op poort 8085.

Voor interactieve elementen (zoals de guestbook en de systeemstatistieken) stuurt de JavaScript-code op de frontend asynchrone API-verzoeken (AJAX) naar PHP-scripts (api.php, stats-api.php) binnen dezelfde container.

Applicatie-Database Flow:
Het api.php script ontvangt een verzoek en gebruikt de PHP PDO-driver om een TCP-verbinding op te zetten met de MySQL Docker container via het interne Docker-netwerk op poort 3306.

Vervolgens worden SQL-queries (SELECT om data te lezen, INSERT om data te schrijven) naar de database gestuurd.

Metrics Collection Flow:
De cAdvisor container heeft toegang tot de Docker-socket van de host en verzamelt continu live metrics (CPU, geheugen, etc.) van alle andere actieve containers.

cAdvisor stelt deze metrics beschikbaar via een intern HTTP-endpoint (/metrics).

De Prometheus container is geconfigureerd om periodiek dit endpoint van de cAdvisor-container te "scrapen" (lezen), en slaat de data op in zijn time-series database.

Metrics Visualisatie Flow:
De gebruiker opent de webinterface van de Grafana container in zijn browser via poort 3000.

Grafana, waarin Prometheus als databron is toegevoegd, stuurt PromQL-queries naar de Prometheus container (op poort 9090) om de opgeslagen metrics op te vragen.

Grafana rendert deze data in de grafieken en panelen van het geconfigureerde dashboard.

2. Technische Componenten & Code
2.1 Jenkinsfile
Het Jenkinsfile definieert de CI/CD-pipeline via "Pipeline-as-Code".

Stages:

Build: Bouwt de custom Docker-image voor de PHP-applicatie met het docker compose build commando.

Start Services: Start alle applicatie- en monitoringservices in detached modus met docker compose up -d.

Show running containers: Voert docker ps uit als snelle verificatie in de Jenkins-log.

Post-sectie: Bevat een echo statement om te voorkomen dat de services na de pipeline-run worden afgesloten, zodat de website live blijft.

2.2 docker-compose.yml
Dit bestand orkestreert alle 5 de services:

php: De webapplicatie, gebouwd vanuit de lokale Dockerfile. De src map wordt gemount in /var/www/html.

mysql: De database, gebruikmakend van de officiële mysql:latest image. De init map wordt gemount in /docker-entrypoint-initdb.d om het schema aan te maken bij de eerste start. Data wordt persistent opgeslagen in het db-data volume.

prometheus: De monitoring-backend. Gebruikt de prom/prometheus image en laadt de configuratie vanuit de lokale prometheus.yml.

cadvisor: De metric-collector. Gebruikt de gcr.io/cadvisor/cadvisor image en krijgt read-only toegang tot de host-directories om container-informatie te verzamelen.

grafana: De visualisatie-frontend. Gebruikt de grafana/grafana image en slaat zijn configuratie op in het grafana_data volume.

2.3 Dockerfile
De Dockerfile definieert de custom image voor de php service:

Base Image: php:7.4-apache.

PHP Extensies: Installeert pdo en pdo_mysql, die essentieel zijn voor de databaseverbinding van de applicatie.

Systeempakketten: Installeert procps en coreutils, die de commando's top, free en df beschikbaar maken voor de systeemstatistieken-API.

3. Monitoring
Wat wordt gemonitord? De monitoring stack observeert de kern-prestatiemetrics voor elke container die in docker-compose.yml is gedefinieerd.

Belangrijkste Metrics:

CPU-gebruik: Het percentage CPU dat door elke container wordt verbruikt.

Geheugengebruik: De hoeveelheid RAM die elke container in beslag neemt.

Netwerk I/O: De hoeveelheid data die door elke container wordt verzonden en ontvangen.

Container Status: De gezondheid en status van de containers (actief, gestopt, herstartend).

Hoe het werkt:

cAdvisor verzamelt de data.

Prometheus slaat de data op.

Grafana visualiseert de data op een dashboard (geïmporteerd via ID 193), specifiek ontworpen voor Docker-containermonitoring.

4. API Queries
4.1 Guestbook API (api.php)
GET /api.php: Voert de volgende SQL-query uit om alle commentaren op te halen, gesorteerd op datum:

SQL

SELECT id, username, message, created_at FROM comments ORDER BY created_at DESC;
POST /api.php: Voert een "prepared SQL statement" uit om veilig een nieuw commentaar toe te voegen en SQL-injectie te voorkomen:

SQL

INSERT INTO comments (username, message) VALUES (:username, :message);
4.2 System Stats API (stats-api.php)
GET /stats-api.php: Deze API bevraagt niet de database, maar voert shell-commando's uit op het besturingssysteem van de container:

uptime: Om de uptime van de server te verkrijgen.

free -m: Om het geheugengebruik op te vragen.

top -bn1: Om een snapshot van actieve processen te krijgen, waaruit CPU-gebruik wordt afgeleid.

De tekstuele output van deze commando's wordt vervolgens door PHP-functies geparsed en omgezet in een gestructureerd JSON-antwoord.