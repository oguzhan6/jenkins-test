pipeline {
    agent any

    stages {
        stage('Build') {
            steps {
                script {
                    dir('jenkins') {
                        sh 'docker-compose build'
                    }
                }
            }
        }
        stage('Start Services') {
            steps {
                script {
                    dir('jenkins') {
                        sh 'docker-compose up -d'
                    }
                }
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
            script {
                dir('jenkins') {
                    sh 'docker-compose down'
                }
            }
        }
    }
}
