import paho.mqtt.client as mqtt
import sys
import mysql.connector

# Abrir conexión con bases de datos
try:
    db = mysql.connector.connect(host='localhost', database='mqtt', user='root', password='') 
    # Hay que tener cuidado con la password, pues en algunos sistemas puede ser distinto.
    # Por ejemplo, al utilizar 'xampp', la password es '' (vacío) y al utilizar MAMP, la password es 'root
except Exception as e:
    print("No se pudo conectar con la base de datos.")
    print(e)
    sys.exit()

# Preparando cursor
cursor = db.cursor()

# The callback for when the client receives a CONNACK response from the server.
def on_connect(client, userdata, flags, rc):
    print("Conectado al broker MQTT. - Código de resultado: " + str(rc))
    # Para ver qué significa cada código de resultado, está la documentación de MQTT:
    # https://docs.oasis-open.org/mqtt/mqtt/v5.0/os/mqtt-v5.0-os.html#_Toc3901074
    # Punto 3.2.2.2 

    # Subscribing in on_connect() means that if we lose the connection and reconnect, then subscriptions will be renewed.
    client.subscribe("Datco/#")

# The callback for when a PUBLISH message is received from the server.
def on_message(client, userdata, msg):
    print(msg.topic + ": " + str(msg.payload.decode("utf-8"))) # Destacar que no se puede enviar solamente msg.payload porque se muestra en bytes

    try:
        sql = """INSERT INTO `mqtt`.`tabla` (`id`,`topic`,`mensaje`,`fecha`) VALUES (%s,%s,%s,%s)"""
        values = (None, msg.topic, str(msg.payload.decode("utf-8")), None)
        cursor.execute(sql,values)
        db.commit()
        print("Datos guardados en bd correctamente.")

    except mysql.connector.Error as error:
        db.rollback()
        print("Error al intentar enviar datos a bd {}".format(error))

client = mqtt.Client()
client.on_connect = on_connect
client.on_message = on_message

try:
    client.connect("190.110.108.59", 1883, 60)
except:
    print("No se pudo conectar con el broker MQTT")
    db.close()
    sys.exit()

client.username_pw_set("admin","Datco_admin") # Tener ojo con esto, puede estar mal

try:
    client.loop_forever()
except KeyboardInterrupt: # Presionar Ctrl + C para salir
    print("Cerrando...")
    db.close()