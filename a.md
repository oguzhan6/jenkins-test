# Projectdocumentatie: Full-Stack Webapplicatie

## 1. Data Flow Design

### Architectuur Diagram
Dit diagram visualiseert hoe de containers met elkaar communiceren en hoe de data stroomt voor zowel de webapplicatie als de monitoring.

```mermaid
graph TD
    %% Definieer de Gebruiker
    User((Gebruiker / Browser))

    %% Host Systeem
    Host[Docker Host / Socket]

    %% Groepering Applicatie
    subgraph AppStack [Applicatie Stack]
        direction TB
        PHP[PHP/Apache Container<br/>Port: 8085]
        MySQL[(MySQL Container<br/>Port: 3306)]
    end

    %% Groepering Monitoring
    subgraph MonStack [Monitoring Stack]
        direction TB
        cAdvisor[cAdvisor Container]
        Prometheus[Prometheus Container<br/>Port: 9090]
        Grafana[Grafana Container<br/>Port: 3000]
    end

    %% Data Flows - Applicatie
    User -- "1. HTTP Request (Website)" --> PHP
    PHP -- "2. SQL Queries (PDO)" --> MySQL

    %% Data Flows - Monitoring Verzameling
    cAdvisor -. "Leest Metrics" .-> Host
    Prometheus -- "3. Scrapes /metrics" --> cAdvisor

    %% Data Flows - Visualisatie
    User -- "4. View Dashboard" --> Grafana
    Grafana -- "5. PromQL Query" --> Prometheus

    %% Styling voor duidelijkheid
    style PHP fill:#e1f5fe,stroke:#01579b
    style MySQL fill:#e1f5fe,stroke:#01579b
    style cAdvisor fill:#fff3e0,stroke:#e65100
    style Prometheus fill:#fff3e0,stroke:#e65100
    style Grafana fill:#fff3e0,stroke:#e65100