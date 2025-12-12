pipeline {
    agent any

    stages {
        stage('Build') {
            steps {
                sh 'docker compose build'
            }
        }
        stage('Start Services') {
            steps {
                sh 'docker compose up -d'
            }
        }
        stage('Show running containers') {
            steps {
                sh 'docker ps'
            }
        }
    }
    post {
        always {
            echo 'Pipeline finished. Services are left running intentionally.'
        }
    }
}
