pipeline {
    agent {
        docker { image 'jenkins-custom' }
    }
    
    environment {
        FTP_SERVER = 'ftp://10.30.212.32:21'
        FTP_USER = 'TEST'
        FTP_PASSWORD = '123456'
        MYSQL_SERVER = 'mysql-db'
        MYSQL_USER = 'user_proyecto'
        MYSQL_PASSWORD = 'PassUser123'
        MYSQL_DATABASE = 'proyecto_db'
    }
    
    stages {
        
        stage('Descargar Código del Servidor FTP') {
            steps {
                script {
                    sh """
                    curl -u $FTP_USER:$FTP_PASSWORD $FTP_SERVER/ruta/codigo.zip -o codigo.zip
                    unzip codigo.zip -d ./codigo_fuente
                    """
                }
            }
        }
        
        stage('Analizar Estructura de Base de Datos') {
            steps {
                script {
                    sh """
                    mysql -h $MYSQL_SERVER -u $MYSQL_USER -p$MYSQL_PASSWORD -e "DESCRIBE nombre_de_tu_tabla;"
                    """
                }
            }
        }

        stage('Validación de Vulnerabilidades SQL') {
            steps {
                script {
                    sh """
                    echo "SQL Injection Test Placeholder"
                    """
                }
            }
        }

        stage('Análisis Estático con SonarQube') {
            steps {
                script {
                    withSonarQubeEnv('SonarQube') {
                        sh """
                        sonar-scanner -Dsonar.projectKey=proyecto \
                                      -Dsonar.sources=./codigo_fuente \
                                      -Dsonar.host.url=http://sonarqube:9000 \
                                      -Dsonar.login=tu_token_sonarqube
                        """
                    }
                }
            }
        }

        stage('Revisión de Análisis Estático') {
            steps {
                script {
                    def qualityGate = waitForQualityGate()
                    if (qualityGate.status != 'OK') {
                        error 'Fallo en el análisis de SonarQube, reiniciando en 5 minutos.'
                        sleep 300
                    }
                }
            }
        }

        stage('Despliegue de Código en Servidor Web') {
            steps {
                script {
                    sh """
                    scp -r ./codigo_fuente/* usuario@webserver:/ruta/despliegue
                    """
                }
            }
        }
    }
}
