pipeline {
    agent any

    environment {
        SONARQUBE_SERVER = 'SonarQube'
        SONAR_AUTH_TOKEN = credentials('sonarqube-token') // ID de la credencial SonarQube token en Jenkins
        PATH = "/opt/sonar-scanner/bin:${env.PATH}"
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }
        stage('SonarQube Analysis') {
            steps {
                withSonarQubeEnv("${SONARQUBE_SERVER}") {
                    sh '''
                        sonar-scanner \
                        -Dsonar.projectKey=testPipeLine \
                        -Dsonar.sources=vulnerabilities \
                        -Dsonar.php.version=8.0 \
                        -Dsonar.host.url=${env.SONAR_HOST_URL} \
                        -Dsonar.login=${SONAR_AUTH_TOKEN}
                    '''
                }
            }
        }
        stage('Quality Gate') {
            steps {
                timeout(time: 1, unit: 'HOURS') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }
        stage('Deploy to Web Server') {
            steps {
                sshagent(['webserver_ssh_credentials_id']) {
                    sh '''
                        ssh user@webserver 'cd /ruta/al/deploy && git clone https://tu.repositorio.git || (cd /ruta/al/deploy/tu_proyecto && git pull)'
                    '''
                }
            }
        }
        stage('ZAP Analysis') {
            steps {
                script {
                    docker.image('owasp/zap2docker-stable').inside('--network host') {
                        sh '''
                            zap.sh -daemon -host 127.0.0.1 -port 8090 -config api.disablekey=true &
                            timeout=120
                            while ! curl -s http://127.0.0.1:8090; do
                                sleep 5
                                timeout=$((timeout - 5))
                                if [ $timeout -le 0 ]; then
                                    echo "ZAP no se inició a tiempo"
                                    exit 1
                                fi
                            done
                            zap-full-scan.py -t http://webserver/tu_proyecto -r zap_report.html -I
                            zap.sh -cmd -shutdown
                        '''
                    }
                }
                publishHTML(target: [
                    reportDir: "${env.WORKSPACE}",
                    reportFiles: 'zap_report.html',
                    reportName: 'Reporte ZAP'
                ])
            }
        }
    }
}
