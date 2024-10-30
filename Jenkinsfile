pipeline {
    agent {
        docker { image 'jenkins/jenkins:lts' } // Usa el contenedor de Jenkins con herramientas instaladas
    }
    
    environment {
        FTP_SERVER = 'ftp://tuserverftp'      // Dirección del servidor FTP
        FTP_USER = 'usuarioftp'               // Usuario del servidor FTP
        FTP_PASSWORD = 'passwordftp'          // Contraseña del servidor FTP
        MYSQL_SERVER = 'direccion_mysql'      // Dirección del servidor MySQL
        MYSQL_USER = 'usuariomysql'           // Usuario de la base de datos
        MYSQL_PASSWORD = 'passwordmysql'      // Contraseña de la base de datos
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
                    # Aquí podrías usar una herramienta como sqlmap para probar inyecciones SQL
                    # sqlmap -u "mysql://$MYSQL_SERVER/nombre_de_tu_tabla" --dbms=mysql --batch
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
                    def qualityGate = waitForQualityGate() // Revisa los resultados de SonarQube
                    if (qualityGate.status != 'OK') {
                        error 'Fallo en el análisis de SonarQube, reiniciando en 5 minutos.'
                        sleep 300 // Espera de 5 minutos
                    }
                }
            }
        }

        stage('Análisis Dinámico con OWASP ZAP') {
            steps {
                script {
                    sh """
                    # Ejecuta un análisis de seguridad dinámico con OWASP ZAP
                    zap-cli start
                    zap-cli open-url http://tuappweb.com
                    zap-cli spider http://tuappweb.com
                    zap-cli active-scan http://tuappweb.com
                    zap-cli report -o owasp_zap_report.html
                    zap-cli stop
                    """
                }
            }
        }

        stage('Revisión de Análisis Dinámico') {
            steps {
                script {
                    def vulnerabilitiesFound = readFile('owasp_zap_report.html').contains('High')
                    if (vulnerabilitiesFound) {
                        error 'Vulnerabilidades encontradas en el análisis dinámico. Reiniciando el pipeline.'
                    }
                }
            }
        }

        stage('Despliegue de Código en Servidor Web') {
            steps {
                script {
                    sh """
                    # Aquí agrega el script para desplegar el código en el servidor web
                    scp -r ./codigo_fuente/* usuario@webserver:/ruta/despliegue
                    """
                }
            }
        }
    }
}
