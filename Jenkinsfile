pipeline {
    agent any

    environment {
        SONARQUBE_SERVER = 'SonarQube'
        SONAR_AUTH_TOKEN = credentials('sonarqube-token')
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
                        /opt/sonar-scanner/bin/sonar-scanner \
                        -Dsonar.projectKey=testPipeLine \
                        -Dsonar.sources=. \
                        -Dsonar.php.version=8.0 \
                        -Dsonar.host.url=http://10.30.212.61:9000/ \
                        -Dsonar.login=sqa_41a7546fa8c3315ab2274b5bc519d44151b6d80b
                    '''
                }
            }
        }
        stage('Quality Gate') {
            steps {
                timeout(time: 1, unit: 'HOURS') {
                    script {
                        waitForQualityGate()
                    }
                }
            }
        }
        stage('DAST con OWASP ZAP') {
            steps {
                script {
                    // Remove any existing container named 'zap_scan'
                    sh '''
                    docker rm -f zap_scan || true
                    '''

                    // Run OWASP ZAP container
                    sh '''
                    docker run --user root --name zap_scan -v zap_volume:/zap/wrk/ -t ghcr.io/zaproxy/zaproxy:stable \
                    zap-baseline.py -t https://10.30.212.61 \
                    -r reporte_zap.html -I
                    '''

                    // Copy the report from the 'zap_scan' container to Jenkins workspace
                    sh '''
                    docker cp zap_scan:/zap/wrk/reporte_zap.html ./reporte_zap.html
                    '''

                    // Remove the 'zap_scan' container
                    sh '''
                    docker rm zap_scan
                    '''
                }
            }
            post {
                always {
                    archiveArtifacts artifacts: 'reporte_zap.html', fingerprint: true
                }
            }
        }
    }
}
